<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('integradora.validator');
jimport('integradora.gettimone');

class FacturasporcobrarControllerFactdata extends JControllerAdmin {

    function updatefact(){
       $id_venta= $_GET['id'];
        $object = new stdClass();
        $object->id = $id_venta;
        $object->status = '14';
        $result = JFactory::getDbo()->updateObject('#__ordenes_venta', $object, 'id');
        var_dump($result);
        return;
    }
}