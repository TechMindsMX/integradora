<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.controlleradmin');

class FacturasControllerOddform extends JControllerAdmin{
    public function save(){
        $post = array(
            'idOrden'       => 'INT',
            'integradoId'   => 'INT',
            'ordenPagada'   => 'INT',
            'referencia'    => 'STRING',
            'cuenta'        => 'STRING',
            'date'          => 'STRING',
            'amount'        => 'FLOAT'
        );
        $data = JFactory::getApplication()->input->getArray($post);

        var_dump($data);exit;


    }
}