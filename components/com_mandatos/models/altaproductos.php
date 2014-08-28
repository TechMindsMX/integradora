<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.integrado');
jimport('integradora.rutas');
jimport('integradora.catalogos');
jimport('integradora.gettimone');

/**
 * Modelo de datos para Alta de Productos
 */
class MandatosModelAltaproductos extends JModelItem {
	
	protected $dataModelo;
	
	public function getProducto(){
		$app			= JFactory::getApplication();
		$currUser		= JFactory::getUser();
		$input 			= JFactory::getApplication()->input;
		$data			= $input->getArray();
		$integrado_id	= getFromTimOne::getIntegradoId($currUser->id);
		
		if($data['prodId'] == ''){
			unset($data['prodId']);
		}
		
		if($currUser->guest){
			$app->redirect('index.php/login');
		}
		
		if( isset($data['prodId']) ){
			$allproducts = getFromTimOne::getProducts($integrado_id['integrado_id']);
			
			foreach ($allproducts as $key => $value) {
				if($value->id == $data['prodId']){
					$this->producto = $value; 
				}
			}

			if($this->producto->status == 1){
				$app->redirect('index.php/component/mandatos/?view=productos', 'El producto esta Deshabilitado', 'warning');
			}
		}else{
			$this->producto = null;
		}
		return $this->producto;
	}
}

