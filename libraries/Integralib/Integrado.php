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
     * Mothod searches RFCs from Integrados excluding the RFCs from
     * @param $rfc
     *
     * @return mixed
     */
    public static function getIntegradoIdFromRfc($rfc)
    {
        $db        = \JFactory::getDbo();

        $query = $db->getQuery(true);
        $query->select($db->quoteName('integradoId'))
              ->from( $db->quoteName('#__integrado_datos_personales', 'p') )
              ->join('LEFT', $db->quoteName('#__integrado', 'i') . ' ON (' . $db->quoteName('p.integradoId') . ' = ' . $db->quoteName('i.integradoId') .')')
            ->where($db->quoteName('i.pers_juridica').' = 1 ' .' AND '.$db->quoteName('p.rfc').' = '.$db->quote($rfc) );
        $db->setQuery($query);
        $personales = $db->loadResult();

        $query = $db->getQuery(true);
        $query->select($db->quoteName('integradoId'))
            ->from('#__integrado_datos_empresa')
            ->where($db->quoteName('rfc').' = '.$db->quote($rfc));
        $db->setQuery($query);
        $empresa = $db->loadResult();

        $integradoId = (!is_null($personales)) ? $personales : $empresa;

        return $integradoId;
    }


}