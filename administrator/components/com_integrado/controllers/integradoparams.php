<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.controllerform');
jimport('integradora.integrado');

/**
 *
 */
class IntegradoControllerIntegradoParams extends JControllerForm {

    public $data;
    public $integradoId;
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
        $params->integradoId = $this->integradoId;
        $params->params = $this->data['params'];

        try {
            if((BOOLEAN)$this->data['exist']){
                $query = $db->getQuery(true);

                $conditions = array(
                    $db->quoteName('integradoId') . ' = '.$this->integradoId
                );

                $query->delete($db->quoteName('#__integrado_params'));
                $query->where($conditions);

                $db->setQuery($query);
                $db->execute();

                $query = $db->getQuery(true);
                $conditions = array(
                    $db->quoteName('integradoId') . ' = '.$this->integradoId
                );

                $query->delete($db->quoteName('#__integrado_comisiones'));
                $query->where($conditions);

                $db->setQuery($query);
                $db->execute();
            }
            $db->insertObject('#__integrado_params', $params);

            $comisones = new stdClass();
            foreach ($this->data['comision'] as $value) {
                $comisones->integradoId = $this->integradoId;
                $comisones->comisionId = $value;

                $db->insertObject('#__integrado_comisiones', $comisones);
            }
            $db->transactionCommit();

            $app->enqueueMessage('Datos Almacenados');
            $ruta = 'index.php?option=com_integrado';
        } catch (Exception $e) {
            $app->enqueueMessage('No se pudo Almacenar los datos', 'error');
            $ruta = 'index.php?option=com_integrado&view=integradoparams&layout=edit&id=' . $this->integradoId;
            $db->transactionRollback();
        }

        $app->redirect($ruta);
    }
}


