<?php

use Integralib\Txs;

defined('_JEXEC') or die('Restricted Access');

jimport('integradora.integrado');
/**
 * helper class for mandatos component
 */
class MandatosHelper {
	
	function __construct($argument) {
		
	}

	// esta funcion y el objeto que se maneja en las vistas para proyecto y subproyecto deben adaptarse
	public static function getProyectFromId($proyId, $integradoId){
		$proyKeyId = array();

		$proyectos = getFromTimOne::getProyects($integradoId);

		// datos del proyecto y subproyecto involucrrado
		foreach ( $proyectos as $key => $proy) {
			$proyKeyId[$proy->id_proyecto] = $proy;
		}
			
		if(array_key_exists($proyId, $proyKeyId)) {
			$proyecto = $proyKeyId[$proyId];
			
			if($proyecto->parentId > 0) {
				$sub_proyecto	= $proyecto;
				$proyecto		= $proyKeyId[$proyecto->parentId];
			} else {
				$subproyecto 	= null;
			}
		}

		return $proyecto;
	}
	
	public static function checkPermisos($viewClass, $integradoId) {
		$user = JFactory::getUser();
		
		$permisos = Integrado::checkPermisos($viewClass, $user->id, $integradoId);
		
		return $permisos;
	}

	public static function getPrintButton($url)
	{
		return getFromTimOne::generatePrintButton( $url );
	}

    public static function getClientsFromID($clientId, $integradoId){
        $datos = getFromTimOne::getClientes($integradoId);

        foreach ($datos as $key => $value) {
           if($clientId==$value->id){
               $cliente = $value;
           }
        }

        return $cliente;
    }

	public static function valida($input, $diccionario){
		$validacion = new validador();
		$document = JFactory::getDocument();

		$respuesta = $validacion->procesamiento($input, $diccionario);

		$document->setMimeEncoding('application/json');

		return $respuesta;
	}

	public static function checkDuplicatedProjectName( $post, $currentValidations ) {
		$integradoId = JFactory::getSession()->get('integradoId', null, 'integrado');

		$projects = getFromTimOne::getProyects($integradoId);

		foreach ( $projects as $value ) {
			if(strtoupper($value->name) == strtoupper($post['name']) && $value->id_proyecto != $post['id_proyecto']) {
				$validacion['success'] = false;
				$validacion['msg'] = JText::_('ERROR_PROJECT_NAME_DUPLICATED');
			}
		}
		$validacion = isset($validacion) ? $validacion : $currentValidations;

		return $validacion;
	}

	public static function getTXsinMandato( ){
		$sesion             = JFactory::getSession();
		$integradoId        = $sesion->get('integradoId', null, 'integrado');

		$txs = getFromTimOne::getTxIntegradoConSaldo($integradoId);

		$retorno = array();
		foreach ( $txs as $trans ) {
			$trans->balance = self::getTxBalance($trans);

			if($trans->balance > 0) {
				$retorno[] = $trans;
			}
		}

		return $retorno;
	}

	/**
	 * @param $trans
	 * se traen los mandatos a los que esta asosciada la Tx
	 * @return mixed
	 */
	private static function getTxBalance( $trans ) {
		$txs = new Txs();

		return $txs->calculateBalance($trans);
	}

	/**
	 * @return int
     */
	public static function getBlockedBalance(){
		$txs = self::getTXsinMandato();
		$blockedBalance = 0;

		foreach($txs as $value){
			$blockedBalance = $value->balance + $blockedBalance;
		}

		return $blockedBalance;
	}

	/**
	 * @param $integradoId
	 * @return mixed
     */
	public static function getBalance( $integradoId ){
		$integrado = new IntegradoSimple($integradoId);
		$integrado->getTimOneData();

		return $integrado->timoneData->balance;
	}
}
