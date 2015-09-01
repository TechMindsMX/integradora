<?php
use Integralib\OdCompra;
use Integralib\OdVenta;
use Integralib\OrdenFn;

defined('_JEXEC') or die('Restricted access');

jimport('integradora.gettimone');
jimport('integradora.validator');
jimport('integradora.notifications');
jimport('html2pdf.reportecontabilidad');
require_once JPATH_COMPONENT . '/helpers/mandatos.php';

class MandatosControllerOdcform extends JControllerLegacy {

    public $permisos;

    public function __construct(){

        $this->app          = JFactory::getApplication();
        $this->inputVars    = $this->app->input;
        $post               = array('idOrden'        => 'INT',
                                    'numOrden'       => 'INT',
                                    'proyecto'       => 'STRING',
                                    'proveedor'      => 'STRING',
                                    'paymentDate'    => 'STRING',
                                    'paymentMethod'  => 'STRING',
                                    'totalAmount'    => 'STRING',
                                    'urlXML'         => 'STRING',
                                    'urlPDF'         => 'STRING',
                                    'observaciones'  => 'STRING',
                                    'bankId'         => 'INT');

        $this->parametros   = $this->inputVars->getArray($post);

        // TODO: validación del xml que se sube en la plataforma, ACTIVAR
        try {
//		    Factura::validateXml( JPATH_ROOT.DIRECTORY_SEPARATOR.$this->parametros->urlXML );

        } catch (Exception $e) {
            $this->app->enqueueMessage(JText::_($e->getMessage()), 'error');
            $this->app->redirect('index.php?option=com_mandatos&view=odcform');
        }

        $session            = JFactory::getSession();
        $this->integradoId  = $session->get( 'integradoId', null, 'integrado' );

        parent::__construct();
    }

    function saveODC() {
        $db = JFactory::getDbo();
        JFactory::getDocument()->setMimeEncoding('application/json');
        $datos = $this->parametros;
        $save  = new sendToTimOne();
        $date  = new DateTime($datos['paymentDate']);
        $id    = $datos['idOrden'];

        $datos['paymentDate'] = $date->getTimestamp();

        $this->permisos  = MandatosHelper::checkPermisos(__CLASS__, $this->integradoId);

        if($this->permisos['canEdit']) {
            // acciones cuando tiene permisos para autorizar
            $this->app->enqueueMessage('aqui enviamos a timone la autorizacion y redireccion con mensaje');
        } else {
            // acciones cuando NO tiene permisos para autorizar
            $this->app->redirect('index.php?option=com_mandatos&view=odclist', JText::_('LBL_DOES_NOT_HAVE_PERMISSIONS'), 'error');
        }

        try {
            $db->transactionStart();
            if ($id === 0) {
                unset($datos['idOrden']);
                $datos['createdDate'] = time();
                $datos['numOrden'] = $save->getNextOrderNumber('odc', $this->integradoId);
                $datos['status'] = 1;
                $datos['integradoId'] = $this->integradoId;

                $save->formatData($datos);
                $salvado = $save->insertDB('ordenes_compra');

                $id = $db->insertid();

                $this->createOpossingOdv(new OdCompra(null, $id));
            } else {
                unset($datos['idOrden']);
                $save->formatData($datos);
                $salvado = $save->updateDB('ordenes_compra', null, 'numOrden = ' . $datos['numOrden']);

                $this->updateOpossingOdv(new OdCompra(null, $id));
            }

            if ($salvado) {
                $sesion = JFactory::getSession();
                $sesion->set('msg', 'Datos Almacenados', 'odcCorrecta');

                $this->sendNotifications($datos);

                $respuesta = array(
                    'urlRedireccion' => 'index.php?option=com_mandatos&view=odcpreview&idOrden=' . $id . '&success=true',
                    'redireccion' => true
                );

//                $createPDF = new reportecontabilidad();
//                $createPDF->createPDF($id, 'odc');

            } else {
                $respuesta = array('redireccion' => false);
            }

            $respuesta['idOrden'] = $id;

            $db->transactionCommit();

            echo json_encode($respuesta);
        }catch (Exception $e){
            $db->transactionRollback();

            $this->app->enqueueMessage('No Fue posible Guardar');
            $respuesta = array(
                'urlRedireccion' => 'index.php?option=com_mandatos&view=odcform',
                'redireccion' => true
            );

            echo json_decode($respuesta);
        }
    }

