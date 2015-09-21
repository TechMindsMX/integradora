<?php
/**
 * Created by PhpStorm.
 * User: rlyon
 * Date: 2/10/2015
 * Time: 11:21 PM
 */
use Integralib\Environment;
use Integralib\SeedIntegradora;

defined('_JEXEC') or die;

class plgSystemIntegralib extends JPlugin {

    private $path = 'C:\Users\dev-ismael/.integradora/';

    protected $filename = 'qa.json';

    protected $seedFilename = 'integradora-seed-qa.json';

    /**
     * Method to include namespace Integralib, load environment variables and make seed
     */
    public function onAfterInitialise() {
		JLoader::registerNamespace( 'Integralib', JPATH_LIBRARIES );

		Environment::setEnvVariables($this->path, $this->filename);

		SeedIntegradora::seedIntegradora($this->path, $this->seedFilename);
	}
}