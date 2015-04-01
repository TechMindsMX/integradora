<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.controllerform');
jimport('integradora.integrado');

/**
 *
 */
class IntegradoControllerIntegradoParams extends JControllerForm {

    protected $data;
    protected $integradoId;
    private $tabla_db;

    function __construct( ) {
        $this->data = JFactory::getApplication()->input->getArray();
        $this->tabla_db = 'integrado_params';
        $this->save = new sendToTimOne();

        $this->integradoId = isset($this->data['cid'][0])?$this->data['cid'][0]:$this->data['id'];

        parent::__construct();
    }

    public function save(){
        $db  = JFactory::getDbo();
        $app = JFactory::getApplication();

        $db->transactionStart();

        $params = new stdClass();
        $params->integrado_id = $this->integradoId;
        $params->params = $this->data['params'];

        var_dump($this->data);exit;

        try{
            $db->insertObject('#__integrado_params',$params);

            $comisones = new stdClass();
            foreach ($this->data['comision'] as $value) {
                $comisones->integradoId = $this->integradoId;
                $comisones->comisionId = $value;

                $db->insertObject('#__integrado_comisiones', $comisones);
            }
            $db->transactionCommit();

            $app->enqueueMessage('Datos Almacenados', 'message');
            $ruta = 'index.php?option=com_integrado';
        }catch (Exception $e){
            $app->enqueueMessage('No se pudo Almacenar los datos');
            $ruta = 'index.php?option=com_integrado&view=integradoparams&layout=edit&id='.$this->integradoId;
            $db->transactionRollback();
        }

        $app->redirect($ruta);
    }
}


