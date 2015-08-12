<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 13-May-15
 * Time: 10:09 AM
 */

namespace Integralib;


class IntegradoHelper {

	public static function filterEmptyIntegrados( $items ) {
		foreach ( $items as $k => $i ) {
			if ( !is_a( $i , 'IntegradoSimple') ) {
				$i = new \IntegradoSimple($i->integradoId);
			}
			if ( !$i->hasRfc() ) {
				unset($items[$k]);
			};
		}
		return $items;
	}
}