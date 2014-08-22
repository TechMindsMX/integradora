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
}
?>