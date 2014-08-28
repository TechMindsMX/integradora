<?php
defined('JPATH_PLATFORM') or die;

jimport('joomla.user.user');
jimport('joomla.factory');
jimport('integradora.catalogos');

class getFromTimOne{
	public static function getProyects($userId = null){
		$proyectos = new stdClass;
		$proyectos->id			= 1;
		$proyectos->integradoid	= 1;
		$proyectos->parentId 	= 0;
		$proyectos->status	 	= 0;
		$proyectos->name 		= 'Proyecto 1';
		$proyectos->description 	= "It doesn't matter who we are. What matters is our plan.
		I am the League of Shadows.
		It will be extremely painful... for you
		Behind you, stands a symbol of oppression. Blackgate Prison, where a thousand men have languished under the name of this man: Harvey Dent.
		Search him. Then I will kill you.
		Citizens, take control. Take control of your ...";
		
		$array[] = $proyectos;
		
		$proyectos = new stdClass;
		$proyectos->id			= 2;
		$proyectos->integradoid	= 2;
		$proyectos->parentId 	= 0;
		$proyectos->status	 	= 0;
		$proyectos->name 		= 'Proyecto 2';
		$proyectos->description	= "It doesn't matter who we are. What matters is our plan.
		I am the League of Shadows.
		It will be extremely painful... for you
		Behind you, stands a symbol of oppression. Blackgate Prison, where a thousand men have languished under the name of this man: Harvey Dent.
		Search him. Then I will kill you.
		Citizens, take control. Take control of your ...";
		
		$array[] = $proyectos;
		
		$proyectos = new stdClass;
		$proyectos->id			= 3;
		$proyectos->integradoid	= 1;
		$proyectos->parentId 	= 1;
		$proyectos->status	 	= 0;
		$proyectos->name 		= 'Subproyecto 1';
		$proyectos->description	= "It doesn't matter who we are. What matters is our plan.
		I am the League of Shadows.
		It will be extremely painful... for you
		Behind you, stands a symbol of oppression. Blackgate Prison, where a thousand men have languished under the name of this man: Harvey Dent.
		Search him. Then I will kill you.
		Citizens, take control. Take control of your ...";
		
		$array[] = $proyectos;
		
		$proyectos = new stdClass;
		$proyectos->id			= 4;
		$proyectos->integradoid	= 1;
		$proyectos->parentId 	= 1;
		$proyectos->status	 	= 1;
		$proyectos->name 		= 'Subproyecto 2';
		$proyectos->description	= "It doesn't matter who we are. What matters is our plan.
		I am the League of Shadows.
		It will be extremely painful... for you
		Behind you, stands a symbol of oppression. Blackgate Prison, where a thousand men have languished under the name of this man: Harvey Dent.
		Search him. Then I will kill you.
		Citizens, take control. Take control of your ...";
		
		$array[] = $proyectos;
		
		$proyectos = new stdClass;
		$proyectos->id			= 5;
		$proyectos->integradoid	= 2;
		$proyectos->parentId 	= 2;
		$proyectos->status	 	= 1;
		$proyectos->name 		= 'Subproyecto 1 del proyecto 2';
		$proyectos->description	= "It doesn't matter who we are. What matters is our plan.
		I am the League of Shadows.
		It will be extremely painful... for you
		Behind you, stands a symbol of oppression. Blackgate Prison, where a thousand men have languished under the name of this man: Harvey Dent.
		Search him. Then I will kill you.
		Citizens, take control. Take control of your ...";
		
		$array[] = $proyectos;
		
		foreach ($array as $key => $value) {
			if($userId == $value->integradoid){
				$respuesta[] = $value;
			}
		}
		
		return $respuesta;
	}
	
