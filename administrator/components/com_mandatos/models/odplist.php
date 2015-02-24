<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');
jimport('integradora.integrado');
jimport('integradora.gettimone');

class MandatosModelOdplist extends JModelList{
	public function __construct($config = array()){
        $config['filter_fields'] = array(
                'a.integrado_id',
                'a.status'
        );
        parent::__construct($config);
	}

    public function getOdps(){
        $post = array('idOrden' => 'INT');
        $post = (OBJECT) JFactory::getApplication()->input->getArray($post);

        $odps = getFromTimOne::getOrdenesPrestamo($post->idOrden, null);

        return $odps;
    }
}