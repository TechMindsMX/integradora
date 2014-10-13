<?php
defined('JPATH_PLATFORM') or die;

jimport('joomla.user.user');
jimport('joomla.factory');
jimport('integradora.catalogos');

class getFromTimOne{
    public static function getProyects($userId = null){
        $respuesta = array();

        $proyectos = new stdClass;
        $proyectos->id			= 1;
        $proyectos->integradoId	= 1;
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
        $proyectos->integradoId	= 1;
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
        $proyectos->integradoId	= 1;
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
        $proyectos->integradoId	= 1;
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
        $proyectos->integradoId	= 1;
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
            if($userId == $value->integradoId){
                $respuesta[] = $value;
            }
        }

        return $respuesta;
    }

    public static function getProducts($userId = null){
        $productos 				= new stdClass;
        $productos->id			= 1;
        $productos->integradoId	= 1;
        $productos->productName	= 'Producto A';
        $productos->measure		= 'Litros';
        $productos->price		= '$10000';
        $productos->iva			= '$150';
        $productos->ieps		= '$100';
        $productos->currency	= 'MXN';
        $productos->status		= '0';
        $productos->description = "U Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.";

        $array[] = $productos;

        $productos 				= new stdClass;
        $productos->id			= 2;
        $productos->integradoId	= 1;
        $productos->productName	= 'Producto B';
        $productos->measure		= 'Centimetros';
        $productos->price		= '$100000';
        $productos->iva			= '$150';
        $productos->ieps		= '$100';
        $productos->currency	= 'MXN';
        $productos->status		= '1';
        $productos->description = "O Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.";

        $array[] = $productos;

        $productos 				= new stdClass;
        $productos->id			= 3;
        $productos->integradoId	= 1;
        $productos->productName	= 'Producto C';
        $productos->measure		= 'Pieza';
        $productos->price		= '$1000';
        $productos->iva			= '$150';
        $productos->ieps		= '$100';
        $productos->currency	= 'MXN';
        $productos->status		= '0';
        $productos->description = "I Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.";

        $array[] = $productos;

        $productos 				= new stdClass;
        $productos->id			= 4;
        $productos->integradoId	= 1;
        $productos->productName	= 'Producto D';
        $productos->measure		= 'Rads';
        $productos->price		= '$1000';
        $productos->iva			= '$150';
        $productos->ieps		= '$100';
        $productos->currency	= 'MXN';
        $productos->status		= '1';
        $productos->description = "E Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.";

        $array[] = $productos;

        $productos 				= new stdClass;
        $productos->id			= 5;
        $productos->integradoId	= 1;
        $productos->productName	= 'Producto E';
        $productos->measure		= 'DB';
        $productos->price		= '$1000';
        $productos->iva			= '$150';
        $productos->ieps		= '$100';
        $productos->currency	= 'MXN';
        $productos->status		= '0';
        $productos->description = "A Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.";

        $array[] = $productos;

        $productos 				= new stdClass;
        $productos->id			= 6;
        $productos->integradoId	= 2;
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
        $productos->integradoId	= 2;
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
        $productos->integradoId	= 2;
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
        $productos->integradoId	= 2;
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
        $productos->integradoId	= 2;
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
            if($userId == $value->integradoId){
                $respuesta[] = $value;
            }
        }

        return $respuesta;
    }

    public static function getClientes($userId = null){
        $respuesta = array();

        $clientes 					= new stdClass;
        $clientes->id				= 1;
        $clientes->type				= 0;
        $clientes->integradoId		= 1;
        $clientes->rfc				= 'AAML810328EH8';
        $clientes->tradeName 		= 'Cliente A';
        $clientes->corporateName	= 'z';
        $clientes->contact			= 'Contacto A';
        $clientes->phone			= '5556183180';
        $clientes->status			= 0;

        $array[] = $clientes;

        $clientes 					= new stdClass;
        $clientes->id				= 2;
        $clientes->type				= 1;
        $clientes->integradoId		= 1;
        $clientes->rfc				= 'BAML810328EH8';
        $clientes->tradeName 		= 'Proveedor A';
        $clientes->corporateName	= 'y';
        $clientes->contact			= 'Contacto B';
        $clientes->phone			= '5556183180';
        $clientes->status			= 0;

        $array[] = $clientes;

        $clientes 					= new stdClass;
        $clientes->id				= 3;
        $clientes->type				= 0;
        $clientes->integradoId		= 1;
        $clientes->rfc				= 'CAML810328EH8';
        $clientes->tradeName 		= 'Cliente B';
        $clientes->corporateName	= 'x';
        $clientes->contact			= 'Contacto C';
        $clientes->phone			= '5556183180';
        $clientes->status			= 0;

        $array[] = $clientes;

        $clientes 					= new stdClass;
        $clientes->id				= 4;
        $clientes->type				= 1;
        $clientes->integradoId		= 1;
        $clientes->rfc				= 'DAML810328EH8';
        $clientes->tradeName 		= 'Proveedor B';
        $clientes->corporateName	= 'w';
        $clientes->contact			= 'Contacto D';
        $clientes->phone			= '5556183180';
        $clientes->status			= 0;

        $array[] = $clientes;

        $clientes 					= new stdClass;
        $clientes->id				= 5;
        $clientes->type				= 0;
        $clientes->integradoId		= 1;
        $clientes->rfc				= 'EAML810328EH8';
        $clientes->tradeName 		= 'Cliente C';
        $clientes->corporateName	= 'v';
        $clientes->contact			= 'Contacto E';
        $clientes->phone			= '5556183180';
        $clientes->status			= 0;

        $array[] = $clientes;

        $clientes 					= new stdClass;
        $clientes->id				= 6;
        $clientes->type				= 1;
        $clientes->integradoId		= 1;
        $clientes->rfc				= 'FAML810328EH8';
        $clientes->tradeName 		= 'Proveedor C';
        $clientes->corporateName	= 'u';
        $clientes->contact			= 'Contacto F';
        $clientes->phone			= '5556183180';
        $clientes->status			= '0';

        $array[] = $clientes;

        $clientes 					= new stdClass;
        $clientes->id				= 7;
        $clientes->type				= 0;
        $clientes->integradoId		= 1;
        $clientes->rfc				= 'GAML810328EH8';
        $clientes->tradeName 		= 'Cliente D';
        $clientes->corporateName	= 't';
        $clientes->contact			= 'Contacto G';
        $clientes->phone			= '5556183180';
        $clientes->status			= '0';

        $array[] = $clientes;

        $clientes 					= new stdClass;
        $clientes->id				= 8;
        $clientes->type				= 1;
        $clientes->integradoId		= 1;
        $clientes->rfc				= 'HAML810328EH8';
        $clientes->tradeName 		= 'Proveedor D';
        $clientes->corporateName	= 's';
        $clientes->contact			= 'Contacto H';
        $clientes->phone			= '5556183180';
        $clientes->status			= '0';

        $array[] = $clientes;

        $clientes 					= new stdClass;
        $clientes->id				= 9;
        $clientes->type				= 0;
        $clientes->integradoId		= 1;
        $clientes->rfc				= 'IAML810328EH8';
        $clientes->tradeName 		= 'Cliente E';
        $clientes->corporateName	= 'r';
        $clientes->contact			= 'Contacto I';
        $clientes->phone			= '5556183180';
        $clientes->status			= 1;

        $array[] = $clientes;

        $clientes 					= new stdClass;
        $clientes->id				= 10;
        $clientes->type				= 1;
        $clientes->integradoId		= 1;
        $clientes->rfc				= 'JAML810328EH8';
        $clientes->tradeName 		= 'Proveedor E';
        $clientes->corporateName	= 'q';
        $clientes->contact			= 'Contacto J';
        $clientes->phone			= '5556183180';
        $clientes->status			= 1;

        $array[] = $clientes;

        foreach ($array as $key => $value) {
            if($userId == $value->integradoId){
                $respuesta[] = $value;
            }
        }

        return $respuesta;
    }

    public static function getOrdenesCompra($integradoId){
        $ordenes 					= new stdClass;
        $ordenes->id				= 1;
        $ordenes->proyecto			= 3;
        $ordenes->proveedor			= 2;
        $ordenes->integradoId		= 1;
        $ordenes->folio				= 988976;
        $ordenes->created			= 1408632474029;
        $ordenes->payment			= 1410000000000;
        $ordenes->productos			= array(
            array('cantidad' => 1, 'descripcion' => 'Producto 1', 'unidad' => 'Kg', 'pUnitario' => 12.35),
            array('cantidad' => 3, 'descripcion' => 'Producto 2', 'unidad' => 'm2', 'pUnitario' => 120.5),
            array('cantidad' => 6, 'descripcion' => 'Producto 3', 'unidad' => 'Unidad', 'pUnitario' => 1.75)
        );
        $ordenes->totalAmount   	= 384.35;
        $ordenes->paymentType		= 0;
        $ordenes->ieps				= .1;
        $ordenes->iva				= .16;
        $ordenes->status			= 0;
        $ordenes->descripcion		= 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
        $ordenes->observaciones		= 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
        $ordenes->currency			= 'MXN';

        $array[] = $ordenes;

        $ordenes 					= new stdClass;
        $ordenes->id				= 2;
        $ordenes->proyecto			= 3;
        $ordenes->proveedor			= 4;
        $ordenes->integradoId		= 1;
        $ordenes->folio				= 588973;
        $ordenes->created			= 1408632474029;
        $ordenes->payment			= 1410000000000;
        $ordenes->productos			= array(
            array('cantidad' => 1, 'descripcion' => 'Producto 1', 'unidad' => 'Kg', 'pUnitario' => 12.35),
            array('cantidad' => 10, 'descripcion' => 'Producto 2', 'unidad' => 'm2', 'pUnitario' => 120.5),
            array('cantidad' => 6, 'descripcion' => 'Producto 3', 'unidad' => 'Unidad', 'pUnitario' => 1.75)
        );
        $ordenes->totalAmount   	=    1227.85;
        $ordenes->paymentType		= 0;
        $ordenes->status			= 1;
        $ordenes->ieps				= .1;
        $ordenes->iva				= .16;
        $ordenes->observaciones		= 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
        $ordenes->currency			= 'MXN';

        $array[] = $ordenes;

        $ordenes 					= new stdClass;
        $ordenes->id				= 3;
        $ordenes->proyecto			= 3;
        $ordenes->proveedor			= 2;
        $ordenes->integradoId		= 1;
        $ordenes->folio				= 988975;
        $ordenes->created			= 1408632474029;
        $ordenes->payment			= 1410000000000;
        $ordenes->productos			= array(
            array('cantidad' => 1, 'descripcion' => 'Producto 1', 'unidad' => 'Kg', 'pUnitario' => 12.35),
            array('cantidad' => 3, 'descripcion' => 'Producto 2', 'unidad' => 'm2', 'pUnitario' => 120.5),
            array('cantidad' => 6, 'descripcion' => 'Producto 3', 'unidad' => 'Unidad', 'pUnitario' => 1.75)
        );
        $ordenes->totalAmount   	=    384.35;
        $ordenes->paymentType		= 0;
        $ordenes->status			= 0;
        $ordenes->ieps				= .1;
        $ordenes->iva				= .16;
        $ordenes->observaciones		= 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
        $ordenes->currency			= 'MXN';

        $array[] = $ordenes;

        $ordenes 					= new stdClass;
        $ordenes->id				= 4;
        $ordenes->proyecto			= 1;
        $ordenes->proveedor			= 4;
        $ordenes->integradoId		= 2;
        $ordenes->folio				= 988977;
        $ordenes->created			= 1408632474029;
        $ordenes->payment			= 1410000000000;
        $ordenes->productos			= array(
            array('cantidad' => 1, 'descripcion' => 'Producto 1', 'unidad' => 'Kg', 'pUnitario' => 12.35),
            array('cantidad' => 3, 'descripcion' => 'Producto 2', 'unidad' => 'm2', 'pUnitario' => 120.5),
            array('cantidad' => 6, 'descripcion' => 'Producto 3', 'unidad' => 'Unidad', 'pUnitario' => 1.75)
        );
        $ordenes->totalAmount   	=    384.35;
        $ordenes->paymentType		= 0;
        $ordenes->status			= 0;
        $ordenes->ieps				= .1;
        $ordenes->iva				= .16;
        $ordenes->observaciones		= 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
        $ordenes->currency			= 'MXN';

        $ordenes 					= new stdClass;
        $ordenes->id				= 4;
        $ordenes->proyecto			= 1;
        $ordenes->proveedor			= 4;
        $ordenes->integradoId		= 2;
        $ordenes->folio				= 988978;
        $ordenes->created			= 1408632474029;
        $ordenes->payment			= 1410000000000;
        $ordenes->productos			= array(
            array('cantidad' => 1, 'descripcion' => 'Producto 1', 'unidad' => 'Kg', 'pUnitario' => 12.35),
            array('cantidad' => 3, 'descripcion' => 'Producto 2', 'unidad' => 'm2', 'pUnitario' => 120.5),
            array('cantidad' => 6, 'descripcion' => 'Producto 3', 'unidad' => 'Unidad', 'pUnitario' => 1.75)
        );
        $ordenes->totalAmount   	=    1384.35;
        $ordenes->paymentType		= 0;
        $ordenes->status			= 0;
        $ordenes->ieps				= .1;
        $ordenes->iva				= .16;
        $ordenes->observaciones		= 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
        $ordenes->currency			= 'MXN';

        $ordenes 					= new stdClass;
        $ordenes->id				= 4;
        $ordenes->proyecto			= 1;
        $ordenes->proveedor			= 4;
        $ordenes->integradoId		= 2;
        $ordenes->folio				= 988979;
        $ordenes->created			= 1408632474029;
        $ordenes->payment			= 1410000000000;
        $ordenes->productos			= array(
            array('cantidad' => 1, 'descripcion' => 'Producto 1', 'unidad' => 'Kg', 'pUnitario' => 12.35),
            array('cantidad' => 3, 'descripcion' => 'Producto 2', 'unidad' => 'm2', 'pUnitario' => 120.5),
            array('cantidad' => 6, 'descripcion' => 'Producto 3', 'unidad' => 'Unidad', 'pUnitario' => 1.75)
        );
        $ordenes->totalAmount   	=    4500.00;
        $ordenes->paymentType		= 0;
        $ordenes->status			= 1;
        $ordenes->ieps				= .1;
        $ordenes->iva				= .16;
        $ordenes->observaciones		= 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
        $ordenes->currency			= 'MXN';

        $array[] = $ordenes;

        foreach ($array as $key => $value) {
            if($integradoId == $value->integradoId){
                self::convierteFechas($value);
                $respuesta[] = $value;
            }
        }

        return $respuesta;
    }

    public static function getOrdenesDeposito($integradoId){
        $respuesta                  = null;
        $ordenes 					= new stdClass;
        $ordenes->id                = 1;
        $ordenes->integradoId       = 1;
        $ordenes->numOrden          = 1;
        $ordenes->created           = 1408632474029;
        $ordenes->payment			= 1428632474029;
        $ordenes->totalAmount        = 10000;
        $ordenes->currency        	= 'MXN';
        $ordenes->paymentType		= 0;
        $ordenes->status            = 0;
        $ordenes->observaciones		= 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $ordenes;

        $ordenes 					= new stdClass;
        $ordenes->id                = 2;
        $ordenes->integradoId       = 1;
        $ordenes->numOrden          = 2;
        $ordenes->created           = 1408632474029;
        $ordenes->payment			= 1428632474029;
        $ordenes->totalAmount        = 10000;
        $ordenes->currency        	= 'MXN';
        $ordenes->paymentType		= 0;
        $ordenes->status            = 1;
        $ordenes->observaciones		= 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $ordenes;

        $ordenes 					= new stdClass;
        $ordenes->id                = 3;
        $ordenes->integradoId       = 1;
        $ordenes->numOrden          = 3;
        $ordenes->created           = 1408632474029;
        $ordenes->payment			= 1428632474029;
        $ordenes->totalAmount        = 10000;
        $ordenes->currency        	= 'MXN';
        $ordenes->paymentType		= 0;
        $ordenes->status            = 0;
        $ordenes->observaciones		= 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $ordenes;

        $ordenes 					= new stdClass;
        $ordenes->id                = 4;
        $ordenes->integradoId       = 1;
        $ordenes->numOrden          = 4;
        $ordenes->created           = 1408632474029;
        $ordenes->payment			= 1428632474029;
        $ordenes->totalAmount        = 10000;
        $ordenes->currency        	= 'MXN';
        $ordenes->paymentType		= 0;
        $ordenes->status            = 0;
        $ordenes->observaciones		= 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $ordenes;

        $ordenes 					= new stdClass;
        $ordenes->id                = 5;
        $ordenes->integradoId       = 1;
        $ordenes->numOrden          = 5;
        $ordenes->created           = 1408632474029;
        $ordenes->payment			= 1428632474029;
        $ordenes->totalAmount        = 10000;
        $ordenes->currency        	= 'MXN';
        $ordenes->paymentType		= 0;
        $ordenes->status            = 0;
        $ordenes->observaciones		= 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $ordenes;

        $ordenes 					= new stdClass;
        $ordenes->id                = 6;
        $ordenes->integradoId       = 2;
        $ordenes->numOrden          = 6;
        $ordenes->created           = 1408632474029;
        $ordenes->payment			= 1428632474029;
        $ordenes->totalAmount        = 10000;
        $ordenes->currency        	= 'MXN';
        $ordenes->paymentType		= 0;
        $ordenes->status            = 0;
        $ordenes->observaciones		= 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $ordenes;

        $ordenes 					= new stdClass;
        $ordenes->id                = 7;
        $ordenes->integradoId       = 2;
        $ordenes->numOrden          = 7;
        $ordenes->created           = 1408632474029;
        $ordenes->payment			= 1428632474029;
        $ordenes->totalAmount        = 10000;
        $ordenes->currency        	= 'MXN';
        $ordenes->paymentType		= 0;
        $ordenes->status            = 0;
        $ordenes->observaciones		= 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $ordenes;

        $ordenes 					= new stdClass;
        $ordenes->id                = 8;
        $ordenes->integradoId       = 2;
        $ordenes->numOrden          = 8;
        $ordenes->created           = 1408632474029;
        $ordenes->payment			= 1428632474029;
        $ordenes->totalAmount        = 10000;
        $ordenes->currency        	= 'MXN';
        $ordenes->paymentType		= 0;
        $ordenes->status            = 0;
        $ordenes->observaciones		= 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $ordenes;

        $ordenes 					= new stdClass;
        $ordenes->id                = 9;
        $ordenes->integradoId       = 2;
        $ordenes->numOrden          = 9;
        $ordenes->created           = 1408632474029;
        $ordenes->payment			= 1428632474029;
        $ordenes->totalAmount        = 10000;
        $ordenes->currency        	= 'MXN';
        $ordenes->paymentType		= 0;
        $ordenes->status            = 0;
        $ordenes->observaciones		= 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $ordenes;

        foreach ($array as $key => $value) {
            if($integradoId == $value->integradoId){
                self::convierteFechas($value);
                $respuesta[] = $value;
            }
        }

        return $respuesta;
    }

    public static function getOrdenesVenta($integradoId) {
        $respuesta = null;

        $ordenes 					= new stdClass;
        $ordenes->id                = 1;
        $ordenes->integradoId       = 1;
        $ordenes->numOrden          = 1;
        $ordenes->proyectId         = 1;
        $ordenes->clientId          = 2;
        $ordenes->created           = 1408632474029;
        $ordenes->payment			= 1428632474029;
        $ordenes->totalAmount        = 10000;
        $ordenes->currency        	= 'MXN';
        $ordenes->status            = 0;
        $ordenes->paymentType       = 1;
        $ordenes->observaciones       = 'Orden de venta de ejemplo';

        $array[] = $ordenes;

        $ordenes 					= new stdClass;
        $ordenes->id                = 2;
        $ordenes->integradoId       = 1;
        $ordenes->numOrden          = 2;
        $ordenes->proyectId         = 1;
        $ordenes->clientId          = 4;
        $ordenes->created           = 1408632474029;
        $ordenes->payment			= 1428632474029;
        $ordenes->totalAmount        = 10000;
        $ordenes->currency        	= 'MXN';
        $ordenes->status            = 0;
        $ordenes->paymentType       = 1;
        $ordenes->observaciones       = 'Orden de venta de ejemplo';

        $array[] = $ordenes;

        $ordenes 					= new stdClass;
        $ordenes->id                = 3;
        $ordenes->integradoId       = 1;
        $ordenes->numOrden          = 3;
        $ordenes->proyectId         = 1;
        $ordenes->clientId          = 6;
        $ordenes->created           = 1408632474029;
        $ordenes->payment			= 1428632474029;
        $ordenes->totalAmount        = 10000;
        $ordenes->currency        	= 'MXN';
        $ordenes->status            = 1;
        $ordenes->paymentType       = 1;
        $ordenes->observaciones       = 'Orden de venta de ejemplo';

        $array[] = $ordenes;

        $ordenes 					= new stdClass;
        $ordenes->id                = 6;
        $ordenes->integradoId       = 2;
        $ordenes->numOrden          = 6;
        $ordenes->proyectId         = 2;
        $ordenes->clientId          = 1;
        $ordenes->created           = 1408632474029;
        $ordenes->payment			= 1428632474029;
        $ordenes->totalAmount        = 10000;
        $ordenes->currency        	= 'MXN';
        $ordenes->status            = 0;
        $ordenes->paymentType       = 1;
        $ordenes->observaciones       = 'Orden de venta de ejemplo';

        $array[] = $ordenes;

        foreach ($array as $key => $value) {
            if($integradoId == $value->integradoId){
                self::convierteFechas($value);
                $respuesta[] = $value;
            }
        }

        return $respuesta;

    }

    public static function getOrdenesRetiro($integradoId) {
        $respuesta = null;

        $ordenes 					= new stdClass;
        $ordenes->id                = 1;
        $ordenes->integradoId       = 1;
        $ordenes->numOrden          = 1;
        $ordenes->proyectId         = 1;
        $ordenes->created           = 1408632474029;
        $ordenes->totalAmount        = 10000;
        $ordenes->currency        	= 'MXN';
        $ordenes->paymentType		= 0;
        $ordenes->status            = 0;
        $ordenes->observaciones		= 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $ordenes;

        $ordenes 					= new stdClass;
        $ordenes->id                = 2;
        $ordenes->integradoId       = 1;
        $ordenes->numOrden          = 20;
        $ordenes->proyectId         = 1;
        $ordenes->created           = 1408632474029;
        $ordenes->totalAmount        = 11000;
        $ordenes->currency        	= 'MXN';
        $ordenes->paymentType		= 0;
        $ordenes->status            = 0;
        $ordenes->observaciones		= 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $ordenes;

        $ordenes 					= new stdClass;
        $ordenes->id                = 3;
        $ordenes->integradoId       = 1;
        $ordenes->numOrden          = 3;
        $ordenes->proyectId         = 1;
        $ordenes->created           = 1408632474029;
        $ordenes->totalAmount        = 12000;
        $ordenes->currency        	= 'MXN';
        $ordenes->paymentType		= 0;
        $ordenes->status            = 1;
        $ordenes->observaciones		= 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $ordenes;

        $ordenes 					= new stdClass;
        $ordenes->id                = 6;
        $ordenes->integradoId       = 2;
        $ordenes->numOrden          = 6;
        $ordenes->proyectId         = 2;
        $ordenes->created           = 1408632474029;
        $ordenes->totalAmount        = 13000;
        $ordenes->currency        	= 'MXN';
        $ordenes->paymentType		= 0;
        $ordenes->status            = 0;
        $ordenes->observaciones		= 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $ordenes;

        foreach ($array as $key => $value) {
            if($integradoId == $value->integradoId){
                self::convierteFechas($value);
                $respuesta[] = $value;
            }
        }

        return $respuesta;

    }

	public static function getComisiones () {
		$commissions = null;

		$commissions 				    = new comisionItem();
		$commissions->id                = 1;
		$commissions->description		= 'Cobro por retiro';
		$commissions->type           	= 1;
		$commissions->frequencyTime		= 15;
		$commissions->status			= 1;
		$commissions->amount			= null;
		$commissions->rate				= 0.25;

		$array[] = $commissions;

		$commissions 				    = new comisionItem;
		$commissions->id                = 2;
		$commissions->description		= 'Cobro por compra';
		$commissions->type           	= 1;
		$commissions->frequencyTime		= 90;
		$commissions->status			= 1;
		$commissions->amount			= null;
		$commissions->rate				= 0.25;

		$array[] = $commissions;

		$commissions 				    = new comisionItem;
		$commissions->id                = 2;
		$commissions->description		= 'Contabilidad';
		$commissions->type           	= 0;
		$commissions->frequencyTime		= null;
		$commissions->status			= 1;
		$commissions->amount			= 1000;
		$commissions->rate				= null;

		$array[] = $commissions;


		return $array;

	}

    public static function getMedidas(){
        $respuesta['litros'] 			= 'litros';
        $respuesta['Metros'] 			= 'Metros';
        $respuesta['Metros Cúbicos'] 	= 'Metros Cúbicos';

        return $respuesta;
    }

    public static function convierteFechas($objeto){
        foreach ($objeto as $key => $value) {
            if($key == 'created' || $key == 'payment'){
                $objeto->$key = date('d-m-Y', ($value/1000) );
            }
        }

    }

	public static function token(){
		$url = MIDDLE.PUERTO.TIMONE.'security/getKey';
		if( !$token = file_get_contents($url) ){
			JFactory::getApplication()->redirect('index.php', 'No se pudo conectar con TIMONE', 'error');
		}

		return $token;
	}

	public static function newintegradoId($envio, $callback){
		$jsonData = json_encode($envio);

//		$serviceUrl = MIDDLE.PUERTO.'/tim-integradora/user/save';
		$serviceUrl = 'http://192.168.0.126:8090/tim-integradora/user/save';

        $sendToTimone = new sendToTimOne();
        $results = $sendToTimone->to_timone($jsonData, $serviceUrl);

		//$results = self::to_timone($jsonData, $serviceUrl);

        return $results;
    }

	public static function getComisionById ($id) {
		$comision = null;
		$comisiones = self::getComisiones();

		foreach ($comisiones as $value) {
			if($value->id == $id) {
				$comision = $value;
			}
		}
		return $comision;
	}
}