	public static function getProducts($userId = null){
		$productos 				= new stdClass;
		$productos->id			= 1;
		$productos->integradoid	= 1;
		$productos->productName	= 'Producto A';
		$productos->measure		= 'Litros';
		$productos->price		= '$1000';
		$productos->iva			= '$150';
		$productos->ieps		= '$100';
		$productos->currency	= 'MXN';
		$productos->status		= '0';
		$productos->description = "U Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.";
		
		$array[] = $productos;
		
		$productos 				= new stdClass;
		$productos->id			= 2;
		$productos->integradoid	= 1;
		$productos->productName	= 'Producto B';
		$productos->measure		= 'Litros';
		$productos->price		= '$1000';
		$productos->iva			= '$150';
		$productos->ieps		= '$100';
		$productos->currency	= 'MXN';
		$productos->status		= '1';
		$productos->description = "O Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.";
		
		$array[] = $productos;
		
		$productos 				= new stdClass;
		$productos->id			= 3;
		$productos->integradoid	= 1;
		$productos->productName	= 'Producto C';
		$productos->measure		= 'Litros';
		$productos->price		= '$1000';
		$productos->iva			= '$150';
		$productos->ieps		= '$100';
		$productos->currency	= 'MXN';
		$productos->status		= '0';
		$productos->description = "I Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.";
		
		$array[] = $productos;
		
		$productos 				= new stdClass;
		$productos->id			= 4;
		$productos->integradoid	= 1;
		$productos->productName	= 'Producto D';
		$productos->measure		= 'Litros';
		$productos->price		= '$1000';
		$productos->iva			= '$150';
		$productos->ieps		= '$100';
		$productos->currency	= 'MXN';
		$productos->status		= '1';
		$productos->description = "E Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.";
		
		$array[] = $productos;
		
		$productos 				= new stdClass;
		$productos->id			= 5;
		$productos->integradoid	= 1;
		$productos->productName	= 'Producto E';
		$productos->measure		= 'Litros';
		$productos->price		= '$1000';
		$productos->iva			= '$150';
		$productos->ieps		= '$100';
		$productos->currency	= 'MXN';
		$productos->status		= '0';
		$productos->description = "A Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.";
		
		$array[] = $productos;
		
		$productos 				= new stdClass;
		$productos->id			= 6;
		$productos->integradoid	= 2;
		$productos->productName	= 'Producto F';
		$productos->measure		= 'Litros';
		$productos->price		= '$1000';
		$productos->iva			= '$150';
		$productos->ieps		= '$100';
		$productos->currency	= 'MXN';
		$productos->status		= '0';
		$productos->description = "U Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.";
		
		$array[] = $productos;
		
		$productos 				= new stdClass;
		$productos->id			= 7;
		$productos->integradoid	= 2;
		$productos->productName	= 'Producto G';
		$productos->measure		= 'Litros';
		$productos->price		= '$1000';
		$productos->iva			= '$150';
		$productos->ieps		= '$100';
		$productos->currency	= 'MXN';
		$productos->status		= '0';
		$productos->description = "O Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.";
		
		$array[] = $productos;
		
		$productos 				= new stdClass;
		$productos->id			= 8;
		$productos->integradoid	= 2;
		$productos->productName	= 'Producto H';
		$productos->measure		= 'Litros';
		$productos->price		= '$1000';
		$productos->iva			= '$150';
		$productos->ieps		= '$100';
		$productos->currency	= 'MXN';
		$productos->status		= '1';
		$productos->description = "I Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.";
		
		$array[] = $productos;
		
		$productos 				= new stdClass;
		$productos->id			= 9;
		$productos->integradoid	= 2;
		$productos->productName	= 'Producto I';
		$productos->measure		= 'Litros';
		$productos->price		= '$1000';
		$productos->iva			= '$150';
		$productos->ieps		= '$100';
		$productos->currency	= 'MXN';
		$productos->status		= '1';
		$productos->description = "E Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.";
		
		$array[] = $productos;
		
		$productos 				= new stdClass;
		$productos->id			= 10;
		$productos->integradoid	= 2;
		$productos->productName	= 'Producto 2';
		$productos->measure		= 'Litros';
		$productos->price		= '$1000';
		$productos->iva			= '$150';
		$productos->ieps		= '$100';
		$productos->currency	= 'MXN';
		$productos->status		= '1';
		$productos->description = "A Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.";
		
		$array[] = $productos;
		
		foreach ($array as $key => $value) {
			if($userId == $value->integradoid){
				$respuesta[] = $value;
			}
		}
		
		return $respuesta;
	}

	public static function getIntegradoId($userId = null){
		$db 	= JFactory::getDbo();
		$db		= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		
		if( is_null($userId) ){
			$query->select('*')
		      	  ->from($db->quoteName('#__integrado_users'));
		}else{
			$query->select('*')
		      	  ->from($db->quoteName('#__integrado_users'))
				  ->where($db->quoteName('user_id').' = '.$userId.' AND '.$db->quoteName('integrado_principal').' = 1');
		}
		$db->setQuery($query);
	 
		$results = $db->loadAssoc();

		return $results;
	}
	
	public static function token(){
		$url = MIDDLE.PUERTO.TIMONE.'security/getKey';
		if( !$token = file_get_contents($url) ){
			JFactory::getApplication()->redirect('index.php', 'No se pudo conectar con TIMONE', 'error');
		}
		
		return $token;
	}
	
	public static function getMedidas(){
		$respuesta['litros'] 			= 'litros';
		$respuesta['Metros'] 			= 'Metros';
		$respuesta['Metros Cúbicos'] 	= 'Metros Cúbicos';
		
		return $respuesta;
	}
}
?>