<?php
/**
 * @version     1.0.1
 * @package     com_donde_comprar
 * @copyright   Copyright (C) 2014. Todos los derechos reservados.
 * @license     Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 * @author      ismael <aguilar_2001@hotmail.com> - http://
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Comprars list controller class.
 */
class Donde_comprarControllerComprars extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel($name = 'conciliacion', $prefix = 'ConciliacionModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
    
    
    
    
}