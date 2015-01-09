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

    function authorize() {
        $session                         = JFactory::getSession();
        $post                            = array('idOrden' => 'INT');
        $this->app 			             = JFactory::getApplication();
        $this->parametros	             = $this->app->input->getArray($post);
        $this->parametros['integradoId'] = $session->get('integradoId', null, 'integrado');
        $this->permisos                  = MandatosHelper::checkPermisos(__CLASS__, $this->parametros['integradoId']);
        $this->integradoId               = JFactory::getSession()->get('integradoId', null,'integrado');
        $this->integradoId               = isset($this->integradoId) ? $this->integradoId : $this->parametros['integradoId'];

        $redirectUrl ='index.php?option=com_mandatos&view=mutuoslist';

        if($this->permisos['canAuth']) {
            // acciones cuando tiene permisos para autorizar
            $user = JFactory::getUser();
            $save = new sendToTimOne();

            $this->parametros['userId']   = (INT)$user->id;
            $this->parametros['authDate'] = time();
            unset($this->parametros['integradoId']);

            $save->formatData($this->parametros);

            $auths = getFromTimOne::getOrdenAuths($this->parametros['idOrden'],'mutuo_auth');

            $check = getFromTimOne::checkUserAuth($auths);

            if($check){
                $this->app->redirect($redirectUrl, JText::_('LBL_USER_AUTHORIZED'), 'error');
            }

            $resultado = $save->insertDB('auth_mutuo');

            if($resultado) {
                // autorización guardada
                $statusChange = $save->changeOrderStatus($this->parametros['idOrden'], 'mutuo', '5');
                if ($statusChange){
                    $this->app->enqueueMessage(JText::_('ORDER_STATUS_CHANGED'));
                }

                $odps = $this->generateODP($this->parametros['idOrden'], JFactory::getUser()->id);

                if($odps){
                    $msgOdps = JText::_('LBL_ODPS_GENERATED');
                    $this->app->redirect($redirectUrl, JText::_('LBL_ORDER_AUTHORIZED').'<br /> '.$msgOdps);
                }else{
                    $msgOdps = JText::_('LBL_ODPS_NO_GENERATED');
                    $this->app->redirect($redirectUrl, JText::_('LBL_ORDER_NOT_AUTHORIZED'), 'error');
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
        $jsontabla = json_decode($mutuo->jsonTabla);
        $save      = new sendToTimOne();

        if( isset($jsontabla->amortizacion_capital_fijo) ){
            $tabla = $jsontabla->amortizacion_capital_fijo;
        }else{
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

        array_unshift($tabla,$elemento0);

        foreach ($tabla as $key => $objeto) {
            $odp           = new stdClass();
            $fecha         = new DateTime('now', $timezone);

            $odp->idMutuo           = $idMutuo;
            $odp->numOrden          = $idMutuo.'-'.($key);
            $odp->fecha_elaboracion = $fecha->getTimestamp();
            $fechaDeposito          = $this->calcFechaDeposito($fecha, $mutuo->paymentPeriod, $key);
            $odp->fecha_deposito    = $fechaDeposito->getTimestamp();
            $odp->tasa              = $jsontabla->tasa_periodo;
            $odp->tipo_movimiento   = 'Integrado a Integrado';
            $odp->acreedor          = $mutuo->integradoAcredor->nombre;
            $odp->a_rfc             = $mutuo->integradoAcredor->rfc;
            $odp->deudor            = $mutuo->integradoDeudor->nombre;
            $odp->d_rfc             = $mutuo->integradoDeudor->rfc;
            $odp->capital           = $objeto->cuota;
            $odp->intereses         = $objeto->intereses;
            $odp->iva_intereses     = $objeto->iva;
            $odp->status            = 1;

            $save->formatData($odp);

            $saved = $save->insertDB('ordenes_prestamo');

            if(!$saved){
                //Si existe un error al generar la ODP se eliminan todas las odps creadas asi como las autorizaciones y se regresa al status 3
                $save->deleteDB('ordenes_prestamo','idMutuo='.$idMutuo);
                $save->changeOrderStatus($idMutuo,'mutuo','3');
                $save->deleteDB('auth_mutuo','idOrden = '.$idMutuo.' && userId = '.$userId);

                $resultado = false;
                break;
            }else{
                $resultado = true;
            }
        }
        return $resultado;
    }

    public function calcFechaDeposito($fechaAutorizacionMutuo, $periodoPagos, $orderKey){
        $catalogos = new Catalogos();
        $catalogoPeriodos = $catalogos->getTiposPeriodos();

        $strInterval = 'P'.$catalogoPeriodos[$periodoPagos]->multiplicador*$orderKey.$catalogoPeriodos[$periodoPagos]->nombreCiclo;
        $interval = new DateInterval($strInterval);
        $fechaAutorizacionMutuo->add($interval);

        return $fechaAutorizacionMutuo;
    }
}
