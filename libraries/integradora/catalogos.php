<?php
use Integralib\TimOneCurl;
use Integralib\TimOneRequest;

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

    public static function getValidFacturaFolioSeries() {
        return array('default' => 'B', 'comisiones' => 'C');
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

    public function getStateIdByName($stateName){
        $estados = $this->getEstados();
        $retorno = '';

        foreach ($estados as $key => $value) {
            $haystack = strtolower($this->removeAccents($stateName));
            $needle = strtolower($this->removeAccents($value->nombre));

            if( strpos( $haystack, $needle )  != false){
                $retorno = $value->id;
            }
        }
        return $retorno;
    }

    function removeAccents($str) {
        $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ', 'Ά', 'ά', 'Έ', 'έ', 'Ό', 'ό', 'Ώ', 'ώ', 'Ί', 'ί', 'ϊ', 'ΐ', 'Ύ', 'ύ', 'ϋ', 'ΰ', 'Ή', 'ή');
        $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o', 'Α', 'α', 'Ε', 'ε', 'Ο', 'ο', 'Ω', 'ω', 'Ι', 'ι', 'ι', 'ι', 'Υ', 'υ', 'υ', 'υ', 'Η', 'η');
        return str_replace($a, $b, $str);
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

    public static function getBancosFromTimOne() {
        $session = JFactory::getSession();

	    $cat = $session->get('bancos', null, 'catalogos');

	    if( is_null($cat) ) {
            $curlRequest = new TimOneRequest();

            $catalogo = $curlRequest->getListBankCodes();

	        if (empty($catalogo)) {
                JFactory::getApplication()->enqueueMessage('El servicio ' . MIDDLE . TIMONE . 'stp/listBankCodes NO esta funcionando', 'error');
                $cat = null;
            } else {
                foreach ($catalogo as $indice => $objeto) {
                    $catalogo2[$objeto->bankCode] = $objeto->name;
                }
                natsort($catalogo2);

                foreach ($catalogo2 as $key => $value) {
                    $objeto = new stdClass;

                    $objeto->banco = $value;
                    $objeto->clave = $key;
                    $objeto->claveClabe = substr($key, -3);

                    $cat[] = $objeto;
                }
            }

            $session->set('bancos', $cat, 'catalogos');
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
	