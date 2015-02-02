<?php
defined('_JEXEC') or die('Restricted access');

jimport('integradora.gettimone');
jimport('integradora.validator');
jimport('integradora.notifications');

require_once JPATH_COMPONENT . '/helpers/mandatos.php';

class MandatosControllerProyectosform extends JControllerLegacy {

	function saveProject() {
		$diccionario = array (
			'name'        => array ( 'tipo' => 'alphaNum', 'length' => 100, 'notNull' => true ),
			'description' => array ( 'tipo' => 'alphaNum', 'length' => 1000, 'notNull' => true ),
		);
		$this->validateAndSave($diccionario);
	}

	function saveSubProject() {
		$diccionario = array (
			'parentId'    => array ( 'tipo' => 'number',   'length' => 10, 'notNull' => true ),
			'name'        => array ( 'tipo' => 'alphaNum', 'length' => 100, 'notNull' => true ),
			'description' => array ( 'tipo' => 'alphaNum', 'length' => 1000, 'notNull' => true ),
		);
		$this->validateAndSave($diccionario);
	}

	function validateAndSave($diccionario) {
		JFactory::getDocument()->setMimeEncoding( 'application/json' );
		$this->integradoId = JFactory::getSession()->get( 'integradoId', null, 'integrado' );

		$campos = array ( 'parentId'    => 'INT',
		                  'name'        => 'STRING',
		                  'description' => 'STRING',
		                  'status'      => 'INT',
		                  'id_proyecto' => 'INT'
		);

		$data = JFactory::getApplication()->input->getArray( $campos );

// validacion

		$validaciones = MandatosHelper::valida( $data, $diccionario );

		// valida que el nombre del proyecto no este duplicado para el integrado
		$validaciones['name'] = MandatosHelper::checkDuplicatedProjectName( $data, $validaciones['name'] );

		foreach ( $validaciones as $key => $check ) {
			if ( is_array( $check ) ) {
				$errores[ $key ] = ' ' . $check['msg'] . $key . ', ';
			}
		}
		if ( isset( $errores ) ) {

			echo json_encode( $validaciones );

			return false;
		}
// fin validacion

		$id_proyecto         = $data['id_proyecto'];
		$data['integradoId'] = $this->integradoId;

		$save = new sendToTimOne();

		unset( $data['id_proyecto'] );
		if ( $id_proyecto == 0 ) {
			$save->saveProject( $data );
		} else {
			$save->updateProject( $data, $id_proyecto );
		}

		if ( isset( $this->integradoId ) ) {
			$integradoSimple = new IntegradoSimple( $this->integradoId );
			$getCurrUser     = new Integrado( $this->integradoId );

			$contenido = JText::sprintf( 'NOTIFICACIONES_2', $integradoSimple->user->username, $data['name'], $getCurrUser->user->username, date( 'd-m-Y' ) );

			$data['titulo'] = JText::_( 'TITULO_2' );
			$data['body']   = $contenido;
			$data['email']  = JFactory::getUser()->email;

			$send = new Send_email();
			$send->notification( $data );
		}

		$sesion = JFactory::getSession();
		$sesion->set('project_name', $data['name'], 'mensajes');

		echo json_encode(array('redirect' => 'index.php?option=com_mandatos&task=proyectosform.redirectUrl&format=raw'));
	}

	public function redirectUrl(){

		$sesion = JFactory::getSession();
		$projectName = $sesion->get('project_name', '', 'mensajes');
		$sesion->clear('mensajes');
		JFactory::getApplication()->redirect('index.php?option=com_mandatos&view=proyectoslist', JText::sprintf('LBL_PROJECT_SAVED', $projectName));

	}

}