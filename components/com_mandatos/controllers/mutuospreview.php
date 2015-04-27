<?php
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT . '/helpers/mandatos.php';
jimport('integradora.gettimone');
jimport('integradora.catalogos');

/**
 * metodo de envio a TimOne
 * @property mixed parametros
 * @property mixed app
 * @property mixed permisos
 * @property mixed integradoId
 */
class MandatosControllerMutuospreview extends JControllerAdmin {

    function __construct(){
        $session                         = JFactory::getSession();
        $post                            = array('idOrden' => 'INT');
        $this->app 			             = JFactory::getApplication();
        $this->parametros	             = $this->app->input->getArray($post);
        $this->parametros['integradoId'] = $session->get('integradoId', null, 'integrado');
        $this->permisos                  = MandatosHelper::checkPermisos(__CLASS__, $this->parametros['integradoId']);
        $this->integradoId               = JFactory::getSession()->get('integradoId', null,'integrado');
        $this->integradoId               = isset($this->integradoId) ? $this->integradoId : $this->parametros['integradoId'];
        $orden                           = getFromTimOne::getMutuos(null, $this->parametros['idOrden']);
        $this->orden                     = $orden[0];
        $this->$redirectUrl ='index.php?option=com_mandatos&view=mutuoslist';

        parent::__construct();
    }

    function authorize() {
        $redirectUrl ='index.php?option=com_mandatos&view=mutuoslist';

        if($this->permisos['canAuth']) {
            // acciones cuando tiene permisos para autorizar
            $user = JFactory::getUser();
            $save = new sendToTimOne();

            $this->parametros['userId']   = (INT)$user->id;
            $this->parametros['authDate'] = time();

            $save->formatData($this->parametros);

            $auths = getFromTimOne::getOrdenAuths($this->parametros['idOrden'],'mutuo_auth');

            $check = getFromTimOne::checkUserAuth($auths);

            if($check){
                $this->app->redirect($redirectUrl, JText::_('LBL_USER_AUTHORIZED'), 'error');
            }

            $this->checkSaldoSuficienteOrRedirectWithError(new IntegradoSimple($this->orden->integradoIdE));

            $resultado = $save->insertDB('auth_mutuo');

            if($resultado) {
                // autorización guardada
                $newStatusId  = 5;
                $statusChange = $save->changeOrderStatus($this->parametros['idOrden'], 'mutuo', $newStatusId);

                if ($statusChange) {
                    $generateOdps = $this->generateODP($this->parametros['idOrden'], JFactory::getUser()->id);

                    if ($generateOdps) {
                        $tx = $this->paymentFirstOdp();

                        if($tx) {
                            $msgOdps = JText::_('LBL_ODPS_GENERATED');
                            $this->app->redirect($redirectUrl, JText::_('LBL_ORDER_AUTHORIZED') . '<br />' . $msgOdps, 'notice');
                        }else{
                            $msgOdps = JText::_('LBL_ODPS_GENERATED');
                            $statusChange = $save->changeOrderStatus($this->parametros['idOrden'], 'mutuo', 3);
                            $this->app->redirect($redirectUrl, JText::_('LBL_ORDER_AUTHORIZED') . '<br />' . $msgOdps, 'notice');
                        }
                    } else {
                        $msgOdps = JText::_('LBL_ODPS_NO_GENERATED');
                        $this->app->redirect($redirectUrl, JText::_('LBL_ORDER_AUTHORIZE_STANDBY').'<br />'.$msgOdps, 'notice');
                    }
                }
            }else{
                $this->app->redirect($redirectUrl, JText::_('LBL_ORDER_NOT_AUTHORIZED'), 'error');
            }
        } else {
            // acciones cuando NO tiene permisos para autorizar
            $this->app->redirect($redirectUrl, JText::_('LBL_DOES_NOT_HAVE_PERMISSIONS'), 'error');
        }
    }

