<?php
defined('_JEXEC') or die('Restricted access');

jimport('integradora.gettimone');
jimport('integradora.validator');
jimport('integradora.notifications');

require_once JPATH_COMPONENT . '/helpers/mandatos.php';

class MandatosControllerProyectosform extends JControllerLegacy {

	protected $integradoId;

	function saveProject() {
		$diccionario = array (
			'name'        => array ( 'alphaNum' => true, 'maxlength' => 100, 'notNull' => true ),
			'description' => array ( 'alphaNum' => true, 'maxlength' => 1000, 'notNull' => true ),
		);
		$this->validateAndSave($diccionario);
	}

	function saveSubProject() {
		$diccionario = array (
			'parentId'    => array ( 'number' => true,   'maxlength' => 10, 'notNull' => true ),
			'name'        => array ( 'alphaNum' => true, 'maxlength' => 100, 'notNull' => true ),
			'description' => array ( 'alphaNum' => true, 'maxlength' => 1000, 'notNull' => true ),
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

		$data 			= JFactory::getApplication()->input->getArray( $campos );

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

			$this->sendEmails( $data );
		} else {
			$save->updateProject( $data, $id_proyecto );
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

	/**
	 * @param $data
	 */
	private function sendEmails( $data ) {
		/*
					 * Envio de correo electronico;
					 */
		$getCurrInteg = new IntegradoSimple( $this->integradoId );
		$array        = array (
			$getCurrInteg->getUserPrincipal()->name,
			$data['name'],
			JFactory::getUser()->name,
			date( 'd-m-Y' )
		);

		$sendEmail = new Send_email(array('ricardolyon@gmail.com', 'liusmagana@gmail.com'));

		$sendEmail->setIntegradoEmailsArray( $getCurrInteg );
		$reportEmail = $sendEmail->sendNotifications( '2', $array );

		return $reportEmail;
	}

}