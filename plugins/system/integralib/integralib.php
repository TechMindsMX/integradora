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

    protected $filename = 'qa.json';

    protected $seedFilename = 'integradora-seed-qa.json';

    private $path = 'C:/Users/Ricardo/.integradora/';

    /**
     * Method to include namespace Integralib, load environment variables and make seed
     */
    public function onAfterInitialise() {
		JLoader::registerNamespace( 'Integralib', JPATH_LIBRARIES );

		Environment::setEnvVariables($this->path, $this->filename);

		SeedIntegradora::seedIntegradora($this->path, $this->seedFilename);
	}
}