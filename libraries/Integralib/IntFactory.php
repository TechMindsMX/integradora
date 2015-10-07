<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 25-Mar-15
 * Time: 12:50 PM
 */
namespace Integralib;

jimport('integradora.integrado');

use Integralib\ExtendedUser;

class IntFactory {

	public static function getTimoneRequest(\urlAndType $datosEnvio, $objEnvio) {
		return new TimOneRequest($datosEnvio, $objEnvio);
	}

	/**
	 * @param $controller
	 * @param $service
	 * @param $action
	 *
	 * @return \urlAndType
	 */
	public static function getServiceRoute($controller, $service, $action) {
		$r = new \servicesRoute();
		return $r->getUrlService($controller, $service, $action);
	}

	public static function getValidator() {
		return new Validator();
	}

	public static function getsUserSecurity( $instance ) {
        //TODO: Modificado por Lutek
		$userSecurity = new UserSecurity();
        return $userSecurity->getUserAnswers($instance);
	}

	/**
	 * @param null $id
	 *
	 * @return \JUser
     */
    public static function getExtendedUser($id = null)
	{
		$u = new ExtendedUser($id);
		return $u->getUser();
	}

	public static function getIntegrdoSimple($integradoId)
	{
		return new \IntegradoSimple($integradoId);
	}
}