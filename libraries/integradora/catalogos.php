<?php
defined('JPATH_PLATFORM') or die;

jimport('joomla.factory');

class CatalogoFactory {
	public static function create() {
		return new Catalogos();
	}
}

/**
 * Clase catalogos
 */
class Catalogos {

	public $basic;

	function __construct() {
		$this->basic    = $this->getBasicStatus();
		$this->db       = JFactory::getDbo();
	}

	public function getCurrencies() {
		$query = $this->db->getQuery(true)
			->select('*')
			->from( $this->db->quoteName('#__catalog_currencies') )
			->order( $this->db->quoteName('money_name').' ASC')
			->where( $this->db->quoteName('code'). ' IN (' . getFromTimOne::acceptedCurrenciesList() . ')' );
		$currencies = $this->db->setQuery($query)->loadObjectList();

		return $currencies;
	}

	public function getNacionalidades()
	{
		$query = $this->db->getQuery(true)
			->select('*')
			->from($this->db->quoteName('#__catalog_paises'))
			->order('nombre ASC');
		$result = $this->db->setQuery($query)->loadObjectList();
		
		$this->nacionalidades = $result;
	}
	
	public function getEstados()
	{
		$query = $this->db->getQuery(true)
			->select('*')
			->from($this->db->quoteName('#__catalog_estados'))
			->order('nombre ASC');
		$result = $this->db->setQuery($query)->loadObjectList();

		$this->estados = $result;

        return $result;
	}

	public function permisionLevels()
	{


		$query = $this->db->getQuery(true)
			->select(array($this->db->quoteName('id'),$this->db->quoteName('name')))
			->from($this->db->quoteName('#__catalog_permission_levels'));
		$result = $this->db->setQuery($query)->loadObjectList('id');

		$this->permissionLevels = $result;

        return $result;
	}

	public function getBancos(){
		$cache = & JFactory::getCache();

		$bancos  = $cache->call( array( 'Catalogos', 'getBancosFromTimOne' ) );

		$this->bancos = $bancos;

		return $bancos;
	}

	public function getBancosFromTimOne() {
		$context = stream_context_create(array('http' => array('header'=>'Connection: close')));

		$catalogo = json_decode(@file_get_contents(MIDDLE.TIMONE.'stp/listBankCodes',false,$context));

		if(empty($catalogo)) {
			JFactory::getApplication()->enqueueMessage('El servicio '.MIDDLE.TIMONE.'stp/listBankCodes NO esta funcionando', 'error');
			$cat = null;
		} else {
			foreach ($catalogo as $indice => $objeto) {
				$catalogo2[$objeto->bankCode] = $objeto->name;
			}
			natsort($catalogo2);

			foreach ($catalogo2 as $key=>$value) {
				$objeto = new stdClass;

				$objeto->banco = $value;
				$objeto->clave = $key;
				$objeto->claveClabe = substr($key, -3);

				$cat[] = $objeto;
			}
		}

		return $cat;
	}

	public function getStatusSolicitud()
	{
		$query = $this->db->getQuery(true)
			->select('*')
			->from($this->db->quoteName('#__integrado_status_catalog'));
		$status = $this->db->setQuery($query)->loadObjectList();

		$this->statusSolicitud = $status;

		return $status;
	}

	public function getComisionesTypes () {
		return array('Fija - Recurrente', 'Variable - Por transacción');
	}

	public function getBasicStatus () {
		return array('Desabilitado', 'Habilitado');
	}

	public function getComisionesFrecuencyTimes () {
		return array(7,15,30,60,90,120,180,360);
	}

	public function getTiposPeriodos(){

        $query = $this->db->getQuery(true)
            ->select('*')
            ->from($this->db->quoteName('#__catalog_tipoperiodos'));

        $tipos = $this->db->setQuery($query)->loadObjectList('IdTipo');

        return $tipos;
    }

	public function getCatalogoIVA(){
		$query = $this->db->getQuery(true)
			->select('*')
			->from($this->db->quoteName('#__catalogo_ivas'));

		$catIva = $this->db->setQuery($query)->loadObjectList('valor');

		return $catIva;
	}

	public function getFullIva() {
		$ivas = $this->getCatalogoIVA();

		return $ivas['3']->leyenda;
	}

	public function getPesonalidadesJuridicas() {
		$this->pers_juridica = array( 1 => 'Moral', 2 => 'Física');

		return $this->pers_juridica;
	}

	public function clientTypes(){
		return array(0,2);
	}

	public function providerTypes(){
		return array(1,2);
	}

	public function getPaymentMethods( $onlyActive = true ) {
		$where = $onlyActive ? 'published = 1' : 'id NOT NULL';

		$query = $this->db->getQuery(true)
			->select('*')
		    ->from($this->db->quoteName('#__catalog_payment_methods'))
			->where($where);

		$cat = $this->db->setQuery($query)->loadObjectList('id');

		return $cat;
	}
}
	