    function valida(){
        $validacion = new validador();
        $document = JFactory::getDocument();

        $parametros = $this->parametros;

        $diccionario = array(
            'integradoId'   => array('alphaNumber' => true,  'maxlength' => 32),
            'numOrden'      => array('number'      => true,  'maxlength' => 10),
            'proveedor'     => array('alphaNumber' => true,  'maxlength' => 32, 'required' => true),
            'proyecto'      => array('number'      => true,  'maxlength' => 10, 'required' => true),
            'paymentDate'   => array('date'        => true,  'maxlength' => 10, 'required' => true),
            'paymentMethod' => array('number'      => true,  'maxlength' => 10),
            'bankId'        => array('number'      => true,  'required'  => true),
            'observaciones' => array('text'        => true,  'maxlength' => 100));

        $respuesta = $validacion->procesamiento($parametros,$diccionario);

        $respuesta['proveedor'] = empty($parametros['proveedor']) ? array('success'=>false,'msg'=>'Seleccione el proveedor') : $respuesta['proveedor'];

        $document->setMimeEncoding('application/json');
        echo json_encode($respuesta);
    }

    private function sendNotifications($datos)
    {
        $info = array();
        /*
         * NOTIFICACIONES 11
         */

        $nameProveedor = $this->getNameProveedor();
        $getIntegradoSimple = new IntegradoSimple($this->integradoId);

        $arrayTitle = array($datos['numOrden']);
        $array = array(
            $getIntegradoSimple->getDisplayName(),
            $datos['numOrden'],
            JFactory::getUser()->name,
            date('d-m-Y'),
            '$'.number_format($this->parametros['totalAmount'], 2),
            strtoupper($nameProveedor));

        $send = new Send_email();
        $send->setIntegradoEmailsArray($getIntegradoSimple);

        $info[] = $send->sendNotifications('11', $array, $arrayTitle);

        /*
         * Notificaciones 13
         */
        $arrayTitleAdmin = array($getIntegradoSimple->getDisplayName(),$datos['numOrden']);

        $send->setAdminEmails();
        $info[] = $send->sendNotifications('13', $array, $arrayTitleAdmin);
    }

    /**
     * @return $nameProveedor
     */
    private function getNameProveedor()
    {
        $proveedores = getFromTimOne::getClientes($this->integradoId, 1);

        foreach ($proveedores as $key => $value) {
            if ($value->id == $this->parametros['proveedor']) {
                $nameProveedor = $value->corporateName;
            }
        }
        return $nameProveedor;
    }

