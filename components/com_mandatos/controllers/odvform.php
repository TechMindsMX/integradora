<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 18-Feb-15
 * Time: 1:37 PM
 */
defined('_JEXEC') or die('Restricted access');

jimport('integradora.validator');
jimport('integradora.gettimone');
jimport('integradora.notifications');

require_once JPATH_COMPONENT . '/helpers/mandatos.php';

class MandatosControllerOdvform extends JControllerAdmin {

	protected $data;
	protected $app;
	protected $integradoId;

	function saveODV () {
		$session            = JFactory::getSession();
		$this->integradoId  = $session->get( 'integradoId', null, 'integrado' );

		$post       = array(
			'idOrden'       => 'INT',
			'projectId'     => 'STRING',
			'projectId2'    => 'STRING',
			'clientId'      => 'STRING',
			'numOrden'      => 'INT',
			'account'       => 'STRING',
			'paymentMethod' => 'STRING',
			'conditions'    => 'STRING',
			'placeIssue'    => 'STRING',
			'descripcion'   => 'ARRAY',
			'unidad'        => 'ARRAY',
			'producto'      => 'ARRAY',
			'cantidad'      => 'ARRAY',
			'p_unitario'    => 'ARRAY',
			'iva'           => 'ARRAY',
			'ieps'          => 'ARRAY');

		$this->app  = JFactory::getApplication();
		$this->data       = $this->app->input->getArray($post);

		$db	        = JFactory::getDbo();
		$save       = new sendToTimOne();

		$productosArray = array();

		foreach ($this->data as $key => $value) {
			if( gettype($value) === 'array' ){
				foreach ( $value as $indice => $valor ) {
					$productosArray[$indice][$key] = $valor;
				}

				unset($this->data[$key]);
			}
		}

		$this->data['productos'] = json_encode($productosArray);

		$this->data['integradoId'] = $this->integradoId;

		if ( $this->data['idOrden'] === 0 ){
			$query 	= $db->getQuery(true);
			$query->select('UNIX_TIMESTAMP(CURRENT_TIMESTAMP)');

			try {
				$db->setQuery($query);
				$results = $db->loadColumn();
			} catch (Exception $e) {
				var_dump($e->getMessage());
				exit;
			}

			$numOrden = $save->getNextOrderNumber('odv', $this->integradoId);

			$this->data['numOrden'] = $numOrden;
			$this->data['createdDate'] = $results[0];
			$this->data['status'] = 1;
			unset($this->data['idOrden']);

			$save->formatData($this->data);

			$saved = $save->insertDB('ordenes_venta');

			$this->data['id'] = $db->insertid();

		} else {
			$this->data['id'] = JFactory::getApplication()->input->get('idOrden', null, 'INT');
			unset($this->data['idOrden']);

			$save->formatData($this->data);
			$saved = $save->updateDB('ordenes_venta',null,$db->quoteName('id').' = '.$db->quote($this->data['id']));
		}

		if ($saved) {
			$this->sendMail($this->data);
		}

		$url = 'index.php?option=com_mandatos&view=odvpreview&idOrden='.$this->data['id'];

		JFactory::getApplication()->redirect($url);

	}

	public function sendMail($data)
	{
		/*
		 * Notificaciones 6
		 */
		$clientes = new IntegradoSimple($data['clientId']);
		$nameCliente = $clientes->getDisplayName();

		$totalAmount = self::getTotalAmount(json_decode($data['productos']));
		$getCurrUser = new IntegradoSimple($this->integradoId);

		$array = array($getCurrUser->getUserPrincipal()->name, $data['numOrden'], JFactory::getUser()->name, date('d-m-Y'), $totalAmount, $nameCliente);

		$sendEmail = new Send_email();
		$sendEmail->setIntegradoEmailsArray($getCurrUser);

		$reportEmail = $sendEmail->sendNotifications('2', $array);

	}

	public function getTotalAmount($productos){
		$totalAmount = 0;

		foreach ($productos as $producto) {
			if($producto->iva == 1){
				$producto->iva = 0;
			}
			if($producto->iva == 2){
				$producto->iva =11;
			}
			if($producto->iva == 3){
				$producto->iva = 16;
			}

			$total = ($producto->cantidad*$producto->p_unitario);
			$montoIva = $total*($producto->iva/100);
			$montoIeps = $total*($producto->ieps/100);

			$totalAmount = $total+$montoIva+$montoIeps+$totalAmount;
		}

		return $totalAmount;
	}

}