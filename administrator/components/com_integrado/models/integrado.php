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
        $this->integ_id = ($input->get('integradoId', 0, 'string') ? $input->get('integradoId', 0, 'string') : $input->get('id', 0, 'string'));

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
				$validos = array(50,99);
				break;
			case 99: // cancelada 99
				$validos = array(1);
				break;
			default:
				$validos = array();
				break;
		}
		$item->transicion_status = $validos;

		$item->integrados[0]->datos_personales->nacionalidad = $this->getNacionalidad($item->integrados[0]->datos_personales->nacionalidad);

        if(!is_null($item->integrados[0]->datos_empresa)) {
            $item->getDataTestimonions();
	        $this->setTestimoniosDataForView($item);
        }

		$item->campos = $this->getCampos();

		return $item;
	}

	public function setTestimoniosDataForView($item) {
		$item->integrados[0]->datos_empresa->testimonio_1_instrum_type = '';
		$item->integrados[0]->datos_empresa->testimonio_1_instrum_fecha = $item->integrados[0]->datos_empresa->testimonio_1->instrum_fecha;
		$item->integrados[0]->datos_empresa->testimonio_1_instrum_notaria= $item->integrados[0]->datos_empresa->testimonio_1->instrum_notaria;
		$item->integrados[0]->datos_empresa->testimonio_1_instrum_num_instrumento = $item->integrados[0]->datos_empresa->testimonio_1->instrum_num_instrumento;
		$item->integrados[0]->datos_empresa->testimonio_1_instrum_nom_notario = $item->integrados[0]->datos_empresa->testimonio_1->instrum_nom_notario;
		$item->integrados[0]->datos_empresa->testimonio_1 = $item->integrados[0]->datos_empresa->testimonio_1->url_instrumento;

		$item->integrados[0]->datos_empresa->testimonio_2_instrum_type = '';
		$item->integrados[0]->datos_empresa->testimonio_2_instrum_fecha = $item->integrados[0]->datos_empresa->testimonio_2->instrum_fecha;
		$item->integrados[0]->datos_empresa->testimonio_2_instrum_notaria= $item->integrados[0]->datos_empresa->testimonio_2->instrum_notaria;
		$item->integrados[0]->datos_empresa->testimonio_2_instrum_num_instrumento = $item->integrados[0]->datos_empresa->testimonio_2->instrum_num_instrumento;
		$item->integrados[0]->datos_empresa->testimonio_2_instrum_nom_notario = $item->integrados[0]->datos_empresa->testimonio_2->instrum_nom_notario;
		$item->integrados[0]->datos_empresa->testimonio_2 = $item->integrados[0]->datos_empresa->testimonio_2->url_instrumento;

		$item->integrados[0]->datos_empresa->poder_instrum_type = '';
		$item->integrados[0]->datos_empresa->poder_instrum_fecha = $item->integrados[0]->datos_empresa->poder->instrum_fecha;
		$item->integrados[0]->datos_empresa->poder_instrum_notaria= $item->integrados[0]->datos_empresa->poder->instrum_notaria;
		$item->integrados[0]->datos_empresa->poder_instrum_num_instrumento = $item->integrados[0]->datos_empresa->poder->instrum_num_instrumento;
		$item->integrados[0]->datos_empresa->poder_instrum_nom_notario = $item->integrados[0]->datos_empresa->poder->instrum_nom_notario;
		$item->integrados[0]->datos_empresa->poder = $item->integrados[0]->datos_empresa->poder->url_instrumento;

		$item->integrados[0]->datos_empresa->reg_propiedad_instrum_type = '';
		$item->integrados[0]->datos_empresa->reg_propiedad_instrum_fecha = $item->integrados[0]->datos_empresa->reg_propiedad->instrum_fecha;
		$item->integrados[0]->datos_empresa->reg_propiedad_instrum_notaria= $item->integrados[0]->datos_empresa->reg_propiedad->instrum_notaria;
		$item->integrados[0]->datos_empresa->reg_propiedad_instrum_num_instrumento = $item->integrados[0]->datos_empresa->reg_propiedad->instrum_num_instrumento;
		$item->integrados[0]->datos_empresa->reg_propiedad_instrum_nom_notario = $item->integrados[0]->datos_empresa->reg_propiedad->instrum_nom_notario;
		$item->integrados[0]->datos_empresa->reg_propiedad = $item->integrados[0]->datos_empresa->reg_propiedad->url_instrumento;

	}

	public function getCatalogos()
	{
		$catalogos = new Catalogos;

		$catalogos->getStatusSolicitud();

		return $catalogos;
	}

	public function getVerifications( ){
		$dbq = JFactory::getDbo();
		$data = getFromTimOne::selectDB('integrado_verificacion_solicitud', 'integradoId = '. $dbq->quote($this->integ_id) );
		$data = empty($data)?$data:$data[0];

		return $data;
	}

	public function getTable($type = 'Integrado', $prefix = 'IntegradoTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true)
	{
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
			'url_comprobante_domicilio'
		);

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
			'sitio_web',
			'testimonio_1_instrum_type',
			'testimonio_1_instrum_fecha',
			'testimonio_1_instrum_notaria',
			'testimonio_1_instrum_num_instrumento',
			'testimonio_1_instrum_nom_notario',
			'testimonio_2_instrum_type',
			'testimonio_2_instrum_fecha',
			'testimonio_2_instrum_notaria',
			'testimonio_2_instrum_num_instrumento',
			'testimonio_2_instrum_nom_notario',
			'poder_instrum_type',
			'poder_instrum_fecha',
			'poder_instrum_notaria',
			'poder_instrum_num_instrumento',
			'poder_instrum_nom_notario',
			'reg_propiedad_instrum_type',
			'reg_propiedad_instrum_fecha',
			'reg_propiedad_instrum_notaria',
			'reg_propiedad_instrum_num_instrumento',
			'reg_propiedad_instrum_nom_notario',
		);

		$campos->attach_LBL_TAB_EMPRESA = array(
			'url_rfc',
			'testimonio_1',
			'testimonio_2',
			'poder',
			'reg_propiedad'
		);

		$campos->LBL_TAB_BANCO = array(
			'bankName',
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