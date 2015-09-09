<?php
defined('_JEXEC') or die( 'Restricted Access' );

jimport('joomla.application.component.modelitem');

jimport('integradora.integrado');
jimport('integradora.rutas');
jimport('integradora.catalogos');
jimport('integradora.xmlparser');

/**
 * Modelo de datos para Formulario p/generar Ordenes de Compra de un integrado
 */
class MandatosModelOdcform extends JModelItem
{
    protected $dataModelo;
    protected $catalogos;

    public function __construct()
    {
        $post            = array ('idOrden' => 'INT');
        $this->inputVars = JFactory::getApplication()->input->getArray($post);
        $this->id        = $this->inputVars['idOrden'];

        $session           = JFactory::getSession();
        $this->integradoId = $session->get('integradoId', null, 'integrado');

        parent::__construct();
    }

    /**
     * @return mixed
     */
    public function getOrden()
    {
        if ( ! isset( $this->dataModelo )) {
            $this->dataModelo = getFromTimOne::getOrdenesCompra($this->integradoId, $this->id);
        }
        $this->dataModelo = $this->dataModelo[0];

        $dataxml = $this->getdata2xml($this->dataModelo->urlXML);

        foreach ($dataxml->conceptos as $key => $value) {
            $this->dataModelo->productos[] = $value;
        }
        $this->dataModelo->dataxml = $dataxml;

        return $this->dataModelo;
    }

    /**
     * @return mixed
     */
    public function getProyectos()
    {
        $proyectos = getFromTimOne::getActiveProyects($this->integradoId);

        foreach ($proyectos as $proyecto) {
            $proyecto->subproyectos = getFromTimOne::getActiveSubProyects($proyecto->id_proyecto);
        }

        return $proyectos;
    }

    /**
     * @return array
     */
    public function getProviders()
    {
        $proveedores = getFromTimOne::getClientes($this->integradoId, 1);

        $respuesta = array ();

        foreach ($proveedores as $key => $value) {
            $integ              = new IntegradoSimple($value->id);
            $integ->displayName = $integ->getDisplayName();

            $proveedores[$key] = $integ;

            if ($value->status != 0) {
                $respuesta[$key] = $proveedores[$key];
            }
        }

        return $respuesta;
    }

    /**
     * @param $urlFile
     * @param null $destination
     *
     * @return stdClass
     * @throws Exception
     */
    public function getdata2xml($urlFile, $destination = null)
    {
        if (is_null($destination)) {
            $urlFile = $this->saveFile($urlFile, MEDIA_FILES . $destination);
        }
        $xmlFileData   = file_get_contents(JPATH_ROOT . DIRECTORY_SEPARATOR . $urlFile);
        $data          = new xml2Array();
        $datos         = $data->manejaXML($xmlFileData);
        $datos->urlXML = $urlFile;

        return $datos;
    }

    /**
     * @return mixed
     */
    public function getCatalogoBancos()
    {
        $this->catalogos = new Catalogos();

        return $this->catalogos->getBancos();
    }

    /**
     * @return mixed
     */
    public function getCatalogos()
    {
        return $this->catalogos->getPaymentMethods();

    }

    /**
     * @param $dataXml
     * @param IntegradoSimple $receptor
     *
     * @throws Exception
     * @internal param $data
     */
    public function checkXmlClientAndProvider($dataXml, IntegradoSimple $emisor, IntegradoSimple $receptor)
    {
        if ($dataXml->emisor['attrs']['RFC'] != $emisor->getIntegradoRfc() && $dataXml->receptor['attrs']['RFC'] !== $receptor->getIntegradoRfc()) {
            throw new Exception(JText::_('ERROR_XML_INVALID_ACTORS'));
        }
    }

    /**
     * @param $file
     * @param $mimeType
     *
     * @throws Exception
     */
    public function validateFileSizeAndExtension($file, $mimeType)
    {
        if ($file['size'] === 0) {
            throw new Exception(JText::sprintf('ERROR_FILE_SIZE', $file['name']));
        } elseif ($file['type'] !== $mimeType) {
            throw new Exception(JText::sprintf('ERROR_FILE_TYPE', $file['name']));
        }

    }

    /**
     * @param $file
     *
     * @return mixed
     * @throws Exception
     */
    public function saveOdcPdfFile($file)
    {
        return $this->saveFile($file, "media/pdf_odc/" . $file['name']);
    }

    /**
     * @param $file
     * @param $destination
     *
     * @return mixed
     * @throws Exception
     */
    public function saveFile($file, $destination)
    {
        if ( ! move_uploaded_file($file['tmp_name'], $destination)) {
            throw new Exception(JText::sprintf('ERR_419_FILE_SAVE_ERRROR', $file['tmp_name']));
        }

        return $destination;
    }

    /**
     * @param $xmlFileName
     *
     * @throws Exception
     */
    public function sendXmlForExternalValidation($xmlFileName)
    {
        if (true === false) {
            $envio           = new \Integralib\TimOneRequest();
            $rutas           = new servicesRoute();
            $envio->objEnvio = new \stdClass();

            $envio->objEnvio->xml = file_get_contents($xmlFileName);

            $envio->makeRequest($rutas->getUrlService('facturacion', 'facturaValidate', 'create'));
        }

        if (true === false) {
            throw new Exception(JText::_('ERROR_'));
        }
    }
}