    function generateODP($idMutuo,$userId){
        $timezone  = new DateTimeZone('America/Mexico_City');
        $mutuos    = getFromTimOne::getMutuos(null,$idMutuo);
        $mutuo     = $mutuos[0];

        if($mutuo->status == 5) {
            $jsontabla = json_decode($mutuo->jsonTabla);
            $save = new sendToTimOne();

            if (isset($jsontabla->amortizacion_capital_fijo)) {
                $tabla = $jsontabla->amortizacion_capital_fijo;
            } else {
                $tabla = $jsontabla->amortizacion_cuota_fija;
            }

            $elemento0 = new stdClass();

            $elemento0->periodo = 0;
            $elemento0->inicial = $mutuo->totalAmount;
            $elemento0->cuota = $mutuo->totalAmount;
            $elemento0->intiva = 0;
            $elemento0->intereses = 0;
            $elemento0->iva = 0;
            $elemento0->acapital = $mutuo->totalAmount;
            $elemento0->final = 0;

            array_unshift($tabla, $elemento0);

            foreach ($tabla as $key => $objeto) {
                $odp = new stdClass();
                $fecha = new DateTime('now', $timezone);

                $odp->idMutuo           = $idMutuo;
                $odp->numOrden          = $idMutuo . '-' . ($key);
                $odp->fecha_elaboracion = $fecha->getTimestamp();
                $fechaDeposito          = $this->calcFechaDeposito($fecha, $mutuo->paymentPeriod, $key);
                $odp->fecha_deposito    = $fechaDeposito->getTimestamp();
                $odp->tasa              = $jsontabla->tasa_periodo;
                $odp->tipo_movimiento   = 'Integrado a Integrado';
                $odp->integradoIdA      = $mutuo->integradoIdE;
                $odp->acreedor          = $mutuo->integradoAcredor->nombre;
                $odp->a_rfc             = $mutuo->integradoAcredor->rfc;
                $odp->integradoIdD      = $mutuo->integradoIdR;
                $odp->deudor            = $mutuo->integradoDeudor->nombre;
                $odp->d_rfc             = $mutuo->integradoDeudor->rfc;
                $odp->capital           = $objeto->cuota;
                $odp->intereses         = $objeto->intereses;
                $odp->iva_intereses     = $objeto->iva;
                $odp->status            = 5;

                $save->formatData($odp);

                $saved = $save->insertDB('ordenes_prestamo');

                if (!$saved) {
                    //Si existe un error al generar la ODP se eliminan todas las odps creadas asi como las autorizaciones y se regresa al status 3
                    $save->deleteDB('ordenes_prestamo', 'idMutuo=' . $idMutuo);
                    $save->changeOrderStatus($idMutuo, 'mutuo', '3');
                    $save->deleteDB('auth_mutuo', 'idOrden = ' . $idMutuo . ' && userId = ' . $userId.' && integradoId = '.JFactory::getSession()->get('integradoId',null,'integrado'));

                    $resultado = false;
                    break;
                } else {
                    $resultado = true;
                }
            }
        }elseif($mutuo->status == 3){
            $resultado = false;
        }

        return $resultado;
    }

    public function calcFechaDeposito($fechaAutorizacionMutuo, $periodoPagos, $orderKey){
        $catalogos = new Catalogos();
        $catalogoPeriodos = $catalogos->getTiposPeriodos();

        $strInterval = 'P' . $catalogoPeriodos[$periodoPagos]->multiplicador * $orderKey . $catalogoPeriodos[$periodoPagos]->nombreCiclo;
        $interval = new DateInterval($strInterval);
        $fechaAutorizacionMutuo->add($interval);

        return $fechaAutorizacionMutuo;
    }

    private function paymentFirstOdp(){
        $odpsGenerated     = getFromTimOne::getOrdenesPrestamo($this->parametros['idOrden']);
        $orden             = $odpsGenerated[0];
        $deudor            = new IntegradoSimple($orden->integradoIdD);
        $save              = new sendToTimOne();
        $orden->orderType  = 'odp';

        if( !empty($deudor->usuarios) ) { //operacion de transfer entre integrados
            $txData = new transferFunds($orden, $orden->integradoIdA, $orden->integradoIdD, $orden->capital);
            $txDone = $txData->sendCreateTx();
        }else{
            $txData = new Cashout($orden,$orden->integradoIdA,$orden->integradoIdD,$orden->capital, array('accountId' => $orden->deudorDataBank->datosBan_id));
            $txDone = $txData->sendCreateTx();
        }

        if($txDone){
            $save->updateDB('ordenes_prestamo',array('status = 13'),'id = '.$orden->id);
        }else{
            $save->updateDB('ordenes_prestamo',array('status = 1'),'id = '.$orden->id);
        }

        return $txDone;
    }

    private function checkSaldoSuficienteOrRedirectWithError(IntegradoSimple $integradoSimple){
        $integradoSimple->getTimOneData();
        if ($integradoSimple->timoneData->balance < $this->totalOperacionOdc()) {
            $this->app->redirect($this->$redirectUrl, 'ERROR_SALDO_INSUFICIENTE', 'error');
        }
    }

    private function totalOperacionOdc(){
        $orden = $this->orden;
        $comisiones = getFromTimOne::getComisionesOfIntegrado($orden->integradoIdE);

        $montoComision = 0;
        if (isset($comisiones)) {
            $montoComision = getFromTimOne::calculaComision($orden, 'MUTUO', $this->comisiones);
        }

        $totalOperacion = (float)$orden->totalAmount + (float)$montoComision;

        return $totalOperacion;
    }

    private function sendMail(){

    }
}
