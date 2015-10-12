<?php
/**
 * Created by PhpStorm.
 * User: lutek
 * Date: 25/05/2015
 * Time: 04:39 PM
 */

namespace Integralib;


class Integrado {

    public $integrado;
    public $integradoraUuid = INTEGRADORA_UUID;

    /**
     * @param \IntegradoSimple $integradoSimple
     */
    public function setInstance(\IntegradoSimple $integradoSimple){
        $this->integrado = $integradoSimple;
    }

    /**
     * @return bool
     */
    public function isIntegradora(){
        return $this->integrado->getId() == $this->integradoraUuid;
    }

    /**
     * @return string
     */
    public function getIntegradoraUuid(){
        return $this->integradoraUuid;
    }

    /**
     *  Set integradora instance
     */
    public function getIntegradora(){
        $this->setInstance(new \IntegradoSimple($this->integradoraUuid));
    }

    /**
     * @return object JUser
     */
    public function getIntegradoraUserData()
    {
        return \JFactory::getUser($this->getIntegradoraJoomlaId());
    }

    /**
     * @return integer Joomla User Id
     */
    public function getIntegradoraJoomlaId()
    {
        $db = \JFactory::getDbo();

        $query = $db->getQuery(true);

        $query->select($db->quoteName('user_id'))
              ->from($db->quoteName('#__integrado_users'))
              ->where($db->quoteName('integradoId') . ' = ' . $db->quote(INTEGRADORA_UUID) . ' AND ' . $db->quoteName('integrado_principal') . ' = 1');
        $db->setQuery($query);

        $integradoraUser = $db->loadResult();

        return $integradoraUser;
    }

    /**
     * Mothod searches RFCs from Integrados excluding the RFCs from personas where pers_juridica is 1
     * @param $rfc
     *
     * @return mixed
     */
    public static function getIntegradoIdByRfc($rfc)
    {
        $db        = \JFactory::getDbo();

        $query = $db->getQuery(true);
        $query->select($db->quoteName('i.integradoId'))
              ->from( $db->quoteName('#__integrado', 'i') )
              ->join('LEFT', $db->quoteName('#__integrado_datos_personales', 'p') . ' ON (' . $db->quoteName('p.integradoId') . ' = ' . $db->quoteName('i.integradoId') .')')
              ->join('LEFT', $db->quoteName('#__integrado_datos_empresa', 'e') . ' ON (' . $db->quoteName('e.integradoId') . ' = ' . $db->quoteName('i.integradoId') .')')
              ->where( '('. $db->quoteName('i.pers_juridica').' != 1 ' .' AND '.$db->quoteName('p.rfc').' = '.$db->quote($rfc) .') OR (' . $db->quoteName('i.pers_juridica').' = 1 ' .' AND '.$db->quoteName('e.rfc').' = '.$db->quote($rfc) . ')' );
        $db->setQuery($query);
        $id = $db->loadResult();

        return $id;
    }


}