    private function createOpossingOdv(OdCompra $odCompra){
        if($odCompra->getReceptor()->isIntegrado()){
            $catalogos  = new Catalogos();
            $save       = new sendToTimOne();
            $db         = JFactory::getDbo();
            $xml        = new xml2Array();
            $dataXML    = $xml->manejaXML(file_get_contents(JPATH_ROOT.DIRECTORY_SEPARATOR.$odCompra->urlXML));

            $odv = new OdVenta();
            $odv->integradoId   = $odCompra->getReceptor()->id;
            $odv->numOrden      = $save->getNextOrderNumber('odv', $odCompra->getReceptor()->id);
            $odv->projectId     = $odCompra->proyecto->id_proyecto;
            $odv->projectId2    = isset($odCompra->subproyecto->id_proyecto) ? $odCompra->subproyecto->id_proyecto : 0;
            $odv->clientId      = $odCompra->getEmisor()->id;
            $odv->account       = $odCompra->dataBank[0]->datosBan_id;
            $odv->paymentMethod = $odCompra->paymentMethod->id;
            $odv->conditions    = 2;
            $odv->placeIssue    = isset($dataXML->emisor['children'][1]['attrs']['ESTADO']) ? $catalogos->getStateIdByName($dataXML->emisor['children'][1]['attrs']['ESTADO']) : $dataXML->emisor['children'][0]['attrs']['ESTADO'];
            $odv->setStatus(3);

            foreach ($dataXML->conceptos as $concepto) {
                foreach ($concepto as $key => $value) {
                    switch($key){
                        case 'DESCRIPCION':
                            $detalle['descripcion'] = $value;
                            $detalle['producto'] = $value;
                            break;
                        case 'UNIDAD':
                            $detalle['unidad'] = $value;
                            break;
                        case 'CANTIDAD':
                            $detalle['cantidad'] = $value;
                            break;
                        case 'VALORUNITARIO':
                            $detalle['p_unitario'] = $value;
                            break;
                    }
                    $detalle['iva']  = $dataXML->impuestos->iva->tasa == 16 ? 3:0;
                    $detalle['ieps'] = isset($dataXML->impuestos->ieps->tasa) ? $dataXML->impuestos->ieps->tasa : 0;
                }
                $productos[] = $detalle;
            }

            $odv->setProductos(json_encode($productos));
            $odv->setCreatedDate(time());
            $odv->setTotalAmount(null);
            $odv->paymentDate = null;
            $odv->urlXML      = $odCompra->urlXML;

            $db->insertObject('#__ordenes_venta', $odv);

            $relation = new stdClass();
            $relation->id_odc = $odCompra->getId();
            $relation->id_odv = $db->insertid();

            $db->insertObject('#__ordenes_odv_odc_relation', $relation);

            $logdata = implode(' | ',array(JFactory::getUser()->id, JFactory::getSession()->get('integradoId', null, 'integrado'), __METHOD__, json_encode( array($this->parametros) ) ) );
            JLog::add($logdata, JLog::DEBUG, 'bitacora');
        }
    }

    private function updateOpossingOdv(OdCompra $odCompra){
        if($odCompra->getReceptor()->isIntegrado()){
            $idOdvRelated = OrdenFn::getRelatedOdvIdFromOdcId($odCompra->getId());
            $catalogos  = new Catalogos();
            $save       = new sendToTimOne();
            $db         = JFactory::getDbo();
            $xml        = new xml2Array();
            $dataXML    = $xml->manejaXML(file_get_contents(JPATH_ROOT.DIRECTORY_SEPARATOR.$odCompra->urlXML));

            $odv = new OdVenta();
            $odv->setId($idOdvRelated);
            $odv->integradoId   = $odCompra->getReceptor()->id;
            $odv->projectId     = $odCompra->proyecto->id_proyecto;
            $odv->projectId2    = isset($odCompra->subproyecto->id_proyecto) ? $odCompra->subproyecto->id_proyecto : 0;
            $odv->clientId      = $odCompra->getEmisor()->id;
            $odv->account       = $odCompra->dataBank[0]->datosBan_id;
            $odv->paymentMethod = $odCompra->paymentMethod->id;
            $odv->conditions    = 2;
            $odv->placeIssue    = isset($dataXML->emisor['children'][1]['attrs']['ESTADO']) ? $catalogos->getStateIdByName($dataXML->emisor['children'][1]['attrs']['ESTADO']) : $dataXML->emisor['children'][0]['attrs']['ESTADO'];
            $odv->setStatus(3);

            foreach ($dataXML->conceptos as $concepto) {
                foreach ($concepto as $key => $value) {
                    switch($key){
                        case 'DESCRIPCION':
                            $detalle['descripcion'] = $value;
                            $detalle['producto'] = $value;
                            break;
                        case 'UNIDAD':
                            $detalle['unidad'] = $value;
                            break;
                        case 'CANTIDAD':
                            $detalle['cantidad'] = $value;
                            break;
                        case 'VALORUNITARIO':
                            $detalle['p_unitario'] = $value;
                            break;
                    }
                    $detalle['iva']  = $dataXML->impuestos->iva->tasa == 16 ? 3:0;
                    $detalle['ieps'] = isset($dataXML->impuestos->ieps->tasa) ? $dataXML->impuestos->ieps->tasa : 0;
                }
                $productos[] = $detalle;
            }

            $odv->setProductos(json_encode($productos));
            $odv->setCreatedDate(time());
            $odv->setTotalAmount(null);
            $odv->paymentDate = null;
            $odv->urlXML      = $odCompra->urlXML;

            $db->updateObject('#__ordenes_venta', $odv, 'id');
        }
    }
}
