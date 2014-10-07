<?php
defined ('_JEXEC') or die('Restricted Access');

jimport ('joomla.application.component.controlleradmin');

/**
 *
 */
class AdminintegradoraControllerComision extends JControllerAdmin
{

	public function getModel ($name = 'Comision',
							  $prefix = 'AdminintegradoraModel') {
		$model = parent::getModel ($name,
								   $prefix,
								   array ('ignore_request' => true));
		return $model;
	}

	public function cancel () {
		$url = 'index.php?option=com_adminintegradora&view=comisions';
		JFactory::getApplication ()->redirect ($url);
	}


}
