<?php
/**
 * Created by PhpStorm.
 * User: rlyon
 * Date: 2/10/2015
 * Time: 11:21 PM
 */
defined('_JEXEC') or die;

class plgSystemIntegralib extends JPlugin {

	public function onAfterInitialise() {
		JLoader::registerNamespace( 'Integralib', JPATH_LIBRARIES );

		$integralib = new Integralib\Environment();
		$integralib->setEnvVariables();

	}
}