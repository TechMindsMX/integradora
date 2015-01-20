<?php
defined('_JEXEC') or die('Restricted access');

jimport('integradora.gettimone');
jimport('integradora.validator');
jimport('integradora.notifications');

require_once JPATH_COMPONENT . '/helpers/mandatos.php';

class MandatosControllerProyectosform extends JControllerLegacy {

	public function valida() {

		$diccionario = array(
			'parentId'      => array('tipo'=>'number', 'length' => 10),
			'name'          => array('tipo'=>'number', 'length' => 100, 'notNull' => true),
			'description'   => array('tipo'=>'number', 'length' => 1000, 'notNull' => true),
			'status'        => array('tipo'=>'number', 'length' => 10),
			'id_proyecto'   => array('tipo'=>'number', 'length' => 10)
		);

		MandatosHelper::valida($diccionario);
	}
}