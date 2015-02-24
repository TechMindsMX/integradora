<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modeladmin');
jimport('integradora.integrado');

class IntegradoModelIntegrado extends JModelAdmin
{
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	public function getItem($pk = null)
	{
		$input = JFactory::getApplication()->input;
		$this->integ_id = ($input->get('integrado_id', 0, 'int') ? $input->get('integrado_id', 0, 'int') : $input->get('id', 0, 'int'));

		$integrado = new IntegradoSimple($this->integ_id);
		$item = $integrado;

		$item->catalogos = $this->getCatalogos();

		switch (intval($item->integrados[0]->integrado->status)) {
			case 0: // Nueva solicitud 0
				$validos = array(0, 2, 3, 99);
				break;
			case 1: // para revision nuevamente 1
				$validos = array(1, 2, 3, 99);
				break;
			case 2: // Devuelto 2
				$validos = array(1, 2);
				break;
			case 3: // contrato 3
				$validos = array(3, 50, 99);
				break;
			case 50: // integrado 50
				$validos = array(50);
				break;
			case 99: // cancelada 99
				$validos = array(99);
				break;
			default:
				$validos = array();
				break;
		}
		$item->transicion_status = $validos;

		$item->integrados[0]->datos_personales->nacionalidad = $this->getNacionalidad($item->integrados[0]->datos_personales->nacionalidad);

		$item->getUrlsTestimonions();

		$item->campos = $this->getCampos();

		return $item;
	}

	public function getCatalogos()
	{
		$catalogos = new Catalogos;

		$catalogos->getStatusSolicitud();

		return $catalogos;
	}

	public function getVerifications( ){
		$data = getFromTimOne::selectDB('integrado_verificacion_solicitud', 'integradoId = '. $this->integ_id );
		$data = empty($data)?$data:$data[0];

		return $data;

	}

	public function getTable($type = 'Integrado', $prefix = 'IntegradoTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true)
	{
//		$form = $this->loadForm('com_integrado.integrado', 'integrado', array('control' => 'jform', 'load_data' => $loadData));
//		if (empty($form)) {
//			return false;
//		}
//		return $form;
	}

	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_integrado.edit.integrado.data', array());
		if (empty($data)) {
			$data = $this->getItem();
		}
		return $data;
	}

	function getNacionalidad($id)
	{
		return Integrado::getNationalityNameFromId($id);
	}

	function getCampos(){
		$campos = new stdClass();

		$campos->LBL_SLIDE_BASIC = array(
			'nacionalidad',
			'sexo',
			'fecha_nacimiento',
			'rfc',
			'calle',
			'num_exterior',
			'num_interior',
			'cod_postal',
			'curp',
			'email',
			'tel_fijo',
			'tel_fijo_extension',
			'tel_movil',
			'nom_comercial',
			'nombre_representante'
		);
		$campos->attach_LBL_SLIDE_BASIC = array(
			'url_identificacion',
			'url_rfc',
			'url_comprobante_domicilio');

		$campos->LBL_TAB_EMPRESA = array(
			'razon_social',
			'rfc',
			'calle',
			'num_exterior',
			'num_interior',
			'cod_postal',
			'tel_fijo',
			'tel_fijo_extension',
			'tel_fax',
			'sitio_web'
		);
		$campos->attach_LBL_TAB_EMPRESA = array(
			'url_rfc',
			'testimonio_1',
			'testimonio_2',
			'poder',
			'reg_propiedad'
		);

		$campos->LBL_TAB_BANCO = array(
			'banco_codigo',
			'banco_cuenta',
			'banco_sucursal',
			'banco_clabe'
		);
		$campos->attach_LBL_TAB_BANCO = array(
			'banco_file');

        $campos->LBL_TAB_AUTHORIZATIONS = array(
            'params'
        );
        $campos->attach_LBL_TAB_AUTHORIZATIONS = array();

		return $campos;
	}

}