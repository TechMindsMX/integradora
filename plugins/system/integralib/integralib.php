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
    protected $path;
    protected $filename;
    protected $seedFilename;
    protected $seedConcentradoraFilename;

    /**
     * Method to include namespace Integralib, load environment variables and make seed
     */
    public function onAfterInitialise() {
        $this->setProperties( $this->getParamsArray() );

		JLoader::registerNamespace( 'Integralib', JPATH_LIBRARIES );

		Environment::setEnvVariables($this->path, $this->filename);

		SeedIntegradora::seedIntegradora($this->path, $this->seedFilename);
		SeedIntegradora::seedIntegradora($this->path, $this->seedConcentradoraFilename);
	}

    /**
     * @return mixed
     */
    private function getFilesParams()
    {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);
        $query->select('*')
            ->from('#__integradora_config');
        $db->setQuery($query);

        return $db->loadObjectList();
    }

    private function getParamsArray()
    {
        $result = $this->getFilesParams();

        foreach ($result as $row) {
            $array[$row->name] = $row->value;
        }

        return $array;
    }
}