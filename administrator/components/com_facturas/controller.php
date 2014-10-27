<?php
defined('_JEXEC') or die;

jimport('integradora.validator');
jimport('integradora.integrado');
jimport('integradora.imagenes');
jimport('integradora.gettimone');
jimport('integradora.classDB');

class FacturasController extends JControllerLegacy {
    public function __construct($config = array()) {
        $get            = JFactory::getApplication()->input;
        $params         = array('claveBanco'=>'ALNUM');
        $this->data     = $get->getArray($params);
        $this->document =  $document = JFactory::getDocument();
        parent::__construct($config);
    }

    public function display($cachable = false, $urlparams = false) {
        require_once JPATH_COMPONENT . '/helpers/facturas.php';
        $view = JFactory::getApplication()->input->getCmd('view', 'facturas');
        JFactory::getApplication()->input->set('view', $view);
        parent::display($cachable, $urlparams);
        return $this;
    }

    public function cuentas() {
        $claveBanco = $this->data['claveBanco'];
        $this->document->setMimeEncoding('application/json');
        $cuentas = getFromTimOne::getNumCuenta();

        foreach ($cuentas as $key => $value) {
            if($claveBanco == $value->banco){
                $respuesta[] = $value;
            }
        }

        $response['data'] = $respuesta;

        echo json_encode($response);
    }
}
