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
class MandatosModelProductosform extends JModelItem {
	
	protected $dataModelo;
	
	public function getProducto(){
        $post           = array('integradoId' => 'INT', 'id_producto' => 'INT');
        $app            = JFactory::getApplication();
        $data           = $app->input->getArray($post);
		$currUser		= JFactory::getUser();

        if($currUser->guest){
			$app->redirect('index.php/login');
		}

		if( $data['id_producto'] != 0 ){
			$producto = getFromTimOne::getProducts(null,$data['id_producto']);
            $producto = $producto[0];

            if($producto->status == 1){
				$app->redirect('index.php/component/mandatos/?view=productos', 'El producto esta Deshabilitado', 'warning');
			}
		}else{
			$this->producto = null;
		}
		
		return $producto;
	}
}

