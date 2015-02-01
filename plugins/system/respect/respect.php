<?php
/**
 * Created by PhpStorm.
 * User: rlyon
 * Date: 1/31/2015
 * Time: 6:23 PM
 */

defined('_JEXEC') or die;

/**
 * Mylib plugin class.
 *
 * @package     Joomla.plugin
 * @subpackage  System.respect
 */
class plgSystemRespect extends JPlugin
{
	/**
	 * Method to register custom library.
	 *
	 * return  void
	 */
	public function onAfterInitialise()
	{
		JLoader::registerNamespace('Respect', JPATH_LIBRARIES . '/respect');

		JLoader::register('allof', JPATH_LIBRARIES . '/respect/Rules/AllOf.php');
		JLoader::register('validator', JPATH_LIBRARIES . '/respect/validator.php');
	}
}