<?php
defined('_JEXEC') or die;

/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 09-Sep-15
 * Time: 12:04 PM
 */

require_once dirname(__FILE__) . '/helper.php';


$exrate = ModExchangeRateHelper::getExchangeRate($params);
require JModuleHelper::getLayoutPath('mod_exchange_rate');