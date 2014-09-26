<?php
defined('_JEXEC') or die('Restricted access');
 
// import joomla controller library
jimport('joomla.application.component.controller');

if (!JFactory::getUser()->authorise('core.manage', 'com_adminintegradora'))
{
        return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}
// Get an instance of the controller prefixed by component name
$controller = JControllerLegacy::getInstance('Adminintegradora');
 
// Perform the Request task
$input = JFactory::getApplication()->input;
$controller->execute($input->getCmd('task'));
 
// Redirect if set by the controller
$controller->redirect();