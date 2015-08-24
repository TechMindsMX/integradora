<?php
defined('_JEXEC') or die('Restricted Access');

jimport('integradora.gettimone');

class UsersintegModelError extends JModelLegacy {

    public function getIntegradoraData()
    {
        $integradora = new \Integralib\Integrado();

        return new IntegradoSimple($integradora->getIntegradoraUuid());
    }
}