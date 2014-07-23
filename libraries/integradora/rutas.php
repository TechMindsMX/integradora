<?php
defined('JPATH_PLATFORM') or die;

jimport('joomla.factory');


/**
 * Clase rutas de servicios
 */
class IntRoute {
	
	protected $ssl;
	
	protected $ip = '192.168.0.122';
	
	protected $port = ':7272';
	
	protected $urls;
	
	public function getUrl($comp,$param) {
		
		$juri = JUri::getInstance();
		
		$this->ssl = $juri->getScheme();
		
		$componentes = array('integrado', 'mandatos','comisiones');
		
		if (in_array($comp, $componentes)) {
			switch ($comp) {
				case $componentes[0]:
					// componente Integrados
					switch ($param) {
						case 'solicitud':
							$urls->post = $this->ssl.'://'.$this->ip.$this->port.DIRECTORY_SEPARATOR.'index.php?option=com_';
							break;
						
						default:
							$urls->post = 'index.php?option=com_';
							break;
					}
					break;
				
				default:
					$urls = false;
					break;
			}
		}
		
		return $urls;
	}
	
}