class sendToTimOne {

	public $result;
	protected $httpType;
	protected $serviceUrl;
	protected $jsonData;

	function __construct () {
		$this->serviceUrl = null;
		$this->jsonData = null;
		$this->setHttpType('GET');
	}

	/**
	 * @param mixed $serviceUrl
	 */
	public function setServiceUrl ($serviceUrl) {
		$this->serviceUrl = $serviceUrl;
	}

	/**
	 * @return mixed
	 */
	public function getServiceUrl () {
		return $this->serviceUrl;
	}

	/**
	 * @param mixed $jsonData
	 */
	public function setJsonData ($jsonData) {
		$this->jsonData = $jsonData;
	}

	/**
	 * @return mixed
	 */
	public function getJsonData () {
		return $this->jsonData;
	}

	public function to_timone() {

//		$credentials = array('username' => '' ,'password' => '');
		$verbose = fopen('curl.log', 'w');
		$ch = curl_init();

		switch($this->getHttpType()) {
			case ('POST'):
				$options = array(
						CURLOPT_POST 			=> true,
						CURLOPT_URL            => $this->serviceUrl,
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_SSL_VERIFYPEER => false,
						CURLOPT_POSTFIELDS     => $this->jsonData,
						CURLOPT_HEADER         => true,
		//			CURLOPT_USERPWD        => ($credentials['username'] . ':' . $credentials['password']),
						CURLOPT_FOLLOWLOCATION => false,
						CURLOPT_VERBOSE        => true,
						CURLOPT_STDERR		   => $verbose,
						CURLOPT_HTTPHEADER	   => array(
							'Content-Type: application/json',
							'Content-Length: ' . strlen($this->jsonData)
						)
				);
				break;
			case ('PUT'):
				$options = array(
						CURLOPT_PUT 			=> true,
						CURLOPT_URL            => $this->serviceUrl,
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_SSL_VERIFYPEER => false,
						CURLOPT_HEADER         => true,
						//			CURLOPT_USERPWD        => ($credentials['username'] . ':' . $credentials['password']),
						CURLOPT_FOLLOWLOCATION => false,
						CURLOPT_VERBOSE        => true,
						CURLOPT_STDERR		   => $verbose,
						CURLOPT_HTTPHEADER	   => array(
							'Content-Type: application/json',
							'Content-Length: ' . strlen($this->jsonData)
						)
				);
				break;
			case 'DELETE':
				$options = array(
						CURLOPT_CUSTOMREQUEST => "DELETE",
						CURLOPT_URL            => $this->serviceUrl,
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_SSL_VERIFYPEER => false,
						CURLOPT_HEADER         => true,
						//			CURLOPT_USERPWD        => ($credentials['username'] . ':' . $credentials['password']),
						CURLOPT_FOLLOWLOCATION => false,
						CURLOPT_VERBOSE        => true,
						CURLOPT_STDERR		   => $verbose,
						CURLOPT_HTTPHEADER	   => array(
							'Content-Type: application/json',
							'Content-Length: ' . strlen($this->jsonData)
						)
				);
				break;
			default:
				$options = array(
						CURLOPT_HTTPGET			=> true,
						CURLOPT_URL            => $this->serviceUrl,
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_SSL_VERIFYPEER => false,
						CURLOPT_HEADER         => true,
						//			CURLOPT_USERPWD        => ($credentials['username'] . ':' . $credentials['password']),
						CURLOPT_FOLLOWLOCATION => false,
						CURLOPT_VERBOSE        => true,
						CURLOPT_STDERR		   => $verbose,
						CURLOPT_HTTPHEADER	   => array(
							'Content-Type: application/json',
							'Content-Length: ' . strlen($this->jsonData)
						)
				);
				break;
		}

		curl_setopt_array($ch,$options);

		$this->result->data = curl_exec($ch);

		rewind($verbose);
		$verboseLog = stream_get_contents($verbose);
//		echo "Verbose information:\n<pre>", htmlspecialchars($verboseLog), "</pre>\n".curl_errno($ch).curl_error($ch);

		$this->result->code = curl_getinfo ($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		switch ($this->result->code) {
			case 200:
				$this->result->message = JText::_('JGLOBAL_AUTH_ACCESS_GRANTED');
				break;
			case 401:
				$this->result->message = JText::_('JGLOBAL_AUTH_ACCESS_DENIED');
				break;
			default:
				$this->result->message = JText::_('JGLOBAL_AUTH_UNKNOWN_ACCESS_DENIED');
				break;
		}

		return $this->result;
	}

	public function setHttpType ($type) {
		if(in_array($type, array('PUT', 'POST', 'GET', 'PATCH', 'DELETE'))) {
			$this->httpType = $type;
		} else {
			return false;
		}
	}

	/**
	 * @return mixed
	 */
	public function getHttpType () {
		return strtoupper($this->httpType);
	}

}

class comisionItem{
	public $id;
	public $description;
	public $type;
	public $frequencyTime;
	public $status;
	public $typeName;
	public $statusName;
	public $frequencyMsg;
	public $amount;
	public $rate;
}