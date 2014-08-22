<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');


class ProyectosModelProyectos extends JModelList
{
    public function getProjects(){
    	var_dump('en el modelo');
    }
}