<?php
use Integralib\OdPrestamo;
use Integralib\OrdenFn;

defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT . '/helpers/mandatos.php';
jimport('integradora.gettimone');
jimport('integradora.catalogos');
jimport('html2pdf.PdfsIntegradora');

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
        $this->redirectUrl ='index.php?option=com_mandatos&view=mutuoslist';

        parent::__construct();
    }

    function authorize() {
        $redirectUrl ='index.php?option=com_mandatos&view=mutuoslist';
        $integradoE = new IntegradoSimple($this->orden->integradoIdE);

        if($this->permisos['canAuth']) {
            // acciones cuando tiene permisos para autorizar
            $user = JFactory::getUser();
            $save = new sendToTimOne();

            $this->parametros['userId']   = (INT)$user->id;
            $this->parametros['authDate'] = time();

            $save->formatData($this->parametros);

            $auths = getFromTimOne::getOrdenAuths($this->parametros['idOrden'],'mutuo_auth');
            $check = false;

            foreach ($auths as $auth) {
                if( $auth->integradoId == $this->parametros['integradoId'] ){
                    if( $auth->userId == $user->id ){
                        $check = true;
                    }
                }
            }

            if($check){
                $this->app->redirect($redirectUrl, JText::_('LBL_USER_AUTHORIZED'), 'error');
            }
            $this->checkSaldoSuficienteOrRedirectWithError($integradoE);
            $db = JFactory::getDbo();

            try{
                $db->transactionStart();

                $mutuo = $this->orden;
                $resultado = $save->insertDB('auth_mutuo');

                $auths       = OrdenFn::getCantidadAutRequeridas( new IntegradoSimple( $mutuo->integradoIdE ), new IntegradoSimple( $mutuo->integradoIdR ) );
                $numAutOrder = getFromTimOne::getOrdenAuths($mutuo->id, 'mutuo_auth');

                if($auths->totales == count($numAutOrder)) {
                    $pagar = true;
                }else{
                    $save->changeOrderStatus($this->parametros['idOrden'],'mutuo',3);
                    $pagar = false;
                }

                $db->transactionCommit();
            }catch (Exception $e){
                $db->transactionRollback();
                $pagar = false;
            }

            if($pagar){
                if($resultado) {
                    // autorización guardada
                    $newStatusId  = 5;
                    $statusChange = $save->changeOrderStatus($this->parametros['idOrden'], 'mutuo', $newStatusId);

                    if ($statusChange) {
                        $generateOdps = $this->generateODP($this->parametros['idOrden'], JFactory::getUser()->id);

                        if ($generateOdps) {

                            $tx = $this->paymentFirstOdp($this->parametros['idOrden']);

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
            }else{
                $html2pdf = new PdfsIntegradora();
                $html2pdf->createPDF($mutuo, 'mutuo');
                // acciones cuando NO tiene permisos para autorizar
                $this->app->redirect($redirectUrl, JText::_('LBL_ORDER_AUTHORIZE_STANDBY'), 'warning');

            }
        } else {
            // acciones cuando NO tiene permisos para autorizar
            $this->app->redirect($redirectUrl, JText::_('LBL_DOES_NOT_HAVE_PERMISSIONS'), 'error');
        }
    }

    /**
     * @param $idMutuo
     * @param $userId
     *
     * @return bool
     */
    function generateODP($idMutuo, $userId) {
        $odp = new OdPrestamo();

        return $odp->generate($idMutuo, $userId);
    }

    public function calcFechaDeposito($fechaAutorizacionMutuo, $periodoPagos, $orderKey){
        $catalogos = new Catalogos();
        $catalogoPeriodos = $catalogos->getTiposPeriodos();

        $strInterval = 'P' . $catalogoPeriodos[$periodoPagos]->multiplicador * $orderKey . $catalogoPeriodos[$periodoPagos]->nombreCiclo;
        $interval = new DateInterval($strInterval);
        $fechaAutorizacionMutuo->add($interval);

        return $fechaAutorizacionMutuo;
    }

    private function paymentFirstOdp($orderId){
            $odp = new OdPrestamo($orderId);

            return $odp->pay();
    }

    private function checkSaldoSuficienteOrRedirectWithError(IntegradoSimple $integradoSimple){
        $integradoSimple->getTimOneData();
        if ($integradoSimple->timoneData->balance < $this->totalOperacionOdc()) {
            $this->app->redirect($this->redirectUrl, 'ERROR_SALDO_INSUFICIENTE', 'error');
        }
    }

    private function totalOperacionOdc(){
        $orden = $this->orden;
        $comisiones = getFromTimOne::getComisionesOfIntegrado($orden->integradoIdE);

        $montoComision = 0;
        if (isset($comisiones)) {
            $montoComision = getFromTimOne::calculaComision($orden, 'MUTUO', $comisiones);
        }

        $totalOperacion = (float)$orden->totalAmount + (float)$montoComision;

        return $totalOperacion;
    }

    private function sendMail(){

    }
}
