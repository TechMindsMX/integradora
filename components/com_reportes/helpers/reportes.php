<?php
defined('_JEXEC') or die('Restricted Access');

class ReportesHelper {

	public static function getPrintBtn( $url ) {
		return getFromTimOne::generatePrintButton( $url );
	}
}