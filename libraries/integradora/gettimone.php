<?php
defined('JPATH_PLATFORM') or die;

jimport('joomla.user.user');
jimport('joomla.factory');
jimport('integradora.catalogos');

class getFromTimOne{
	public static function getProyects($userId){
		$proyectos = new stdClass;
		$proyectos->id			= 1;
		$proyectos->user		= $userId;
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
		$proyectos->user		= $userId;
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
		$proyectos->user		= $userId;
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
		$proyectos->user		= $userId;
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
		$proyectos->user		= $userId;
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
		
		return $array;
	}
	
	public static function getProducts($userId){
		$productos = new stdClass;
		$productos->id			= 1;
		$productos->name		= 'Producto 1';
		$productos->medida		= '5 X 5 cm';
		$productos->precio		= '$1000';
		$productos->iva			= '$150';
		$productos->ieps		= '$100';
		$productos->moneda		= 'MXN';
		$productos->status		= '0';
		$productos->description 	= "It doesn't matter who we are. What matters is our plan.
		I am the League of Shadows.
		It will be extremely painful... for you
		Behind you, stands a symbol of oppression. Blackgate Prison, where a thousand men have languished under the name of this man: Harvey Dent.
		Search him. Then I will kill you.
		Citizens, take control. Take control of your ...";
		
		$array[] = $productos;
		
		$productos = new stdClass;
		$productos->id			= 2;
		$productos->name		= 'Producto 2';
		$productos->medida		= '5 X 5 cm';
		$productos->precio		= '$1000';
		$productos->iva			= '$150';
		$productos->ieps		= '$100';
		$productos->moneda		= 'MXN';
		$productos->status		= '1';
		$productos->description 	= "It doesn't matter who we are. What matters is our plan.
		I am the League of Shadows.
		It will be extremely painful... for you
		Behind you, stands a symbol of oppression. Blackgate Prison, where a thousand men have languished under the name of this man: Harvey Dent.
		Search him. Then I will kill you.
		Citizens, take control. Take control of your ...";
		
		$array[] = $productos;
		
		$productos = new stdClass;
		$productos->id			= 3;
		$productos->name		= 'Producto 3';
		$productos->medida		= '5 X 5 cm';
		$productos->precio		= '$1000';
		$productos->iva			= '$150';
		$productos->ieps		= '$100';
		$productos->moneda		= 'MXN';
		$productos->status		= '1';
		$productos->description 	= "It doesn't matter who we are.";
		
		$array[] = $productos;
		
		$productos = new stdClass;
		$productos->id			= 4;
		$productos->name		= 'Producto 4';
		$productos->medida		= '5 X 5 cm';
		$productos->precio		= '$1000';
		$productos->iva			= '$150';
		$productos->ieps		= '$100';
		$productos->moneda		= 'MXN';
		$productos->status		= '0';
		$productos->description 	= "It doesn't matter who we are. What matters is our plan.
		I am the League of Shadows.
		It will be extremely painful... for you
		Behind you, stands a symbol of oppression. Blackgate Prison, where a thousand men have languished under the name of this man: Harvey Dent.
		Search him. Then I will kill you.
		Citizens, take control. Take control of your ...";
		
		$array[] = $productos;
		
		$productos = new stdClass;
		$productos->id			= 5;
		$productos->name		= 'Producto 5';
		$productos->medida		= '5 X 5 cm';
		$productos->precio		= '$1000';
		$productos->iva			= '$150';
		$productos->ieps		= '$100';
		$productos->moneda		= 'MXN';
		$productos->status		= '0';
		$productos->description 	= "It doesn't matter who we are. What matters is our plan.
		I am the League of Shadows.
		It will be extremely painful... for you
		Behind you, stands a symbol of oppression. Blackgate Prison, where a thousand men have languished under the name of this man: Harvey Dent.
		Search him. Then I will kill you.
		Citizens, take control. Take control of your ...";
		
		$array[] = $productos;
		
		$productos = new stdClass;
		$productos->id			= 6;
		$productos->name		= 'Producto 6';
		$productos->medida		= '5 X 5 cm';
		$productos->precio		= '$1000';
		$productos->iva			= '$150';
		$productos->ieps		= '$100';
		$productos->moneda		= 'MXN';
		$productos->status		= '0';
		$productos->description 	= "It doesn't matter who we are. What matters is our plan.
		I am the League of Shadows.
		It will be extremely painful... for you
		Behind you, stands a symbol of oppression. Blackgate Prison, where a thousand men have languished under the name of this man: Harvey Dent.
		Search him. Then I will kill you.
		Citizens, take control. Take control of your ...";
		
		$array[] = $productos;
		
		$productos = new stdClass;
		$productos->id			= 7;
		$productos->name		= 'Producto 7';
		$productos->medida		= '5 X 5 cm';
		$productos->precio		= '$1000';
		$productos->iva			= '$150';
		$productos->ieps		= '$100';
		$productos->moneda		= 'MXN';
		$productos->status		= '0';
		$productos->description 	= "It doesn't matter who we are. What matters is our plan.
		I am the League of Shadows.
		It will be extremely painful... for you
		Behind you, stands a symbol of oppression. Blackgate Prison, where a thousand men have languished under the name of this man: Harvey Dent.
		Search him. Then I will kill you.
		Citizens, take control. Take control of your ...";
		
		$array[] = $productos;
		
		$productos = new stdClass;
		$productos->id			= 8;
		$productos->name		= 'Producto 8';
		$productos->medida		= '5 X 5 cm';
		$productos->precio		= '$1000';
		$productos->iva			= '$150';
		$productos->ieps		= '$100';
		$productos->moneda		= 'MXN';
		$productos->status		= '0';
		$productos->description 	= "It doesn't matter who we are. What matters is our plan.
		I am the League of Shadows.
		It will be extremely painful... for you
		Behind you, stands a symbol of oppression. Blackgate Prison, where a thousand men have languished under the name of this man: Harvey Dent.
		Search him. Then I will kill you.
		Citizens, take control. Take control of your ...";
		
		$array[] = $productos;
		
		$productos = new stdClass;
		$productos->id			= 9;
		$productos->name		= 'Producto 9';
		$productos->medida		= '5 X 5 cm';
		$productos->precio		= '$1000';
		$productos->iva			= '$150';
		$productos->ieps		= '$100';
		$productos->moneda		= 'MXN';
		$productos->status		= '0';
		$productos->description 	= "It doesn't matter who we are. What matters is our plan.
		I am the League of Shadows.
		It will be extremely painful... for you
		Behind you, stands a symbol of oppression. Blackgate Prison, where a thousand men have languished under the name of this man: Harvey Dent.
		Search him. Then I will kill you.
		Citizens, take control. Take control of your ...";
		
		$array[] = $productos;
		
		$productos = new stdClass;
		$productos->id			= 10;
		$productos->name		= 'Producto 10';
		$productos->medida		= '5 X 5 cm';
		$productos->precio		= '$1000';
		$productos->iva			= '$150';
		$productos->ieps		= '$100';
		$productos->moneda		= 'MXN';
		$productos->status		= '0';
		$productos->description 	= "It doesn't matter who we are. What matters is our plan.
		I am the League of Shadows.
		It will be extremely painful... for you
		Behind you, stands a symbol of oppression. Blackgate Prison, where a thousand men have languished under the name of this man: Harvey Dent.
		Search him. Then I will kill you.
		Citizens, take control. Take control of your ...";
		
		$array[] = $productos;
		
		return $array;
	}

	public static function getIntegradoId(){
		$db = JFactory::getDbo();
		
		$db		= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		
		$query->select('*')
		      ->from($db->quoteName('#__integrado_users'));

		$db->setQuery($query);
	 
		$results = $db->loadAssoc();

		return $results;
	}
	
	public static function token(){
		$url = MIDDLE.PUERTO.TIMONE.'security/getKey';
		
		return 'khdasgjhdgfjhdgfdgfjdsgfsd';//file_get_contents($url);
	}
}
?>