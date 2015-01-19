<?php
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT . '/helpers/mandatos.php';

class MandatosControllerMandatos extends JControllerAdmin {

	private $integradoId;
	private $ruta;

	function route() {

		$vars   = array ( 'ruta' => 'STRING', 'integradoId' => 'INT' );
		$app    = JFactory::getApplication();
		$params = $app->input->getArray( $vars );

		if ( empty( $params['ruta'] ) || ! isset( $params['integradoId'] ) ) {
			$error = new stdClass();
			$error->code = '403-1' ;
			$error->msg  = 'LBL_ERROR';
		} else {
			$this->integradoId = $params['integradoId'];
			$this->ruta = $params['ruta'];
		}

		$rutas = array(
			'default' => JRoute::_('index.php?option=com_mandatos'),
			'list_proyectos' => JRoute::_('index.php?option=com_mandatos&view=proyectoslist'),
            'list_productos' => JRoute::_('index.php?option=com_mandatos&view=productoslist'),
            'list_clientes' => JRoute::_('index.php?option=com_mandatos&view=clienteslist'),
            'list_odc' => JRoute::_('index.php?option=com_mandatos&view=odclist'),
            'list_odd' => JRoute::_('index.php?option=com_mandatos&view=oddlist'),
            'list_odr' => JRoute::_('index.php?option=com_mandatos&view=odrlist'),
            'list_odv' => JRoute::_('index.php?option=com_mandatos&view=odvlist'),
            'list_fv' => JRoute::_('index.php?option=com_mandatos&view=facturalist'),
            'list_mutos' => JRoute::_('index.php?option=com_mandatos&view=mutuoslist'),
            'go_liquidacion' => JRoute::_('index.php?option=com_mandatos&view=solicitudliquidacion'),
            'tx_sin_mandato' => JRoute::_('index.php?option=com_mandatos&view=txsinmandatolist')
		);

		$usuario = new UsuarioIntegradora();
		$valid = $usuario->isValidIntegradoIdOfCurrentUser( $this->integradoId );

		if ( $valid ) {
			$integ = new IntegradoSimple($this->integradoId);
		}

		if ( !isset( $integ ) ) {
			$error = new stdClass();
			$error->code = '403-2' ;
			$error->msg  = 'LBL_ERROR';
		}

		if ( isset( $error ) ) {
			$app->enqueueMessage($error->code.' - '.$error->msg, 'warning');
			$app->redirect($rutas['default']);
		} else {
			$session = JFactory::getSession();
			$session->set('integradoId', $this->integradoId, 'integrado');
			$app->redirect($rutas[$this->ruta]);
		}
	}
}
