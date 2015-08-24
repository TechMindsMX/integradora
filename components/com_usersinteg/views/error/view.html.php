<?php
defined('_JEXEC') or die('Restricted Access');

class UsersintegViewError extends JViewLegacy {

    public $integradora;

    /**
     * @param null $tpl
     *
     * @return mixed|void
     */
    public function display($tpl = null)
    {
        $this->integradora = $this->get('integradoraData');

        parent::display();
    }
}
