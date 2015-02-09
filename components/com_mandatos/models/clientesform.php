<?php
defined('_JEXEC') or die('Restricted Access');
jimport('joomla.application.component.modelitem');
jimport('integradora.integrado');
jimport('integradora.rutas');
jimport('integradora.catalogos');
jimport('integradora.gettimone');

class MandatosModelClientesform extends JModelItem {
	
	protected $dataModelo;

    public function getCliente( $integradoId ){
        $app			= JFactory::getApplication();
        $currUser		= JFactory::getUser();
        $input   		= JFactory::getApplication()->input;
        $post           = array( 'idCliPro' => 'INT');
        $data			= $input->getArray($post);

        $sesion             = JFactory::getSession();
        $integradoId        = $sesion->get('integradoId', null, 'integrado');

        $idCliPro       = $data['idCliPro'];
        $return         = '';

        if($currUser->guest){
            $app->redirect('index.php?option=com_users&view=login');
        }

        $clientes = getFromTimOne::getClientes($integradoId);

        foreach ($clientes as $key => $value) {
            if($value->id == $idCliPro){
                $return = $value;
            }
        }

        return $return;
	}
	
	public function getCatalogos() {
		$catalogos = new Catalogos;
		
		$catalogos->getNacionalidades();
		$catalogos->getEstados();
		$catalogos->getBancos();
		
		return $catalogos;
	}
}

?>