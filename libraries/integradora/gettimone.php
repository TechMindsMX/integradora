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

    public static function getOrdenesCompra($integradoId = null){
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
            }else{
                self::convierteFechas($value);
                $respuesta[] = $value;
            }
        }

        return $respuesta;
    }

    public static function getOrdenesDeposito($integradoId = null){
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
            }else{
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
	    $ordenes->ieps				= .1;
	    $ordenes->iva				= .16;
        $ordenes->paymentType       = 1;
        $ordenes->observaciones       = 'Orden de venta de ejemplo';
        $ordenes->productos			= array(
            array('cantidad' => 1, 'descripcion' => 'Producto 1', 'unidad' => 'Kg', 'pUnitario' => 12.35),
            array('cantidad' => 3, 'descripcion' => 'Producto 2', 'unidad' => 'm2', 'pUnitario' => 120.5),
            array('cantidad' => 6, 'descripcion' => 'Producto 3', 'unidad' => 'Unidad', 'pUnitario' => 1.75)
        );

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
	    $ordenes->ieps				= .1;
	    $ordenes->iva				= .16;
        $ordenes->paymentType       = 1;
        $ordenes->observaciones       = 'Orden de venta de ejemplo';
        $ordenes->productos			= array(
            array('cantidad' => 1, 'descripcion' => 'Producto 1', 'unidad' => 'Kg', 'pUnitario' => 12.35),
            array('cantidad' => 3, 'descripcion' => 'Producto 2', 'unidad' => 'm2', 'pUnitario' => 120.5),
            array('cantidad' => 6, 'descripcion' => 'Producto 3', 'unidad' => 'Unidad', 'pUnitario' => 1.75)
        );

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
	    $ordenes->ieps				= .1;
	    $ordenes->iva				= .16;
        $ordenes->paymentType       = 1;
        $ordenes->observaciones       = 'Orden de venta de ejemplo';
        $ordenes->productos			= array(
            array('cantidad' => 1, 'descripcion' => 'Producto 1', 'unidad' => 'Kg', 'pUnitario' => 12.35),
            array('cantidad' => 3, 'descripcion' => 'Producto 2', 'unidad' => 'm2', 'pUnitario' => 120.5),
            array('cantidad' => 6, 'descripcion' => 'Producto 3', 'unidad' => 'Unidad', 'pUnitario' => 1.75)
        );

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
	    $ordenes->ieps				= .1;
	    $ordenes->iva				= .16;
        $ordenes->paymentType       = 1;
        $ordenes->observaciones       = 'Orden de venta de ejemplo';
        $ordenes->productos			= array(
            array('cantidad' => 1, 'descripcion' => 'Producto 1', 'unidad' => 'Kg', 'pUnitario' => 12.35),
            array('cantidad' => 3, 'descripcion' => 'Producto 2', 'unidad' => 'm2', 'pUnitario' => 120.5),
            array('cantidad' => 6, 'descripcion' => 'Producto 3', 'unidad' => 'Unidad', 'pUnitario' => 1.75)
        );

        $array[] = $ordenes;

        foreach ($array as $key => $value) {
            if($integradoId == $value->integradoId){
                self::convierteFechas($value);
                $respuesta[] = $value;
            }
        }

        return $respuesta;

    }

    public static function getBalances($integradoId)
    {
        $respuesta = null;

        $balance = new stdClass;
        $balance->id = 1;
        $balance->integradoId = 1;
        $balance->numBalance = 1;
        $balance->proyectId = 1;
        $balance->created = 1388880000000;
        $balance->currency = 'MXN';
        $balance->paymentType = 0;
        $balance->status = 0;
        $balance->observaciones = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $balance;

        $balance = new stdClass;
        $balance->id = 1;
        $balance->integradoId = 1;
        $balance->numBalance = 1;
        $balance->proyectId = 1;
        $balance->created = 1393718400000;
        $balance->currency = 'MXN';
        $balance->paymentType = 0;
        $balance->status = 0;
        $balance->observaciones = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $balance;

        $balance = new stdClass;
        $balance->id = 1;
        $balance->integradoId = 1;
        $balance->numBalance = 1;
        $balance->proyectId = 1;
        $balance->created = 1396137600000;
        $balance->currency = 'MXN';
        $balance->paymentType = 0;
        $balance->status = 0;
        $balance->observaciones = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $balance;

        $balance = new stdClass;
        $balance->id = 1;
        $balance->integradoId = 1;
        $balance->numBalance = 1;
        $balance->proyectId = 1;
        $balance->created = 1398816000000;
        $balance->currency = 'MXN';
        $balance->paymentType = 0;
        $balance->status = 0;
        $balance->observaciones = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $balance;

        $balance = new stdClass;
        $balance->id = 1;
        $balance->integradoId = 1;
        $balance->numBalance = 1;
        $balance->proyectId = 1;
        $balance->created = 1401408000000;
        $balance->currency = 'MXN';
        $balance->paymentType = 0;
        $balance->status = 0;
        $balance->observaciones = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $balance;


        $balance = new stdClass;
        $balance->id = 1;
        $balance->integradoId = 1;
        $balance->numBalance = 1;
        $balance->proyectId = 1;
        $balance->created = 1404086400000;
        $balance->currency = 'MXN';
        $balance->paymentType = 0;
        $balance->status = 0;
        $balance->observaciones = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $balance;

        $balance = new stdClass;
        $balance->id = 1;
        $balance->integradoId = 1;
        $balance->numBalance = 1;
        $balance->proyectId = 1;
        $balance->created = 1409356800000;
        $balance->currency = 'MXN';
        $balance->paymentType = 0;
        $balance->status = 0;
        $balance->observaciones = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $balance;

        $balance = new stdClass;
        $balance->id = 1;
        $balance->integradoId = 1;
        $balance->numBalance = 1;
        $balance->proyectId = 1;
        $balance->created = 1412035200000;
        $balance->currency = 'MXN';
        $balance->paymentType = 0;
        $balance->status = 0;
        $balance->observaciones = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $balance;

        $balance = new stdClass;
        $balance->id = 1;
        $balance->integradoId = 1;
        $balance->numBalance = 1;
        $balance->proyectId = 1;
        $balance->created = 1414627200000;
        $balance->currency = 'MXN';
        $balance->paymentType = 0;
        $balance->status = 0;
        $balance->observaciones = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $balance;

        $balance = new stdClass;
        $balance->id = 1;
        $balance->integradoId = 1;
        $balance->numBalance = 1;
        $balance->proyectId = 1;
        $balance->created = 1417305600000;
        $balance->currency = 'MXN';
        $balance->paymentType = 0;
        $balance->status = 0;
        $balance->observaciones = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $balance;

        $balance = new stdClass;
        $balance->id = 1;
        $balance->integradoId = 1;
        $balance->numBalance = 1;
        $balance->proyectId = 1;
        $balance->created = 1419897600000;
        $balance->currency = 'MXN';
        $balance->paymentType = 0;
        $balance->status = 0;
        $balance->observaciones = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $balance;

        foreach ($array as $key => $value) {
            if($integradoId == $value->integradoId){
                self::convierteFechas($value);
                $respuesta[] = $value;
            }
        }
        return $respuesta;
    }

    public static function getFlujo($integradoId)
    {
        $respuesta = null;

        $flujo = new stdClass;
        $flujo->id = 1;
        $flujo->integradoId = 1;
        $flujo->numflujo = 1;
        $flujo->proyectId = 1;
        $flujo->created = 1388880000000;
        $flujo->currency = 'MXN';
        $flujo->paymentType = 0;
        $flujo->status = 0;
        $flujo->observaciones = 'Muy lejos, más allá de las montañas de palabras, alejados de los países de las vocales y las consonantes, viven los textos simulados. Viven aislados en casas de letras, en la costa de la semántica, un';

        $array[] = $flujo;

        $flujo = new stdClass;
        $flujo->id = 1;
        $flujo->integradoId = 1;
        $flujo->numflujo = 1;
        $flujo->proyectId = 1;
        $flujo->created = 1393718400000;
        $flujo->currency = 'MXN';
        $flujo->paymentType = 0;
        $flujo->status = 0;
        $flujo->observaciones = 'Muy lejos, más allá de las montañas de palabras, alejados de los países de las vocales y las consonantes, viven los textos simulados. Viven aislados en casas de letras, en la costa de la semántica, un';

        $array[] = $flujo;

        $flujo = new stdClass;
        $flujo->id = 1;
        $flujo->integradoId = 1;
        $flujo->numflujo = 1;
        $flujo->proyectId = 1;
        $flujo->created = 1396137600000;
        $flujo->currency = 'MXN';
        $flujo->paymentType = 0;
        $flujo->status = 0;
        $flujo->observaciones = 'Muy lejos, más allá de las montañas de palabras, alejados de los países de las vocales y las consonantes, viven los textos simulados. Viven aislados en casas de letras, en la costa de la semántica, un';

        $array[] = $flujo;

        $flujo = new stdClass;
        $flujo->id = 1;
        $flujo->integradoId = 1;
        $flujo->numflujo = 1;
        $flujo->proyectId = 1;
        $flujo->created = 1398816000000;
        $flujo->currency = 'MXN';
        $flujo->paymentType = 0;
        $flujo->status = 0;
        $flujo->observaciones = 'Muy lejos, más allá de las montañas de palabras, alejados de los países de las vocales y las consonantes, viven los textos simulados. Viven aislados en casas de letras, en la costa de la semántica, un';

        $array[] = $flujo;

        $flujo = new stdClass;
        $flujo->id = 1;
        $flujo->integradoId = 1;
        $flujo->numflujo = 1;
        $flujo->proyectId = 1;
        $flujo->created = 1401408000000;
        $flujo->currency = 'MXN';
        $flujo->paymentType = 0;
        $flujo->status = 0;
        $flujo->observaciones = 'Muy lejos, más allá de las montañas de palabras, alejados de los países de las vocales y las consonantes, viven los textos simulados. Viven aislados en casas de letras, en la costa de la semántica, un';

        $array[] = $flujo;


        $flujo = new stdClass;
        $flujo->id = 1;
        $flujo->integradoId = 1;
        $flujo->numflujo = 1;
        $flujo->proyectId = 1;
        $flujo->created = 1404086400000;
        $flujo->currency = 'MXN';
        $flujo->paymentType = 0;
        $flujo->status = 0;
        $flujo->observaciones = 'Muy lejos, más allá de las montañas de palabras, alejados de los países de las vocales y las consonantes, viven los textos simulados. Viven aislados en casas de letras, en la costa de la semántica, un';

        $array[] = $flujo;

        $flujo = new stdClass;
        $flujo->id = 1;
        $flujo->integradoId = 1;
        $flujo->numflujo = 1;
        $flujo->proyectId = 1;
        $flujo->created = 1409356800000;
        $flujo->currency = 'MXN';
        $flujo->paymentType = 0;
        $flujo->status = 0;
        $flujo->observaciones = 'Muy lejos, más allá de las montañas de palabras, alejados de los países de las vocales y las consonantes, viven los textos simulados. Viven aislados en casas de letras, en la costa de la semántica, un';

        $array[] = $flujo;

        $flujo = new stdClass;
        $flujo->id = 1;
        $flujo->integradoId = 1;
        $flujo->numflujo = 1;
        $flujo->proyectId = 1;
        $flujo->created = 1412035200000;
        $flujo->currency = 'MXN';
        $flujo->paymentType = 0;
        $flujo->status = 0;
        $flujo->observaciones = 'Muy lejos, más allá de las montañas de palabras, alejados de los países de las vocales y las consonantes, viven los textos simulados. Viven aislados en casas de letras, en la costa de la semántica, un';

        $array[] = $flujo;

        $flujo = new stdClass;
        $flujo->id = 1;
        $flujo->integradoId = 1;
        $flujo->numflujo = 1;
        $flujo->proyectId = 1;
        $flujo->created = 1414627200000;
        $flujo->currency = 'MXN';
        $flujo->paymentType = 0;
        $flujo->status = 0;
        $flujo->observaciones = 'Muy lejos, más allá de las montañas de palabras, alejados de los países de las vocales y las consonantes, viven los textos simulados. Viven aislados en casas de letras, en la costa de la semántica, un';

        $array[] = $flujo;

        $flujo = new stdClass;
        $flujo->id = 1;
        $flujo->integradoId = 1;
        $flujo->numflujo = 1;
        $flujo->proyectId = 1;
        $flujo->created = 1417305600000;
        $flujo->currency = 'MXN';
        $flujo->paymentType = 0;
        $flujo->status = 0;
        $flujo->observaciones = 'Muy lejos, más allá de las montañas de palabras, alejados de los países de las vocales y las consonantes, viven los textos simulados. Viven aislados en casas de letras, en la costa de la semántica, un';

        $array[] = $flujo;

        $flujo = new stdClass;
        $flujo->id = 1;
        $flujo->integradoId = 1;
        $flujo->numflujo = 1;
        $flujo->proyectId = 1;
        $flujo->created = 1419897600000;
        $flujo->currency = 'MXN';
        $flujo->paymentType = 0;
        $flujo->status = 0;
        $flujo->observaciones = 'Muy lejos, más allá de las montañas de palabras, alejados de los países de las vocales y las consonantes, viven los textos simulados. Viven aislados en casas de letras, en la costa de la semántica, un';

        $array[] = $flujo;

        foreach ($array as $key => $value) {
            if($integradoId == $value->integradoId){
                self::convierteFechas($value);
                $respuesta[] = $value;
            }
        }
        return $respuesta;
    }

    public static function getResultados($integradoId)
    {
        $respuesta = null;

        $resultados = new stdClass;
        $resultados->id = 1;
        $resultados->integradoId = 1;
        $resultados->numresultados = 1;
        $resultados->proyectId = 1;
        $resultados->created = 1388880000000;
        $resultados->currency = 'MXN';
        $resultados->paymentType = 0;
        $resultados->status = 0;
        $resultados->observaciones = 'Una mañana, tras un sueño intranquilo, Gregorio Samsa se despertó convertido en un monstruoso insecto. Estaba echado de espaldas sobre un duro caparazón y, al alzar la cabeza, vio su vientre convexo y';

        $array[] = $resultados;

        $resultados = new stdClass;
        $resultados->id = 1;
        $resultados->integradoId = 1;
        $resultados->numresultados = 1;
        $resultados->proyectId = 1;
        $resultados->created = 1393718400000;
        $resultados->currency = 'MXN';
        $resultados->paymentType = 0;
        $resultados->status = 0;
        $resultados->observaciones = 'Una mañana, tras un sueño intranquilo, Gregorio Samsa se despertó convertido en un monstruoso insecto. Estaba echado de espaldas sobre un duro caparazón y, al alzar la cabeza, vio su vientre convexo y';

        $array[] = $resultados;

        $resultados = new stdClass;
        $resultados->id = 1;
        $resultados->integradoId = 1;
        $resultados->numresultados = 1;
        $resultados->proyectId = 1;
        $resultados->created = 1396137600000;
        $resultados->currency = 'MXN';
        $resultados->paymentType = 0;
        $resultados->status = 0;
        $resultados->observaciones = 'Una mañana, tras un sueño intranquilo, Gregorio Samsa se despertó convertido en un monstruoso insecto. Estaba echado de espaldas sobre un duro caparazón y, al alzar la cabeza, vio su vientre convexo y';

        $array[] = $resultados;

        $resultados = new stdClass;
        $resultados->id = 1;
        $resultados->integradoId = 1;
        $resultados->numresultados = 1;
        $resultados->proyectId = 1;
        $resultados->created = 1398816000000;
        $resultados->currency = 'MXN';
        $resultados->paymentType = 0;
        $resultados->status = 0;
        $resultados->observaciones = 'Una mañana, tras un sueño intranquilo, Gregorio Samsa se despertó convertido en un monstruoso insecto. Estaba echado de espaldas sobre un duro caparazón y, al alzar la cabeza, vio su vientre convexo y';

        $array[] = $resultados;

        $resultados = new stdClass;
        $resultados->id = 1;
        $resultados->integradoId = 1;
        $resultados->numresultados = 1;
        $resultados->proyectId = 1;
        $resultados->created = 1401408000000;
        $resultados->currency = 'MXN';
        $resultados->paymentType = 0;
        $resultados->status = 0;
        $resultados->observaciones = 'Una mañana, tras un sueño intranquilo, Gregorio Samsa se despertó convertido en un monstruoso insecto. Estaba echado de espaldas sobre un duro caparazón y, al alzar la cabeza, vio su vientre convexo y';

        $array[] = $resultados;


        $resultados = new stdClass;
        $resultados->id = 1;
        $resultados->integradoId = 1;
        $resultados->numresultados = 1;
        $resultados->proyectId = 1;
        $resultados->created = 1404086400000;
        $resultados->currency = 'MXN';
        $resultados->paymentType = 0;
        $resultados->status = 0;
        $resultados->observaciones = 'Una mañana, tras un sueño intranquilo, Gregorio Samsa se despertó convertido en un monstruoso insecto. Estaba echado de espaldas sobre un duro caparazón y, al alzar la cabeza, vio su vientre convexo y';

        $array[] = $resultados;

        $resultados = new stdClass;
        $resultados->id = 1;
        $resultados->integradoId = 1;
        $resultados->numresultados = 1;
        $resultados->proyectId = 1;
        $resultados->created = 1409356800000;
        $resultados->currency = 'MXN';
        $resultados->paymentType = 0;
        $resultados->status = 0;
        $resultados->observaciones = 'Una mañana, tras un sueño intranquilo, Gregorio Samsa se despertó convertido en un monstruoso insecto. Estaba echado de espaldas sobre un duro caparazón y, al alzar la cabeza, vio su vientre convexo y';

        $array[] = $resultados;

        $resultados = new stdClass;
        $resultados->id = 1;
        $resultados->integradoId = 1;
        $resultados->numresultados = 1;
        $resultados->proyectId = 1;
        $resultados->created = 1412035200000;
        $resultados->currency = 'MXN';
        $resultados->paymentType = 0;
        $resultados->status = 0;
        $resultados->observaciones = 'Una mañana, tras un sueño intranquilo, Gregorio Samsa se despertó convertido en un monstruoso insecto. Estaba echado de espaldas sobre un duro caparazón y, al alzar la cabeza, vio su vientre convexo y';

        $array[] = $resultados;

        $resultados = new stdClass;
        $resultados->id = 1;
        $resultados->integradoId = 1;
        $resultados->numresultados = 1;
        $resultados->proyectId = 1;
        $resultados->created = 1414627200000;
        $resultados->currency = 'MXN';
        $resultados->paymentType = 0;
        $resultados->status = 0;
        $resultados->observaciones = 'Una mañana, tras un sueño intranquilo, Gregorio Samsa se despertó convertido en un monstruoso insecto. Estaba echado de espaldas sobre un duro caparazón y, al alzar la cabeza, vio su vientre convexo y';

        $array[] = $resultados;

        $resultados = new stdClass;
        $resultados->id = 1;
        $resultados->integradoId = 1;
        $resultados->numresultados = 1;
        $resultados->proyectId = 1;
        $resultados->created = 1417305600000;
        $resultados->currency = 'MXN';
        $resultados->paymentType = 0;
        $resultados->status = 0;
        $resultados->observaciones = 'Una mañana, tras un sueño intranquilo, Gregorio Samsa se despertó convertido en un monstruoso insecto. Estaba echado de espaldas sobre un duro caparazón y, al alzar la cabeza, vio su vientre convexo y';

        $array[] = $resultados;

        $resultados = new stdClass;
        $resultados->id = 1;
        $resultados->integradoId = 1;
        $resultados->numresultados = 1;
        $resultados->proyectId = 1;
        $resultados->created = 1419897600000;
        $resultados->currency = 'MXN';
        $resultados->paymentType = 0;
        $resultados->status = 0;
        $resultados->observaciones = 'Una mañana, tras un sueño intranquilo, Gregorio Samsa se despertó convertido en un monstruoso insecto. Estaba echado de espaldas sobre un duro caparazón y, al alzar la cabeza, vio su vientre convexo y';

        $array[] = $resultados;

        foreach ($array as $key => $value) {
            if($integradoId == $value->integradoId){
                self::convierteFechas($value);
                $respuesta[] = $value;
            }
        }
        return $respuesta;
    }
    public static function getOrdenesRetiro($integradoId = null) {
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
            }else{
                self::convierteFechas($value);
                $respuesta[] = $value;
            }
        }

        return $respuesta;

    }
    public static function getFactura() {
        $factura = null;

        $factura 				                                        = new stdClass();
        $factura->id                                                    = 1;
        $factura ->integradoId                                          = 1;
        $factura->status                                                = 1;
        $factura->Comprobante->schemaLocation                           = "http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv32.xsd";
        $factura->Comprobante->version                                  = "3.2";
        $factura->Comprobante->serie                                    = "110 - ";
        $factura->Comprobante->folio                                    = "120";
        $factura->Comprobante->LugarExpedicion                          = "México, D.F.";
        $factura->Comprobante->NumCtaPago                               = "012180001931017464 BANCOMER";
        $factura->Comprobante->TipoCambio                               = "1";
        $factura->Comprobante->Moneda                                   = "Pesos Mexicanos";
        $factura->Comprobante->fecha                                    = "2014-06-14T08:46:22";
        $factura->Comprobante->sello                                    = "LvBPRXayjjhrxNg5ulHqEF1Y89Dq8nz9c7hifOzH0C6r8iwODBf6h7P+ACbRgh+QPBtgnqjvlXn10Zw9WuWDVr+rAgkenFxC+VNST/2AHGr29zqa7r/FnENvtSjecClfJAy4GysstHXWm0BQQilfrYHZvu/qUZidnZVscr07jRU=";
        $factura->Comprobante->formaDePago                              = "Pago en una sola exhibición";
        $factura->Comprobante->noCertificado                            = "00001000000303670260";
        $factura->Comprobante->certificado                              = "MIIEhTCCA22gAwIBAgIUMDAwMDEwMDAwMDAzMDM2NzAyNjAwDQYJKoZIhvcNAQEFBQAwggGKMTgwNgYDVQQDDC9BLkMuIGRlbCBTZXJ2aWNpbyBkZSBBZG1pbmlzdHJhY2nDs24gVHJpYnV0YXJpYTEvMC0GA1UECgwmU2VydmljaW8gZGUgQWRtaW5pc3RyYWNpw7NuIFRyaWJ1dGFyaWExODA2BgNVBAsML0FkbWluaXN0cmFjacOzbiBkZSBTZWd1cmlkYWQgZGUgbGEgSW5mb3JtYWNpw7NuMR8wHQYJKoZIhvcNAQkBFhBhY29kc0BzYXQuZ29iLm14MSYwJAYDVQQJDB1Bdi4gSGlkYWxnbyA3NywgQ29sLiBHdWVycmVybzEOMAwGA1UEEQwFMDYzMDAxCzAJBgNVBAYTAk1YMRkwFwYDVQQIDBBEaXN0cml0byBGZWRlcmFsMRQwEgYDVQQHDAtDdWF1aHTDqW1vYzEVMBMGA1UELRMMU0FUOTcwNzAxTk4zMTUwMwYJKoZIhvcNAQkCDCZSZXNwb25zYWJsZTogQ2xhdWRpYSBDb3ZhcnJ1YmlhcyBPY2hvYTAeFw0xNDA0MDYyMTExMDJaFw0xODA0MDYyMTExMDJaMIHRMSowKAYDVQQDEyFYT0NISVRMIENPUkFMIE1FTkRJRVRBIEJFVEFOQ09VUlQxKjAoBgNVBCkTIVhPQ0hJVEwgQ09SQUwgTUVORElFVEEgQkVUQU5DT1VSVDEqMCgGA1UEChMhWE9DSElUTCBDT1JBTCBNRU5ESUVUQSBCRVRBTkNPVVJUMRYwFAYDVQQtEw1NRUJYOTExMTEwSVNBMRswGQYDVQQFExJNRUJYOTExMTEwTURGTlRDMDAxFjAUBgNVBAsTDU1FQlg5MTExMTBJU0EwgZ8wDQYJKoZIhvcNAQEBBQADgY0AMIGJAoGBAII3SaNzuQj6jBsM4pJHrOZ4XT0Bwzq9dOCY4kWgQ6WCPWXak0CnUlYkoYni8GBxhN2Uvtu4QQpCg+8e10HaQCw1lItoThQqpbRwLfxgXEgyEZ1v3KYGBju0zBeCnUjrNePPpcsGD5uolo08HW3rlc6+zfJDhOhLlXhL/oEob7rDAgMBAAGjHTAbMAwGA1UdEwEB/wQCMAAwCwYDVR0PBAQDAgbAMA0GCSqGSIb3DQEBBQUAA4IBAQDRxrGj3KsYlUar8+Mw/rDCAOrB2yXTDdoGS43hL0Hktvye98jcbhhpZLSRCxQT/Rf9WNFdb6YqfruTWwBJOYrS0OdPmAcrkrH2eG0tQKUto8XKC/iMM4xcAz9kjc+t4SveSdQHs5EihXeHOjU4e6r26X/8n4h3kZ14nqF0QjDLo08GTySKsXKe7cmBWJxAeHfrOSIsJvk80h1JGcn3KoXxAz5F7JUsoxihgbXCNSgQu7Wc8ZcdWeM6zsySz0xnjtZlRYeynwJ5S3NkZiYyZwZLk37FpZMOmGELMo8QGoxJChyklW0XzOTEYa5l9MwRJc6llePyWjVmWGzHU3ib6+BZ";
        $factura->Comprobante->subTotal                                 = "2280.00";
        $factura->Comprobante->total                                    = "2644.80";
        $factura->Comprobante->metodoDePago                             = "Transferencia Electrónica de Fondos";
        $factura->Comprobante->tipoDeComprobante                        = "ingreso";
        $factura->Comprobante->cfdi                                     = "http://www.sat.gob.mx/cfd/3";
        $factura->Comprobante->xsi                                      = "http://www.w3.org/2001/XMLSchema-instance";
        $factura->Emisor->rfc                                           = "MEBX911110ISA";
        $factura->Emisor->nombre                                        = "Xochitl Coral Mendieta Betancourt";
        $factura->Emisor->DomicilioFiscal->calle                        = "Calle. Real del Monte";
        $factura->Emisor->DomicilioFiscal->noExterior                   = "No. 95";
        $factura->Emisor->DomicilioFiscal->colonia                      = "Col. Industrial";
        $factura->Emisor->DomicilioFiscal->localidad                    = "México, D.F.";
        $factura->Emisor->DomicilioFiscal->municipio                    = "Gustavo A. Madero";
        $factura->Emisor->DomicilioFiscal->estado                       = "Distrito Federal";
        $factura->Emisor->DomicilioFiscal->pais                         = "México";
        $factura->Emisor->DomicilioFiscal->codigoPostal                 = "07800";
        $factura->Emisor->RegimenFiscal->Regimen                        = "Régimen de las Personas Físicas con Actividades Empresariales y Profesionales.";
        $factura->Receptor->rfc                                         = "IEC121203FV8";
        $factura->Receptor->nombre                                      = "INTEGRADORA DE EMPRENDIMIENTOS CULTURALES SA DE CV";
        $factura->Receptor->Domicilio->calle                            = "TIBURCIO MONTIEL";
        $factura->Receptor->Domicilio->noExterior                       = "80";
        $factura->Receptor->Domicilio->noInterior                       = "INTERIOR B-3";
        $factura->Receptor->Domicilio->colonia                          = "COL. SAN MIGUEL CHAPULTEPEC";
        $factura->Receptor->Domicilio->localidad                        = "DISTRITO FEDERAL";
        $factura->Receptor->Domicilio->municipio                        = "Miguel Hidalgo";
        $factura->Receptor->Domicilio->estado                           = "Distrito Federal";
        $factura->Receptor->Domicilio->pais                             = "México";
        $factura->Receptor->codigoPostal                                = "11850";
        $factura->Conceptos->Concepto->cantidad                         = "1";
        $factura->Conceptos->Concepto->unidad                           = "No Aplica";
        $factura->Conceptos->Concepto->descripcion                      = "Coordinador ";
        $factura->Conceptos->Concepto->valorUnitario                    = "720";
        $factura->Conceptos->Concepto->importe                          = "720.00";
        $factura->Conceptos->Concepto->cantidad                         = "3";
        $factura->Conceptos->Concepto->exitunidad                       = "No Aplica";
        $factura->Conceptos->Concepto->descripcion                      = "Acomodadores";
        $factura->Conceptos->Concepto->valorUnitario                    = "520";
        $factura->Conceptos->Concepto->importe                          = "1560.00";
        $factura->Impuestos->totalImpuestosTrasladados                  ="364.80";
        $factura->Impuestos->Traslados->Traslado->impuesto              = "IVA";
        $factura->Impuestos->Traslados->Traslado->tasa                  = "16";
        $factura->Impuestos->Traslados->Traslado->importe               = "364.80";
        $factura->Complemento->TimbreFiscalDigital->schemaLocation      = "http://www.sat.gob.mx/TimbreFiscalDigital http://www.sat.gob.mx/TimbreFiscalDigital/TimbreFiscalDigital.xsd";
        $factura->Complemento->TimbreFiscalDigital->version             = "1.0";
        $factura->Complemento->TimbreFiscalDigital->UUID                = "101EB734-47C3-47FA-BBCD-C6D167648B26";
        $factura->Complemento->TimbreFiscalDigital->FechaTimbrado       = "2014-05-14T08:46:24";
        $factura->Complemento->TimbreFiscalDigital->selloCFD            = "LvBPRXayjjhrxNg5ulHqEF1Y89Dq8nz9c7hifOzH0C6r8iwODBf6h7P+ACbRgh+QPBtgnqjvlXn10Zw9WuWDVr+rAgkenFxC+VNST/2AHGr29zqa7r/FnENvtSjecClfJAy4GysstHXWm0BQQilfrYHZvu/qUZidnZVscr07jRU=";
        $factura->Complemento->TimbreFiscalDigital->noCertificadoSAT    = "00001000000202864883";
        $factura->Complemento->TimbreFiscalDigital->selloSAT            = "VZ4eWlWWfHkfUWv/2XGH4M4NEJujW7Vsm7EGe4IH/Ok02coXGgOUK667QDLMiDtG5e+fh4xXHCjbbLScvuKmdRJbRwbouWp/gfcgtRDK7c/akr0nn+miRWOS8XcdhtJ9SNxkQKCWZx26D1wD6XEMqV93YG9/M/yPfLiexa9QAyY=";
        $factura->Complemento->TimbreFiscalDigital->tfd                 = "http://www.sat.gob.mx/TimbreFiscalDigital";
        $factura->Complemento->TimbreFiscalDigital->xsi                 = "http://www.w3.org/2001/XMLSchema-instance";


        $array[]                                                        = $factura;


        $factura 				                                        = new stdClass();
        $factura->id                                                    = 2;
        $factura ->integradoId                                          = 2;
        $factura->status                                                = 0;
        $factura->Comprobante->schemaLocation                           = "http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv32.xsd";
        $factura->Comprobante->version                                  = "3.2";
        $factura->Comprobante->serie                                    = "120 - ";
        $factura->Comprobante->folio                                    = "1120";
        $factura->Comprobante->LugarExpedicion                          = "México, D.F.";
        $factura->Comprobante->NumCtaPago                               = "012180001931017464 BANCOMER";
        $factura->Comprobante->TipoCambio                               = "1";
        $factura->Comprobante->Moneda                                   = "Pesos Mexicanos";
        $factura->Comprobante->fecha                                    = "2014-05-13T08:46:22";
        $factura->Comprobante->sello                                    = "LvBPRXayjjhrxNg5ulHqEF1Y89Dq8nz9c7hifOzH0C6r8iwODBf6h7P+ACbRgh+QPBtgnqjvlXn10Zw9WuWDVr+rAgkenFxC+VNST/2AHGr29zqa7r/FnENvtSjecClfJAy4GysstHXWm0BQQilfrYHZvu/qUZidnZVscr07jRU=";
        $factura->Comprobante->formaDePago                              = "Pago en una sola exhibición";
        $factura->Comprobante->noCertificado                            = "00001000000303670260";
        $factura->Comprobante->certificado                              = "MIIEhTCCA22gAwIBAgIUMDAwMDEwMDAwMDAzMDM2NzAyNjAwDQYJKoZIhvcNAQEFBQAwggGKMTgwNgYDVQQDDC9BLkMuIGRlbCBTZXJ2aWNpbyBkZSBBZG1pbmlzdHJhY2nDs24gVHJpYnV0YXJpYTEvMC0GA1UECgwmU2VydmljaW8gZGUgQWRtaW5pc3RyYWNpw7NuIFRyaWJ1dGFyaWExODA2BgNVBAsML0FkbWluaXN0cmFjacOzbiBkZSBTZWd1cmlkYWQgZGUgbGEgSW5mb3JtYWNpw7NuMR8wHQYJKoZIhvcNAQkBFhBhY29kc0BzYXQuZ29iLm14MSYwJAYDVQQJDB1Bdi4gSGlkYWxnbyA3NywgQ29sLiBHdWVycmVybzEOMAwGA1UEEQwFMDYzMDAxCzAJBgNVBAYTAk1YMRkwFwYDVQQIDBBEaXN0cml0byBGZWRlcmFsMRQwEgYDVQQHDAtDdWF1aHTDqW1vYzEVMBMGA1UELRMMU0FUOTcwNzAxTk4zMTUwMwYJKoZIhvcNAQkCDCZSZXNwb25zYWJsZTogQ2xhdWRpYSBDb3ZhcnJ1YmlhcyBPY2hvYTAeFw0xNDA0MDYyMTExMDJaFw0xODA0MDYyMTExMDJaMIHRMSowKAYDVQQDEyFYT0NISVRMIENPUkFMIE1FTkRJRVRBIEJFVEFOQ09VUlQxKjAoBgNVBCkTIVhPQ0hJVEwgQ09SQUwgTUVORElFVEEgQkVUQU5DT1VSVDEqMCgGA1UEChMhWE9DSElUTCBDT1JBTCBNRU5ESUVUQSBCRVRBTkNPVVJUMRYwFAYDVQQtEw1NRUJYOTExMTEwSVNBMRswGQYDVQQFExJNRUJYOTExMTEwTURGTlRDMDAxFjAUBgNVBAsTDU1FQlg5MTExMTBJU0EwgZ8wDQYJKoZIhvcNAQEBBQADgY0AMIGJAoGBAII3SaNzuQj6jBsM4pJHrOZ4XT0Bwzq9dOCY4kWgQ6WCPWXak0CnUlYkoYni8GBxhN2Uvtu4QQpCg+8e10HaQCw1lItoThQqpbRwLfxgXEgyEZ1v3KYGBju0zBeCnUjrNePPpcsGD5uolo08HW3rlc6+zfJDhOhLlXhL/oEob7rDAgMBAAGjHTAbMAwGA1UdEwEB/wQCMAAwCwYDVR0PBAQDAgbAMA0GCSqGSIb3DQEBBQUAA4IBAQDRxrGj3KsYlUar8+Mw/rDCAOrB2yXTDdoGS43hL0Hktvye98jcbhhpZLSRCxQT/Rf9WNFdb6YqfruTWwBJOYrS0OdPmAcrkrH2eG0tQKUto8XKC/iMM4xcAz9kjc+t4SveSdQHs5EihXeHOjU4e6r26X/8n4h3kZ14nqF0QjDLo08GTySKsXKe7cmBWJxAeHfrOSIsJvk80h1JGcn3KoXxAz5F7JUsoxihgbXCNSgQu7Wc8ZcdWeM6zsySz0xnjtZlRYeynwJ5S3NkZiYyZwZLk37FpZMOmGELMo8QGoxJChyklW0XzOTEYa5l9MwRJc6llePyWjVmWGzHU3ib6+BZ";
        $factura->Comprobante->subTotal                                 = "2280.00";
        $factura->Comprobante->total                                    = "2644.80";
        $factura->Comprobante->metodoDePago                             = "Transferencia Electrónica de Fondos";
        $factura->Comprobante->tipoDeComprobante                        = "ingreso";
        $factura->Comprobante->cfdi                                     = "http://www.sat.gob.mx/cfd/3";
        $factura->Comprobante->xsi                                      = "http://www.w3.org/2001/XMLSchema-instance";
        $factura->Emisor->rfc                                           = "MEBX911110ISA";
        $factura->Emisor->nombre                                        = "Xochitl Coral Mendieta Betancourt";
        $factura->Emisor->DomicilioFiscal->calle                        = "Calle. Real del Monte";
        $factura->Emisor->DomicilioFiscal->noExterior                   = "No. 95";
        $factura->Emisor->DomicilioFiscal->colonia                      = "Col. Industrial";
        $factura->Emisor->DomicilioFiscal->localidad                    = "México, D.F.";
        $factura->Emisor->DomicilioFiscal->municipio                    = "Gustavo A. Madero";
        $factura->Emisor->DomicilioFiscal->estado                       = "Distrito Federal";
        $factura->Emisor->DomicilioFiscal->pais                         = "México";
        $factura->Emisor->DomicilioFiscal->codigoPostal                 = "07800";
        $factura->Emisor->RegimenFiscal->Regimen                        = "Régimen de las Personas Físicas con Actividades Empresariales y Profesionales.";
        $factura->Receptor->rfc                                         = "IEC121203FV8";
        $factura->Receptor->nombre                                      = "INTEGRADORA DE EMPRENDIMIENTOS CULTURALES SA DE CV";
        $factura->Receptor->Domicilio->calle                            = "TIBURCIO MONTIEL";
        $factura->Receptor->Domicilio->noExterior                       = "80";
        $factura->Receptor->Domicilio->noInterior                       = "INTERIOR B-3";
        $factura->Receptor->Domicilio->colonia                          = "COL. SAN MIGUEL CHAPULTEPEC";
        $factura->Receptor->Domicilio->localidad                        = "DISTRITO FEDERAL";
        $factura->Receptor->Domicilio->municipio                        = "Miguel Hidalgo";
        $factura->Receptor->Domicilio->estado                           = "Distrito Federal";
        $factura->Receptor->Domicilio->pais                             = "México";
        $factura->Receptor->codigoPostal                                = "11850";
        $factura->Conceptos->Concepto->cantidad                         = "1";
        $factura->Conceptos->Concepto->unidad                           = "No Aplica";
        $factura->Conceptos->Concepto->descripcion                      = "Coordinador ";
        $factura->Conceptos->Concepto->valorUnitario                    = "720";
        $factura->Conceptos->Concepto->importe                          = "720.00";
        $factura->Conceptos->Concepto->cantidad                         = "3";
        $factura->Conceptos->Concepto->exitunidad                       = "No Aplica";
        $factura->Conceptos->Concepto->descripcion                      = "Acomodadores";
        $factura->Conceptos->Concepto->valorUnitario                    = "520";
        $factura->Conceptos->Concepto->importe                          = "1560.00";
        $factura->Impuestos->totalImpuestosTrasladados                  ="364.80";
        $factura->Impuestos->Traslados->Traslado->impuesto              = "IVA";
        $factura->Impuestos->Traslados->Traslado->tasa                  = "16";
        $factura->Impuestos->Traslados->Traslado->importe               = "364.80";
        $factura->Complemento->TimbreFiscalDigital->schemaLocation      = "http://www.sat.gob.mx/TimbreFiscalDigital http://www.sat.gob.mx/TimbreFiscalDigital/TimbreFiscalDigital.xsd";
        $factura->Complemento->TimbreFiscalDigital->version             = "1.0";
        $factura->Complemento->TimbreFiscalDigital->UUID                = "101EB734-47C3-47FA-BBCD-C6D167648B26";
        $factura->Complemento->TimbreFiscalDigital->FechaTimbrado       = "2014-05-14T08:46:24";
        $factura->Complemento->TimbreFiscalDigital->selloCFD            = "LvBPRXayjjhrxNg5ulHqEF1Y89Dq8nz9c7hifOzH0C6r8iwODBf6h7P+ACbRgh+QPBtgnqjvlXn10Zw9WuWDVr+rAgkenFxC+VNST/2AHGr29zqa7r/FnENvtSjecClfJAy4GysstHXWm0BQQilfrYHZvu/qUZidnZVscr07jRU=";
        $factura->Complemento->TimbreFiscalDigital->noCertificadoSAT    = "00001000000202864883";
        $factura->Complemento->TimbreFiscalDigital->selloSAT            = "VZ4eWlWWfHkfUWv/2XGH4M4NEJujW7Vsm7EGe4IH/Ok02coXGgOUK667QDLMiDtG5e+fh4xXHCjbbLScvuKmdRJbRwbouWp/gfcgtRDK7c/akr0nn+miRWOS8XcdhtJ9SNxkQKCWZx26D1wD6XEMqV93YG9/M/yPfLiexa9QAyY=";
        $factura->Complemento->TimbreFiscalDigital->tfd                 = "http://www.sat.gob.mx/TimbreFiscalDigital";
        $factura->Complemento->TimbreFiscalDigital->xsi                 = "http://www.w3.org/2001/XMLSchema-instance";


        $array[]                                                        = $factura;


        $factura 				                                        = new stdClass();
        $factura->id                                                    = 3;
        $factura ->integradoId                                          = 3;
        $factura->status                                                = 0;
        $factura->Comprobante->schemaLocation                           = "http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv32.xsd";
        $factura->Comprobante->version                                  = "3.2";
        $factura->Comprobante->serie                                    = "150 - ";
        $factura->Comprobante->folio                                    = "450";
        $factura->Comprobante->LugarExpedicion                          = "México, D.F.";
        $factura->Comprobante->NumCtaPago                               = "012180001931017464 BANCOMER";
        $factura->Comprobante->TipoCambio                               = "1";
        $factura->Comprobante->Moneda                                   = "Pesos Mexicanos";
        $factura->Comprobante->fecha                                    = "2013-05-14T08:46:22";
        $factura->Comprobante->sello                                    = "LvBPRXayjjhrxNg5ulHqEF1Y89Dq8nz9c7hifOzH0C6r8iwODBf6h7P+ACbRgh+QPBtgnqjvlXn10Zw9WuWDVr+rAgkenFxC+VNST/2AHGr29zqa7r/FnENvtSjecClfJAy4GysstHXWm0BQQilfrYHZvu/qUZidnZVscr07jRU=";
        $factura->Comprobante->formaDePago                              = "Pago en una sola exhibición";
        $factura->Comprobante->noCertificado                            = "00001000000303670260";
        $factura->Comprobante->certificado                              = "MIIEhTCCA22gAwIBAgIUMDAwMDEwMDAwMDAzMDM2NzAyNjAwDQYJKoZIhvcNAQEFBQAwggGKMTgwNgYDVQQDDC9BLkMuIGRlbCBTZXJ2aWNpbyBkZSBBZG1pbmlzdHJhY2nDs24gVHJpYnV0YXJpYTEvMC0GA1UECgwmU2VydmljaW8gZGUgQWRtaW5pc3RyYWNpw7NuIFRyaWJ1dGFyaWExODA2BgNVBAsML0FkbWluaXN0cmFjacOzbiBkZSBTZWd1cmlkYWQgZGUgbGEgSW5mb3JtYWNpw7NuMR8wHQYJKoZIhvcNAQkBFhBhY29kc0BzYXQuZ29iLm14MSYwJAYDVQQJDB1Bdi4gSGlkYWxnbyA3NywgQ29sLiBHdWVycmVybzEOMAwGA1UEEQwFMDYzMDAxCzAJBgNVBAYTAk1YMRkwFwYDVQQIDBBEaXN0cml0byBGZWRlcmFsMRQwEgYDVQQHDAtDdWF1aHTDqW1vYzEVMBMGA1UELRMMU0FUOTcwNzAxTk4zMTUwMwYJKoZIhvcNAQkCDCZSZXNwb25zYWJsZTogQ2xhdWRpYSBDb3ZhcnJ1YmlhcyBPY2hvYTAeFw0xNDA0MDYyMTExMDJaFw0xODA0MDYyMTExMDJaMIHRMSowKAYDVQQDEyFYT0NISVRMIENPUkFMIE1FTkRJRVRBIEJFVEFOQ09VUlQxKjAoBgNVBCkTIVhPQ0hJVEwgQ09SQUwgTUVORElFVEEgQkVUQU5DT1VSVDEqMCgGA1UEChMhWE9DSElUTCBDT1JBTCBNRU5ESUVUQSBCRVRBTkNPVVJUMRYwFAYDVQQtEw1NRUJYOTExMTEwSVNBMRswGQYDVQQFExJNRUJYOTExMTEwTURGTlRDMDAxFjAUBgNVBAsTDU1FQlg5MTExMTBJU0EwgZ8wDQYJKoZIhvcNAQEBBQADgY0AMIGJAoGBAII3SaNzuQj6jBsM4pJHrOZ4XT0Bwzq9dOCY4kWgQ6WCPWXak0CnUlYkoYni8GBxhN2Uvtu4QQpCg+8e10HaQCw1lItoThQqpbRwLfxgXEgyEZ1v3KYGBju0zBeCnUjrNePPpcsGD5uolo08HW3rlc6+zfJDhOhLlXhL/oEob7rDAgMBAAGjHTAbMAwGA1UdEwEB/wQCMAAwCwYDVR0PBAQDAgbAMA0GCSqGSIb3DQEBBQUAA4IBAQDRxrGj3KsYlUar8+Mw/rDCAOrB2yXTDdoGS43hL0Hktvye98jcbhhpZLSRCxQT/Rf9WNFdb6YqfruTWwBJOYrS0OdPmAcrkrH2eG0tQKUto8XKC/iMM4xcAz9kjc+t4SveSdQHs5EihXeHOjU4e6r26X/8n4h3kZ14nqF0QjDLo08GTySKsXKe7cmBWJxAeHfrOSIsJvk80h1JGcn3KoXxAz5F7JUsoxihgbXCNSgQu7Wc8ZcdWeM6zsySz0xnjtZlRYeynwJ5S3NkZiYyZwZLk37FpZMOmGELMo8QGoxJChyklW0XzOTEYa5l9MwRJc6llePyWjVmWGzHU3ib6+BZ";
        $factura->Comprobante->subTotal                                 = "2280.00";
        $factura->Comprobante->total                                    = "2644.80";
        $factura->Comprobante->metodoDePago                             = "Transferencia Electrónica de Fondos";
        $factura->Comprobante->tipoDeComprobante                        = "ingreso";
        $factura->Comprobante->cfdi                                     = "http://www.sat.gob.mx/cfd/3";
        $factura->Comprobante->xsi                                      = "http://www.w3.org/2001/XMLSchema-instance";
        $factura->Emisor->rfc                                           = "MEBX911110ISA";
        $factura->Emisor->nombre                                        = "Xochitl Coral Mendieta Betancourt";
        $factura->Emisor->DomicilioFiscal->calle                        = "Calle. Real del Monte";
        $factura->Emisor->DomicilioFiscal->noExterior                   = "No. 95";
        $factura->Emisor->DomicilioFiscal->colonia                      = "Col. Industrial";
        $factura->Emisor->DomicilioFiscal->localidad                    = "México, D.F.";
        $factura->Emisor->DomicilioFiscal->municipio                    = "Gustavo A. Madero";
        $factura->Emisor->DomicilioFiscal->estado                       = "Distrito Federal";
        $factura->Emisor->DomicilioFiscal->pais                         = "México";
        $factura->Emisor->DomicilioFiscal->codigoPostal                 = "07800";
        $factura->Emisor->RegimenFiscal->Regimen                        = "Régimen de las Personas Físicas con Actividades Empresariales y Profesionales.";
        $factura->Receptor->rfc                                         = "IEC121203FV8";
        $factura->Receptor->nombre                                      = "INTEGRADORA DE EMPRENDIMIENTOS CULTURALES SA DE CV";
        $factura->Receptor->Domicilio->calle                            = "TIBURCIO MONTIEL";
        $factura->Receptor->Domicilio->noExterior                       = "80";
        $factura->Receptor->Domicilio->noInterior                       = "INTERIOR B-3";
        $factura->Receptor->Domicilio->colonia                          = "COL. SAN MIGUEL CHAPULTEPEC";
        $factura->Receptor->Domicilio->localidad                        = "DISTRITO FEDERAL";
        $factura->Receptor->Domicilio->municipio                        = "Miguel Hidalgo";
        $factura->Receptor->Domicilio->estado                           = "Distrito Federal";
        $factura->Receptor->Domicilio->pais                             = "México";
        $factura->Receptor->codigoPostal                                = "11850";
        $factura->Conceptos->Concepto->cantidad                         = "1";
        $factura->Conceptos->Concepto->unidad                           = "No Aplica";
        $factura->Conceptos->Concepto->descripcion                      = "Coordinador ";
        $factura->Conceptos->Concepto->valorUnitario                    = "720";
        $factura->Conceptos->Concepto->importe                          = "720.00";
        $factura->Conceptos->Concepto->cantidad                         = "3";
        $factura->Conceptos->Concepto->exitunidad                       = "No Aplica";
        $factura->Conceptos->Concepto->descripcion                      = "Acomodadores";
        $factura->Conceptos->Concepto->valorUnitario                    = "520";
        $factura->Conceptos->Concepto->importe                          = "1560.00";
        $factura->Impuestos->totalImpuestosTrasladados                  ="364.80";
        $factura->Impuestos->Traslados->Traslado->impuesto              = "IVA";
        $factura->Impuestos->Traslados->Traslado->tasa                  = "16";
        $factura->Impuestos->Traslados->Traslado->importe               = "364.80";
        $factura->Complemento->TimbreFiscalDigital->schemaLocation      = "http://www.sat.gob.mx/TimbreFiscalDigital http://www.sat.gob.mx/TimbreFiscalDigital/TimbreFiscalDigital.xsd";
        $factura->Complemento->TimbreFiscalDigital->version             = "1.0";
        $factura->Complemento->TimbreFiscalDigital->UUID                = "101EB734-47C3-47FA-BBCD-C6D167648B26";
        $factura->Complemento->TimbreFiscalDigital->FechaTimbrado       = "2014-05-14T08:46:24";
        $factura->Complemento->TimbreFiscalDigital->selloCFD            = "LvBPRXayjjhrxNg5ulHqEF1Y89Dq8nz9c7hifOzH0C6r8iwODBf6h7P+ACbRgh+QPBtgnqjvlXn10Zw9WuWDVr+rAgkenFxC+VNST/2AHGr29zqa7r/FnENvtSjecClfJAy4GysstHXWm0BQQilfrYHZvu/qUZidnZVscr07jRU=";
        $factura->Complemento->TimbreFiscalDigital->noCertificadoSAT    = "00001000000202864883";
        $factura->Complemento->TimbreFiscalDigital->selloSAT            = "VZ4eWlWWfHkfUWv/2XGH4M4NEJujW7Vsm7EGe4IH/Ok02coXGgOUK667QDLMiDtG5e+fh4xXHCjbbLScvuKmdRJbRwbouWp/gfcgtRDK7c/akr0nn+miRWOS8XcdhtJ9SNxkQKCWZx26D1wD6XEMqV93YG9/M/yPfLiexa9QAyY=";
        $factura->Complemento->TimbreFiscalDigital->tfd                 = "http://www.sat.gob.mx/TimbreFiscalDigital";
        $factura->Complemento->TimbreFiscalDigital->xsi                 = "http://www.w3.org/2001/XMLSchema-instance";


        $array[]                                                        = $factura;

        $factura 				                                        = new stdClass();
        $factura->id                                                    = 4;
        $factura ->integradoId                                          = 2;
        $factura->status                                                = 0;
        $factura->Comprobante->schemaLocation                           = "http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv32.xsd";
        $factura->Comprobante->version                                  = "3.2";
        $factura->Comprobante->serie                                    = "1010 - ";
        $factura->Comprobante->folio                                    = "11220";
        $factura->Comprobante->LugarExpedicion                          = "México, D.F.";
        $factura->Comprobante->NumCtaPago                               = "012180001931017464 BANCOMER";
        $factura->Comprobante->TipoCambio                               = "1";
        $factura->Comprobante->Moneda                                   = "Pesos Mexicanos";
        $factura->Comprobante->fecha                                    = "2011-07-14T08:46:22";
        $factura->Comprobante->sello                                    = "LvBPRXayjjhrxNg5ulHqEF1Y89Dq8nz9c7hifOzH0C6r8iwODBf6h7P+ACbRgh+QPBtgnqjvlXn10Zw9WuWDVr+rAgkenFxC+VNST/2AHGr29zqa7r/FnENvtSjecClfJAy4GysstHXWm0BQQilfrYHZvu/qUZidnZVscr07jRU=";
        $factura->Comprobante->formaDePago                              = "Pago en una sola exhibición";
        $factura->Comprobante->noCertificado                            = "00001000000303670260";
        $factura->Comprobante->certificado                              = "MIIEhTCCA22gAwIBAgIUMDAwMDEwMDAwMDAzMDM2NzAyNjAwDQYJKoZIhvcNAQEFBQAwggGKMTgwNgYDVQQDDC9BLkMuIGRlbCBTZXJ2aWNpbyBkZSBBZG1pbmlzdHJhY2nDs24gVHJpYnV0YXJpYTEvMC0GA1UECgwmU2VydmljaW8gZGUgQWRtaW5pc3RyYWNpw7NuIFRyaWJ1dGFyaWExODA2BgNVBAsML0FkbWluaXN0cmFjacOzbiBkZSBTZWd1cmlkYWQgZGUgbGEgSW5mb3JtYWNpw7NuMR8wHQYJKoZIhvcNAQkBFhBhY29kc0BzYXQuZ29iLm14MSYwJAYDVQQJDB1Bdi4gSGlkYWxnbyA3NywgQ29sLiBHdWVycmVybzEOMAwGA1UEEQwFMDYzMDAxCzAJBgNVBAYTAk1YMRkwFwYDVQQIDBBEaXN0cml0byBGZWRlcmFsMRQwEgYDVQQHDAtDdWF1aHTDqW1vYzEVMBMGA1UELRMMU0FUOTcwNzAxTk4zMTUwMwYJKoZIhvcNAQkCDCZSZXNwb25zYWJsZTogQ2xhdWRpYSBDb3ZhcnJ1YmlhcyBPY2hvYTAeFw0xNDA0MDYyMTExMDJaFw0xODA0MDYyMTExMDJaMIHRMSowKAYDVQQDEyFYT0NISVRMIENPUkFMIE1FTkRJRVRBIEJFVEFOQ09VUlQxKjAoBgNVBCkTIVhPQ0hJVEwgQ09SQUwgTUVORElFVEEgQkVUQU5DT1VSVDEqMCgGA1UEChMhWE9DSElUTCBDT1JBTCBNRU5ESUVUQSBCRVRBTkNPVVJUMRYwFAYDVQQtEw1NRUJYOTExMTEwSVNBMRswGQYDVQQFExJNRUJYOTExMTEwTURGTlRDMDAxFjAUBgNVBAsTDU1FQlg5MTExMTBJU0EwgZ8wDQYJKoZIhvcNAQEBBQADgY0AMIGJAoGBAII3SaNzuQj6jBsM4pJHrOZ4XT0Bwzq9dOCY4kWgQ6WCPWXak0CnUlYkoYni8GBxhN2Uvtu4QQpCg+8e10HaQCw1lItoThQqpbRwLfxgXEgyEZ1v3KYGBju0zBeCnUjrNePPpcsGD5uolo08HW3rlc6+zfJDhOhLlXhL/oEob7rDAgMBAAGjHTAbMAwGA1UdEwEB/wQCMAAwCwYDVR0PBAQDAgbAMA0GCSqGSIb3DQEBBQUAA4IBAQDRxrGj3KsYlUar8+Mw/rDCAOrB2yXTDdoGS43hL0Hktvye98jcbhhpZLSRCxQT/Rf9WNFdb6YqfruTWwBJOYrS0OdPmAcrkrH2eG0tQKUto8XKC/iMM4xcAz9kjc+t4SveSdQHs5EihXeHOjU4e6r26X/8n4h3kZ14nqF0QjDLo08GTySKsXKe7cmBWJxAeHfrOSIsJvk80h1JGcn3KoXxAz5F7JUsoxihgbXCNSgQu7Wc8ZcdWeM6zsySz0xnjtZlRYeynwJ5S3NkZiYyZwZLk37FpZMOmGELMo8QGoxJChyklW0XzOTEYa5l9MwRJc6llePyWjVmWGzHU3ib6+BZ";
        $factura->Comprobante->subTotal                                 = "2280.00";
        $factura->Comprobante->total                                    = "2644.80";
        $factura->Comprobante->metodoDePago                             = "Transferencia Electrónica de Fondos";
        $factura->Comprobante->tipoDeComprobante                        = "ingreso";
        $factura->Comprobante->cfdi                                     = "http://www.sat.gob.mx/cfd/3";
        $factura->Comprobante->xsi                                      = "http://www.w3.org/2001/XMLSchema-instance";
        $factura->Emisor->rfc                                           = "MEBX911110ISA";
        $factura->Emisor->nombre                                        = "Xochitl Coral Mendieta Betancourt";
        $factura->Emisor->DomicilioFiscal->calle                        = "Calle. Real del Monte";
        $factura->Emisor->DomicilioFiscal->noExterior                   = "No. 95";
        $factura->Emisor->DomicilioFiscal->colonia                      = "Col. Industrial";
        $factura->Emisor->DomicilioFiscal->localidad                    = "México, D.F.";
        $factura->Emisor->DomicilioFiscal->municipio                    = "Gustavo A. Madero";
        $factura->Emisor->DomicilioFiscal->estado                       = "Distrito Federal";
        $factura->Emisor->DomicilioFiscal->pais                         = "México";
        $factura->Emisor->DomicilioFiscal->codigoPostal                 = "07800";
        $factura->Emisor->RegimenFiscal->Regimen                        = "Régimen de las Personas Físicas con Actividades Empresariales y Profesionales.";
        $factura->Receptor->rfc                                         = "IEC121203FV8";
        $factura->Receptor->nombre                                      = "INTEGRADORA DE EMPRENDIMIENTOS CULTURALES SA DE CV";
        $factura->Receptor->Domicilio->calle                            = "TIBURCIO MONTIEL";
        $factura->Receptor->Domicilio->noExterior                       = "80";
        $factura->Receptor->Domicilio->noInterior                       = "INTERIOR B-3";
        $factura->Receptor->Domicilio->colonia                          = "COL. SAN MIGUEL CHAPULTEPEC";
        $factura->Receptor->Domicilio->localidad                        = "DISTRITO FEDERAL";
        $factura->Receptor->Domicilio->municipio                        = "Miguel Hidalgo";
        $factura->Receptor->Domicilio->estado                           = "Distrito Federal";
        $factura->Receptor->Domicilio->pais                             = "México";
        $factura->Receptor->codigoPostal                                = "11850";
        $factura->Conceptos->Concepto->cantidad                         = "1";
        $factura->Conceptos->Concepto->unidad                           = "No Aplica";
        $factura->Conceptos->Concepto->descripcion                      = "Coordinador ";
        $factura->Conceptos->Concepto->valorUnitario                    = "720";
        $factura->Conceptos->Concepto->importe                          = "720.00";
        $factura->Conceptos->Concepto->cantidad                         = "3";
        $factura->Conceptos->Concepto->exitunidad                       = "No Aplica";
        $factura->Conceptos->Concepto->descripcion                      = "Acomodadores";
        $factura->Conceptos->Concepto->valorUnitario                    = "520";
        $factura->Conceptos->Concepto->importe                          = "1560.00";
        $factura->Impuestos->totalImpuestosTrasladados                  ="364.80";
        $factura->Impuestos->Traslados->Traslado->impuesto              = "IVA";
        $factura->Impuestos->Traslados->Traslado->tasa                  = "16";
        $factura->Impuestos->Traslados->Traslado->importe               = "364.80";
        $factura->Complemento->TimbreFiscalDigital->schemaLocation      = "http://www.sat.gob.mx/TimbreFiscalDigital http://www.sat.gob.mx/TimbreFiscalDigital/TimbreFiscalDigital.xsd";
        $factura->Complemento->TimbreFiscalDigital->version             = "1.0";
        $factura->Complemento->TimbreFiscalDigital->UUID                = "101EB734-47C3-47FA-BBCD-C6D167648B26";
        $factura->Complemento->TimbreFiscalDigital->FechaTimbrado       = "2014-05-14T08:46:24";
        $factura->Complemento->TimbreFiscalDigital->selloCFD            = "LvBPRXayjjhrxNg5ulHqEF1Y89Dq8nz9c7hifOzH0C6r8iwODBf6h7P+ACbRgh+QPBtgnqjvlXn10Zw9WuWDVr+rAgkenFxC+VNST/2AHGr29zqa7r/FnENvtSjecClfJAy4GysstHXWm0BQQilfrYHZvu/qUZidnZVscr07jRU=";
        $factura->Complemento->TimbreFiscalDigital->noCertificadoSAT    = "00001000000202864883";
        $factura->Complemento->TimbreFiscalDigital->selloSAT            = "VZ4eWlWWfHkfUWv/2XGH4M4NEJujW7Vsm7EGe4IH/Ok02coXGgOUK667QDLMiDtG5e+fh4xXHCjbbLScvuKmdRJbRwbouWp/gfcgtRDK7c/akr0nn+miRWOS8XcdhtJ9SNxkQKCWZx26D1wD6XEMqV93YG9/M/yPfLiexa9QAyY=";
        $factura->Complemento->TimbreFiscalDigital->tfd                 = "http://www.sat.gob.mx/TimbreFiscalDigital";
        $factura->Complemento->TimbreFiscalDigital->xsi                 = "http://www.w3.org/2001/XMLSchema-instance";


        $array[]                                                        = $factura;


        $factura 				                                        = new stdClass();
        $factura->id                                                    = 5;
        $factura ->integradoId                                          = 1;
        $factura->status                                                = 1;
        $factura->Comprobante->schemaLocation                           = "http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv32.xsd";
        $factura->Comprobante->version                                  = "3.2";
        $factura->Comprobante->serie                                    = "110 - ";
        $factura->Comprobante->folio                                    = "120";
        $factura->Comprobante->LugarExpedicion                          = "México, D.F.";
        $factura->Comprobante->NumCtaPago                               = "012180001931017464 BANCOMER";
        $factura->Comprobante->TipoCambio                               = "1";
        $factura->Comprobante->Moneda                                   = "Pesos Mexicanos";
        $factura->Comprobante->fecha                                    = "2014-05-24T08:46:22";
        $factura->Comprobante->sello                                    = "LvBPRXayjjhrxNg5ulHqEF1Y89Dq8nz9c7hifOzH0C6r8iwODBf6h7P+ACbRgh+QPBtgnqjvlXn10Zw9WuWDVr+rAgkenFxC+VNST/2AHGr29zqa7r/FnENvtSjecClfJAy4GysstHXWm0BQQilfrYHZvu/qUZidnZVscr07jRU=";
        $factura->Comprobante->formaDePago                              = "Pago en una sola exhibición";
        $factura->Comprobante->noCertificado                            = "00001000000303670260";
        $factura->Comprobante->certificado                              = "MIIEhTCCA22gAwIBAgIUMDAwMDEwMDAwMDAzMDM2NzAyNjAwDQYJKoZIhvcNAQEFBQAwggGKMTgwNgYDVQQDDC9BLkMuIGRlbCBTZXJ2aWNpbyBkZSBBZG1pbmlzdHJhY2nDs24gVHJpYnV0YXJpYTEvMC0GA1UECgwmU2VydmljaW8gZGUgQWRtaW5pc3RyYWNpw7NuIFRyaWJ1dGFyaWExODA2BgNVBAsML0FkbWluaXN0cmFjacOzbiBkZSBTZWd1cmlkYWQgZGUgbGEgSW5mb3JtYWNpw7NuMR8wHQYJKoZIhvcNAQkBFhBhY29kc0BzYXQuZ29iLm14MSYwJAYDVQQJDB1Bdi4gSGlkYWxnbyA3NywgQ29sLiBHdWVycmVybzEOMAwGA1UEEQwFMDYzMDAxCzAJBgNVBAYTAk1YMRkwFwYDVQQIDBBEaXN0cml0byBGZWRlcmFsMRQwEgYDVQQHDAtDdWF1aHTDqW1vYzEVMBMGA1UELRMMU0FUOTcwNzAxTk4zMTUwMwYJKoZIhvcNAQkCDCZSZXNwb25zYWJsZTogQ2xhdWRpYSBDb3ZhcnJ1YmlhcyBPY2hvYTAeFw0xNDA0MDYyMTExMDJaFw0xODA0MDYyMTExMDJaMIHRMSowKAYDVQQDEyFYT0NISVRMIENPUkFMIE1FTkRJRVRBIEJFVEFOQ09VUlQxKjAoBgNVBCkTIVhPQ0hJVEwgQ09SQUwgTUVORElFVEEgQkVUQU5DT1VSVDEqMCgGA1UEChMhWE9DSElUTCBDT1JBTCBNRU5ESUVUQSBCRVRBTkNPVVJUMRYwFAYDVQQtEw1NRUJYOTExMTEwSVNBMRswGQYDVQQFExJNRUJYOTExMTEwTURGTlRDMDAxFjAUBgNVBAsTDU1FQlg5MTExMTBJU0EwgZ8wDQYJKoZIhvcNAQEBBQADgY0AMIGJAoGBAII3SaNzuQj6jBsM4pJHrOZ4XT0Bwzq9dOCY4kWgQ6WCPWXak0CnUlYkoYni8GBxhN2Uvtu4QQpCg+8e10HaQCw1lItoThQqpbRwLfxgXEgyEZ1v3KYGBju0zBeCnUjrNePPpcsGD5uolo08HW3rlc6+zfJDhOhLlXhL/oEob7rDAgMBAAGjHTAbMAwGA1UdEwEB/wQCMAAwCwYDVR0PBAQDAgbAMA0GCSqGSIb3DQEBBQUAA4IBAQDRxrGj3KsYlUar8+Mw/rDCAOrB2yXTDdoGS43hL0Hktvye98jcbhhpZLSRCxQT/Rf9WNFdb6YqfruTWwBJOYrS0OdPmAcrkrH2eG0tQKUto8XKC/iMM4xcAz9kjc+t4SveSdQHs5EihXeHOjU4e6r26X/8n4h3kZ14nqF0QjDLo08GTySKsXKe7cmBWJxAeHfrOSIsJvk80h1JGcn3KoXxAz5F7JUsoxihgbXCNSgQu7Wc8ZcdWeM6zsySz0xnjtZlRYeynwJ5S3NkZiYyZwZLk37FpZMOmGELMo8QGoxJChyklW0XzOTEYa5l9MwRJc6llePyWjVmWGzHU3ib6+BZ";
        $factura->Comprobante->subTotal                                 = "2280.00";
        $factura->Comprobante->total                                    = "2644.80";
        $factura->Comprobante->metodoDePago                             = "Transferencia Electrónica de Fondos";
        $factura->Comprobante->tipoDeComprobante                        = "ingreso";
        $factura->Comprobante->cfdi                                     = "http://www.sat.gob.mx/cfd/3";
        $factura->Comprobante->xsi                                      = "http://www.w3.org/2001/XMLSchema-instance";
        $factura->Emisor->rfc                                           = "MEBX911110ISA";
        $factura->Emisor->nombre                                        = "Xochitl Coral Mendieta Betancourt";
        $factura->Emisor->DomicilioFiscal->calle                        = "Calle. Real del Monte";
        $factura->Emisor->DomicilioFiscal->noExterior                   = "No. 95";
        $factura->Emisor->DomicilioFiscal->colonia                      = "Col. Industrial";
        $factura->Emisor->DomicilioFiscal->localidad                    = "México, D.F.";
        $factura->Emisor->DomicilioFiscal->municipio                    = "Gustavo A. Madero";
        $factura->Emisor->DomicilioFiscal->estado                       = "Distrito Federal";
        $factura->Emisor->DomicilioFiscal->pais                         = "México";
        $factura->Emisor->DomicilioFiscal->codigoPostal                 = "07800";
        $factura->Emisor->RegimenFiscal->Regimen                        = "Régimen de las Personas Físicas con Actividades Empresariales y Profesionales.";
        $factura->Receptor->rfc                                         = "IEC121203FV8";
        $factura->Receptor->nombre                                      = "INTEGRADORA DE EMPRENDIMIENTOS CULTURALES SA DE CV";
        $factura->Receptor->Domicilio->calle                            = "TIBURCIO MONTIEL";
        $factura->Receptor->Domicilio->noExterior                       = "80";
        $factura->Receptor->Domicilio->noInterior                       = "INTERIOR B-3";
        $factura->Receptor->Domicilio->colonia                          = "COL. SAN MIGUEL CHAPULTEPEC";
        $factura->Receptor->Domicilio->localidad                        = "DISTRITO FEDERAL";
        $factura->Receptor->Domicilio->municipio                        = "Miguel Hidalgo";
        $factura->Receptor->Domicilio->estado                           = "Distrito Federal";
        $factura->Receptor->Domicilio->pais                             = "México";
        $factura->Receptor->codigoPostal                                = "11850";
        $factura->Conceptos->Concepto->cantidad                         = "1";
        $factura->Conceptos->Concepto->unidad                           = "No Aplica";
        $factura->Conceptos->Concepto->descripcion                      = "Coordinador ";
        $factura->Conceptos->Concepto->valorUnitario                    = "720";
        $factura->Conceptos->Concepto->importe                          = "720.00";
        $factura->Conceptos->Concepto->cantidad                         = "3";
        $factura->Conceptos->Concepto->exitunidad                       = "No Aplica";
        $factura->Conceptos->Concepto->descripcion                      = "Acomodadores";
        $factura->Conceptos->Concepto->valorUnitario                    = "520";
        $factura->Conceptos->Concepto->importe                          = "1560.00";
        $factura->Impuestos->totalImpuestosTrasladados                  ="364.80";
        $factura->Impuestos->Traslados->Traslado->impuesto              = "IVA";
        $factura->Impuestos->Traslados->Traslado->tasa                  = "16";
        $factura->Impuestos->Traslados->Traslado->importe               = "364.80";
        $factura->Complemento->TimbreFiscalDigital->schemaLocation      = "http://www.sat.gob.mx/TimbreFiscalDigital http://www.sat.gob.mx/TimbreFiscalDigital/TimbreFiscalDigital.xsd";
        $factura->Complemento->TimbreFiscalDigital->version             = "1.0";
        $factura->Complemento->TimbreFiscalDigital->UUID                = "101EB734-47C3-47FA-BBCD-C6D167648B26";
        $factura->Complemento->TimbreFiscalDigital->FechaTimbrado       = "2014-05-14T08:46:24";
        $factura->Complemento->TimbreFiscalDigital->selloCFD            = "LvBPRXayjjhrxNg5ulHqEF1Y89Dq8nz9c7hifOzH0C6r8iwODBf6h7P+ACbRgh+QPBtgnqjvlXn10Zw9WuWDVr+rAgkenFxC+VNST/2AHGr29zqa7r/FnENvtSjecClfJAy4GysstHXWm0BQQilfrYHZvu/qUZidnZVscr07jRU=";
        $factura->Complemento->TimbreFiscalDigital->noCertificadoSAT    = "00001000000202864883";
        $factura->Complemento->TimbreFiscalDigital->selloSAT            = "VZ4eWlWWfHkfUWv/2XGH4M4NEJujW7Vsm7EGe4IH/Ok02coXGgOUK667QDLMiDtG5e+fh4xXHCjbbLScvuKmdRJbRwbouWp/gfcgtRDK7c/akr0nn+miRWOS8XcdhtJ9SNxkQKCWZx26D1wD6XEMqV93YG9/M/yPfLiexa9QAyY=";
        $factura->Complemento->TimbreFiscalDigital->tfd                 = "http://www.sat.gob.mx/TimbreFiscalDigital";
        $factura->Complemento->TimbreFiscalDigital->xsi                 = "http://www.w3.org/2001/XMLSchema-instance";


        $array[]                                                        = $factura;

        $factura 				                                        = new stdClass();
        $factura->id                                                    = 6;
        $factura ->integradoId                                          = 1;
        $factura->status                                                = 0;
        $factura->Comprobante->schemaLocation                           = "http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv32.xsd";
        $factura->Comprobante->version                                  = "3.2";
        $factura->Comprobante->serie                                    = "4110 - ";
        $factura->Comprobante->folio                                    = "1790";
        $factura->Comprobante->LugarExpedicion                          = "México, D.F.";
        $factura->Comprobante->NumCtaPago                               = "012180001931017464 BANCOMER";
        $factura->Comprobante->TipoCambio                               = "1";
        $factura->Comprobante->Moneda                                   = "Pesos Mexicanos";
        $factura->Comprobante->fecha                                    = "2014-09-14T08:46:22";
        $factura->Comprobante->sello                                    = "LvBPRXayjjhrxNg5ulHqEF1Y89Dq8nz9c7hifOzH0C6r8iwODBf6h7P+ACbRgh+QPBtgnqjvlXn10Zw9WuWDVr+rAgkenFxC+VNST/2AHGr29zqa7r/FnENvtSjecClfJAy4GysstHXWm0BQQilfrYHZvu/qUZidnZVscr07jRU=";
        $factura->Comprobante->formaDePago                              = "Pago en una sola exhibición";
        $factura->Comprobante->noCertificado                            = "00001000000303670260";
        $factura->Comprobante->certificado                              = "MIIEhTCCA22gAwIBAgIUMDAwMDEwMDAwMDAzMDM2NzAyNjAwDQYJKoZIhvcNAQEFBQAwggGKMTgwNgYDVQQDDC9BLkMuIGRlbCBTZXJ2aWNpbyBkZSBBZG1pbmlzdHJhY2nDs24gVHJpYnV0YXJpYTEvMC0GA1UECgwmU2VydmljaW8gZGUgQWRtaW5pc3RyYWNpw7NuIFRyaWJ1dGFyaWExODA2BgNVBAsML0FkbWluaXN0cmFjacOzbiBkZSBTZWd1cmlkYWQgZGUgbGEgSW5mb3JtYWNpw7NuMR8wHQYJKoZIhvcNAQkBFhBhY29kc0BzYXQuZ29iLm14MSYwJAYDVQQJDB1Bdi4gSGlkYWxnbyA3NywgQ29sLiBHdWVycmVybzEOMAwGA1UEEQwFMDYzMDAxCzAJBgNVBAYTAk1YMRkwFwYDVQQIDBBEaXN0cml0byBGZWRlcmFsMRQwEgYDVQQHDAtDdWF1aHTDqW1vYzEVMBMGA1UELRMMU0FUOTcwNzAxTk4zMTUwMwYJKoZIhvcNAQkCDCZSZXNwb25zYWJsZTogQ2xhdWRpYSBDb3ZhcnJ1YmlhcyBPY2hvYTAeFw0xNDA0MDYyMTExMDJaFw0xODA0MDYyMTExMDJaMIHRMSowKAYDVQQDEyFYT0NISVRMIENPUkFMIE1FTkRJRVRBIEJFVEFOQ09VUlQxKjAoBgNVBCkTIVhPQ0hJVEwgQ09SQUwgTUVORElFVEEgQkVUQU5DT1VSVDEqMCgGA1UEChMhWE9DSElUTCBDT1JBTCBNRU5ESUVUQSBCRVRBTkNPVVJUMRYwFAYDVQQtEw1NRUJYOTExMTEwSVNBMRswGQYDVQQFExJNRUJYOTExMTEwTURGTlRDMDAxFjAUBgNVBAsTDU1FQlg5MTExMTBJU0EwgZ8wDQYJKoZIhvcNAQEBBQADgY0AMIGJAoGBAII3SaNzuQj6jBsM4pJHrOZ4XT0Bwzq9dOCY4kWgQ6WCPWXak0CnUlYkoYni8GBxhN2Uvtu4QQpCg+8e10HaQCw1lItoThQqpbRwLfxgXEgyEZ1v3KYGBju0zBeCnUjrNePPpcsGD5uolo08HW3rlc6+zfJDhOhLlXhL/oEob7rDAgMBAAGjHTAbMAwGA1UdEwEB/wQCMAAwCwYDVR0PBAQDAgbAMA0GCSqGSIb3DQEBBQUAA4IBAQDRxrGj3KsYlUar8+Mw/rDCAOrB2yXTDdoGS43hL0Hktvye98jcbhhpZLSRCxQT/Rf9WNFdb6YqfruTWwBJOYrS0OdPmAcrkrH2eG0tQKUto8XKC/iMM4xcAz9kjc+t4SveSdQHs5EihXeHOjU4e6r26X/8n4h3kZ14nqF0QjDLo08GTySKsXKe7cmBWJxAeHfrOSIsJvk80h1JGcn3KoXxAz5F7JUsoxihgbXCNSgQu7Wc8ZcdWeM6zsySz0xnjtZlRYeynwJ5S3NkZiYyZwZLk37FpZMOmGELMo8QGoxJChyklW0XzOTEYa5l9MwRJc6llePyWjVmWGzHU3ib6+BZ";
        $factura->Comprobante->subTotal                                 = "2280.00";
        $factura->Comprobante->total                                    = "2644.80";
        $factura->Comprobante->metodoDePago                             = "Transferencia Electrónica de Fondos";
        $factura->Comprobante->tipoDeComprobante                        = "ingreso";
        $factura->Comprobante->cfdi                                     = "http://www.sat.gob.mx/cfd/3";
        $factura->Comprobante->xsi                                      = "http://www.w3.org/2001/XMLSchema-instance";
        $factura->Emisor->rfc                                           = "MEBX911110ISA";
        $factura->Emisor->nombre                                        = "Xochitl Coral Mendieta Betancourt";
        $factura->Emisor->DomicilioFiscal->calle                        = "Calle. Real del Monte";
        $factura->Emisor->DomicilioFiscal->noExterior                   = "No. 95";
        $factura->Emisor->DomicilioFiscal->colonia                      = "Col. Industrial";
        $factura->Emisor->DomicilioFiscal->localidad                    = "México, D.F.";
        $factura->Emisor->DomicilioFiscal->municipio                    = "Gustavo A. Madero";
        $factura->Emisor->DomicilioFiscal->estado                       = "Distrito Federal";
        $factura->Emisor->DomicilioFiscal->pais                         = "México";
        $factura->Emisor->DomicilioFiscal->codigoPostal                 = "07800";
        $factura->Emisor->RegimenFiscal->Regimen                        = "Régimen de las Personas Físicas con Actividades Empresariales y Profesionales.";
        $factura->Receptor->rfc                                         = "IEC121203FV8";
        $factura->Receptor->nombre                                      = "INTEGRADORA DE EMPRENDIMIENTOS CULTURALES SA DE CV";
        $factura->receptor->domicilio->calle                            = "TIBURCIO MONTIEL";
        $factura->Receptor->Domicilio->noExterior                       = "80";
        $factura->Receptor->Domicilio->noInterior                       = "INTERIOR B-3";
        $factura->Receptor->Domicilio->colonia                          = "COL. SAN MIGUEL CHAPULTEPEC";
        $factura->Receptor->Domicilio->localidad                        = "DISTRITO FEDERAL";
        $factura->Receptor->Domicilio->municipio                        = "Miguel Hidalgo";
        $factura->Receptor->Domicilio->estado                           = "Distrito Federal";
        $factura->Receptor->Domicilio->pais                             = "México";
        $factura->Receptor->codigoPostal                                = "11850";
        $factura->Conceptos->Concepto->cantidad                         = "1";
        $factura->Conceptos->Concepto->unidad                           = "No Aplica";
        $factura->Conceptos->Concepto->descripcion                      = "Coordinador ";
        $factura->Conceptos->Concepto->valorUnitario                    = "720";
        $factura->Conceptos->Concepto->importe                          = "720.00";
        $factura->Conceptos->Concepto->cantidad                         = "3";
        $factura->Conceptos->Concepto->exitunidad                       = "No Aplica";
        $factura->Conceptos->Concepto->descripcion                      = "Acomodadores";
        $factura->Conceptos->Concepto->valorUnitario                    = "520";
        $factura->Conceptos->Concepto->importe                          = "1560.00";
        $factura->Impuestos->totalImpuestosTrasladados                  ="364.80";
        $factura->Impuestos->Traslados->Traslado->impuesto              = "IVA";
        $factura->Impuestos->Traslados->Traslado->tasa                  = "16";
        $factura->Impuestos->Traslados->Traslado->importe               = "364.80";
        $factura->Complemento->TimbreFiscalDigital->schemaLocation      = "http://www.sat.gob.mx/TimbreFiscalDigital http://www.sat.gob.mx/TimbreFiscalDigital/TimbreFiscalDigital.xsd";
        $factura->Complemento->TimbreFiscalDigital->version             = "1.0";
        $factura->Complemento->TimbreFiscalDigital->UUID                = "101EB734-47C3-47FA-BBCD-C6D167648B26";
        $factura->Complemento->TimbreFiscalDigital->FechaTimbrado       = "2014-05-14T08:46:24";
        $factura->Complemento->TimbreFiscalDigital->selloCFD            = "LvBPRXayjjhrxNg5ulHqEF1Y89Dq8nz9c7hifOzH0C6r8iwODBf6h7P+ACbRgh+QPBtgnqjvlXn10Zw9WuWDVr+rAgkenFxC+VNST/2AHGr29zqa7r/FnENvtSjecClfJAy4GysstHXWm0BQQilfrYHZvu/qUZidnZVscr07jRU=";
        $factura->Complemento->TimbreFiscalDigital->noCertificadoSAT    = "00001000000202864883";
        $factura->Complemento->TimbreFiscalDigital->selloSAT            = "VZ4eWlWWfHkfUWv/2XGH4M4NEJujW7Vsm7EGe4IH/Ok02coXGgOUK667QDLMiDtG5e+fh4xXHCjbbLScvuKmdRJbRwbouWp/gfcgtRDK7c/akr0nn+miRWOS8XcdhtJ9SNxkQKCWZx26D1wD6XEMqV93YG9/M/yPfLiexa9QAyY=";
        $factura->Complemento->TimbreFiscalDigital->tfd                 = "http://www.sat.gob.mx/TimbreFiscalDigital";
        $factura->Complemento->TimbreFiscalDigital->xsi                 = "http://www.w3.org/2001/XMLSchema-instance";


        $array[]                                                        = $factura;

        $factura 				                                        = new stdClass();
        $factura->id                                                    = 7;
        $factura ->integradoId                                          = 2;
        $factura->status                                                = 0;
        $factura->Comprobante->schemaLocation                           = "http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv32.xsd";
        $factura->Comprobante->version                                  = "3.2";
        $factura->Comprobante->serie                                    = "110 - ";
        $factura->Comprobante->folio                                    = "120";
        $factura->Comprobante->LugarExpedicion                          = "México, D.F.";
        $factura->Comprobante->NumCtaPago                               = "012180001931017464 BANCOMER";
        $factura->Comprobante->TipoCambio                               = "1";
        $factura->Comprobante->Moneda                                   = "Pesos Mexicanos";
        $factura->Comprobante->fecha                                    = "2014-12-14T08:46:22";
        $factura->Comprobante->sello                                    = "LvBPRXayjjhrxNg5ulHqEF1Y89Dq8nz9c7hifOzH0C6r8iwODBf6h7P+ACbRgh+QPBtgnqjvlXn10Zw9WuWDVr+rAgkenFxC+VNST/2AHGr29zqa7r/FnENvtSjecClfJAy4GysstHXWm0BQQilfrYHZvu/qUZidnZVscr07jRU=";
        $factura->Comprobante->formaDePago                              = "Pago en una sola exhibición";
        $factura->Comprobante->noCertificado                            = "00001000000303670260";
        $factura->Comprobante->certificado                              = "MIIEhTCCA22gAwIBAgIUMDAwMDEwMDAwMDAzMDM2NzAyNjAwDQYJKoZIhvcNAQEFBQAwggGKMTgwNgYDVQQDDC9BLkMuIGRlbCBTZXJ2aWNpbyBkZSBBZG1pbmlzdHJhY2nDs24gVHJpYnV0YXJpYTEvMC0GA1UECgwmU2VydmljaW8gZGUgQWRtaW5pc3RyYWNpw7NuIFRyaWJ1dGFyaWExODA2BgNVBAsML0FkbWluaXN0cmFjacOzbiBkZSBTZWd1cmlkYWQgZGUgbGEgSW5mb3JtYWNpw7NuMR8wHQYJKoZIhvcNAQkBFhBhY29kc0BzYXQuZ29iLm14MSYwJAYDVQQJDB1Bdi4gSGlkYWxnbyA3NywgQ29sLiBHdWVycmVybzEOMAwGA1UEEQwFMDYzMDAxCzAJBgNVBAYTAk1YMRkwFwYDVQQIDBBEaXN0cml0byBGZWRlcmFsMRQwEgYDVQQHDAtDdWF1aHTDqW1vYzEVMBMGA1UELRMMU0FUOTcwNzAxTk4zMTUwMwYJKoZIhvcNAQkCDCZSZXNwb25zYWJsZTogQ2xhdWRpYSBDb3ZhcnJ1YmlhcyBPY2hvYTAeFw0xNDA0MDYyMTExMDJaFw0xODA0MDYyMTExMDJaMIHRMSowKAYDVQQDEyFYT0NISVRMIENPUkFMIE1FTkRJRVRBIEJFVEFOQ09VUlQxKjAoBgNVBCkTIVhPQ0hJVEwgQ09SQUwgTUVORElFVEEgQkVUQU5DT1VSVDEqMCgGA1UEChMhWE9DSElUTCBDT1JBTCBNRU5ESUVUQSBCRVRBTkNPVVJUMRYwFAYDVQQtEw1NRUJYOTExMTEwSVNBMRswGQYDVQQFExJNRUJYOTExMTEwTURGTlRDMDAxFjAUBgNVBAsTDU1FQlg5MTExMTBJU0EwgZ8wDQYJKoZIhvcNAQEBBQADgY0AMIGJAoGBAII3SaNzuQj6jBsM4pJHrOZ4XT0Bwzq9dOCY4kWgQ6WCPWXak0CnUlYkoYni8GBxhN2Uvtu4QQpCg+8e10HaQCw1lItoThQqpbRwLfxgXEgyEZ1v3KYGBju0zBeCnUjrNePPpcsGD5uolo08HW3rlc6+zfJDhOhLlXhL/oEob7rDAgMBAAGjHTAbMAwGA1UdEwEB/wQCMAAwCwYDVR0PBAQDAgbAMA0GCSqGSIb3DQEBBQUAA4IBAQDRxrGj3KsYlUar8+Mw/rDCAOrB2yXTDdoGS43hL0Hktvye98jcbhhpZLSRCxQT/Rf9WNFdb6YqfruTWwBJOYrS0OdPmAcrkrH2eG0tQKUto8XKC/iMM4xcAz9kjc+t4SveSdQHs5EihXeHOjU4e6r26X/8n4h3kZ14nqF0QjDLo08GTySKsXKe7cmBWJxAeHfrOSIsJvk80h1JGcn3KoXxAz5F7JUsoxihgbXCNSgQu7Wc8ZcdWeM6zsySz0xnjtZlRYeynwJ5S3NkZiYyZwZLk37FpZMOmGELMo8QGoxJChyklW0XzOTEYa5l9MwRJc6llePyWjVmWGzHU3ib6+BZ";
        $factura->Comprobante->subTotal                                 = "2280.00";
        $factura->Comprobante->total                                    = "2644.80";
        $factura->Comprobante->metodoDePago                             = "Transferencia Electrónica de Fondos";
        $factura->Comprobante->tipoDeComprobante                        = "ingreso";
        $factura->Comprobante->cfdi                                     = "http://www.sat.gob.mx/cfd/3";
        $factura->Comprobante->xsi                                      = "http://www.w3.org/2001/XMLSchema-instance";
        $factura->Emisor->rfc                                           = "MEBX911110ISA";
        $factura->Emisor->nombre                                        = "Xochitl Coral Mendieta Betancourt";
        $factura->Emisor->DomicilioFiscal->calle                        = "Calle. Real del Monte";
        $factura->Emisor->DomicilioFiscal->noExterior                   = "No. 95";
        $factura->Emisor->DomicilioFiscal->colonia                      = "Col. Industrial";
        $factura->Emisor->DomicilioFiscal->localidad                    = "México, D.F.";
        $factura->Emisor->DomicilioFiscal->municipio                    = "Gustavo A. Madero";
        $factura->Emisor->DomicilioFiscal->estado                       = "Distrito Federal";
        $factura->Emisor->DomicilioFiscal->pais                         = "México";
        $factura->Emisor->DomicilioFiscal->codigoPostal                 = "07800";
        $factura->Emisor->RegimenFiscal->Regimen                        = "Régimen de las Personas Físicas con Actividades Empresariales y Profesionales.";
        $factura->Receptor->rfc                                         = "IEC121203FV8";
        $factura->Receptor->nombre                                      = "INTEGRADORA DE EMPRENDIMIENTOS CULTURALES SA DE CV";
        $factura->Receptor->Domicilio->calle                            = "TIBURCIO MONTIEL";
        $factura->Receptor->Domicilio->noExterior                       = "80";
        $factura->Receptor->Domicilio->noInterior                       = "INTERIOR B-3";
        $factura->Receptor->Domicilio->colonia                          = "COL. SAN MIGUEL CHAPULTEPEC";
        $factura->Receptor->Domicilio->localidad                        = "DISTRITO FEDERAL";
        $factura->Receptor->Domicilio->municipio                        = "Miguel Hidalgo";
        $factura->Receptor->Domicilio->estado                           = "Distrito Federal";
        $factura->Receptor->Domicilio->pais                             = "México";
        $factura->Receptor->codigoPostal                                = "11850";
        $factura->Conceptos->Concepto->cantidad                         = "1";
        $factura->Conceptos->Concepto->unidad                           = "No Aplica";
        $factura->Conceptos->Concepto->descripcion                      = "Coordinador ";
        $factura->Conceptos->Concepto->valorUnitario                    = "720";
        $factura->Conceptos->Concepto->importe                          = "720.00";
        $factura->Conceptos->Concepto->cantidad                         = "3";
        $factura->Conceptos->Concepto->exitunidad                       = "No Aplica";
        $factura->Conceptos->Concepto->descripcion                      = "Acomodadores";
        $factura->Conceptos->Concepto->valorUnitario                    = "520";
        $factura->Conceptos->Concepto->importe                          = "1560.00";
        $factura->Impuestos->totalImpuestosTrasladados                  ="364.80";
        $factura->Impuestos->Traslados->Traslado->impuesto              = "IVA";
        $factura->Impuestos->Traslados->Traslado->tasa                  = "16";
        $factura->Impuestos->Traslados->Traslado->importe               = "364.80";
        $factura->Complemento->TimbreFiscalDigital->schemaLocation      = "http://www.sat.gob.mx/TimbreFiscalDigital http://www.sat.gob.mx/TimbreFiscalDigital/TimbreFiscalDigital.xsd";
        $factura->Complemento->TimbreFiscalDigital->version             = "1.0";
        $factura->Complemento->TimbreFiscalDigital->UUID                = "101EB734-47C3-47FA-BBCD-C6D167648B26";
        $factura->Complemento->TimbreFiscalDigital->FechaTimbrado       = "2014-05-14T08:46:24";
        $factura->Complemento->TimbreFiscalDigital->selloCFD            = "LvBPRXayjjhrxNg5ulHqEF1Y89Dq8nz9c7hifOzH0C6r8iwODBf6h7P+ACbRgh+QPBtgnqjvlXn10Zw9WuWDVr+rAgkenFxC+VNST/2AHGr29zqa7r/FnENvtSjecClfJAy4GysstHXWm0BQQilfrYHZvu/qUZidnZVscr07jRU=";
        $factura->Complemento->TimbreFiscalDigital->noCertificadoSAT    = "00001000000202864883";
        $factura->Complemento->TimbreFiscalDigital->selloSAT            = "VZ4eWlWWfHkfUWv/2XGH4M4NEJujW7Vsm7EGe4IH/Ok02coXGgOUK667QDLMiDtG5e+fh4xXHCjbbLScvuKmdRJbRwbouWp/gfcgtRDK7c/akr0nn+miRWOS8XcdhtJ9SNxkQKCWZx26D1wD6XEMqV93YG9/M/yPfLiexa9QAyY=";
        $factura->Complemento->TimbreFiscalDigital->tfd                 = "http://www.sat.gob.mx/TimbreFiscalDigital";
        $factura->Complemento->TimbreFiscalDigital->xsi                 = "http://www.w3.org/2001/XMLSchema-instance";


        $array[]                                                        = $factura;

        return $array;


    }

    public static function getFactComisiones(){
        $factComiciones = new stdClass();

        $factComiciones->id             = 1;
        $factComiciones->receptor       = 1;
        $factComiciones->emisor         = 'INTEGRADORA DE EMPRENDIMIENTOS CULTURALES SA DE CV';
        $factComiciones->created        = 1408632474029;
        $factComiciones->totalAmount    = 100000;

        $array[] = $factComiciones;

        $factComiciones = new stdClass();

        $factComiciones->id             = 34;
        $factComiciones->receptor       = 2;
        $factComiciones->emisor         = 'INTEGRADORA DE EMPRENDIMIENTOS CULTURALES SA DE CV';
        $factComiciones->created        = 1408632474129;
        $factComiciones->totalAmount    = 500000;

        $array[] = $factComiciones;

        foreach ($array as $key => $value) {
            self::convierteFechas($value);
            $respuesta[] = $value;
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

        $commissions 				    = new comisionItem;
        $commissions->id                = 2;
        $commissions->description		= 'Factura';
        $commissions->type           	= 0;
        $commissions->frequencyTime		= null;
        $commissions->status			= 1;
        $commissions->amount			= 1000;
        $commissions->rate				= null;

        $array[] = $commissions;


		return $array;

	}


    public static function getTxSTP($userId = null)
    {
        $txstp = new stdClass;
        $txstp->referencia = 'A458455A554SJHS445AA2D';
        $txstp->userId = 1;
        $txstp->date = 1419897600000;
        $txstp->amount = '34014.100';


        $array[] = $txstp;

        $txstp = new stdClass;
        $txstp->referencia = 'A458455A5S1S5200S4AA2D';
        $txstp->userId = 1;
        $txstp->date = 1408632474029;
        $txstp->amount = '1520.2145';


        $array[] = $txstp;

        $txstp = new stdClass;
        $txstp->referencia = 'A458455A55422S5S555220';
        $txstp->userId = 1;
        $txstp->date = 1419897603300;
        $txstp->amount = '34240.10';


        $array[] = $txstp;

        $txstp = new stdClass;
        $txstp->referencia = 'A458455A55422255F6AA2D';
        $txstp->userId = 1;
        $txstp->date = 1419897602100;
        $txstp->amount = '8340.10';


        $array[] = $txstp;

        $txstp = new stdClass;
        $txstp->referencia = 'A458455A55421S555S17S74';
        $txstp->userId = 1;
        $txstp->date = 1419897600023;
        $txstp->amount = '1340.10';


        $array[] = $txstp;

        $txstp = new stdClass;
        $txstp->referencia = 'A458455A554222115s11s5s';
        $txstp->userId = 1;
        $txstp->date = 1408632474029;
        $txstp->amount = '34540.10';


        $array[] = $txstp;

        $txstp = new stdClass;
        $txstp->referencia = 'A458455A55422255F6AA2D';
        $txstp->userId = 1;
        $txstp->date = 1419897600000;
        $txstp->amount = '340.10';

        $array[] = $txstp;

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
/*		$url = MIDDLE.PUERTO.TIMONE.'security/getKey';
		if( !$token = file_get_contents($url) ){
			JFactory::getApplication()->redirect('index.php', 'No se pudo conectar con TIMONE', 'error');
		}*/
$token = 'fghgjsdatr';
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

    public static function getOperacionesVenta($integradoId){

        $operaciones 					= new stdClass;
        $operaciones->id                = 1;
        $operaciones->integradoId       = 1;
        $operaciones->numOrden          = 1;
        $operaciones->created           = 1408632474029;
        $operaciones->totalAmount       = 13000;
        $operaciones->currency        	= 'MXN';
        $operaciones->status            = 1;
        $operaciones->observaciones		= 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $operaciones;

        $operaciones 					= new stdClass;
        $operaciones->id                = 2;
        $operaciones->integradoId       = 1;
        $operaciones->numOrden          = 2;
        $operaciones->created           = 1408632474029;
        $operaciones->totalAmount       = 10000;
        $operaciones->currency        	= 'MXN';
        $operaciones->status            = 1;
        $operaciones->observaciones		= 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $operaciones;

        $operaciones 					= new stdClass;
        $operaciones->id                = 3;
        $operaciones->integradoId       = 1;
        $operaciones->numOrden          = 3;
        $operaciones->created           = 1408632474029;
        $operaciones->totalAmount       = 113000;
        $operaciones->currency        	= 'MXN';
        $operaciones->paymentType		= 0;
        $operaciones->status            = 0;
        $operaciones->observaciones		= 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $array[] = $operaciones;

        foreach ($array as $key => $value) {
            if($integradoId == $value->integradoId){
                self::convierteFechas($value);
                $respuesta[] = $value;
            }
        }

        return $respuesta;
    }

	public static function getFacturasVenta($integradoId)
	{
		return self::getOrdenesVenta($integradoId);
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