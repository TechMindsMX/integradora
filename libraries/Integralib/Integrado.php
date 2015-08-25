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

        $query->select($db->quoteName('�d'))
              ->from($db->quoteName('#__integrado_users'))
              ->where($db->quoteName('integradoId') . ' = ' . INTEGRADORA_UUID . ' AND ' . $db->quoteName('integrado_principal') . ' = 1');
        $db->setQuery($query);

        $integradoraUser = $db->loadResult();

        return $integradoraUser;
    }
}