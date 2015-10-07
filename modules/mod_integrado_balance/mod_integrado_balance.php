<?php
defined('_JEXEC') or die;

/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 09-Sep-15
 * Time: 12:04 PM
 */

require_once dirname(__FILE__) . '/helper.php';

$cache = JFactory::getCache();
$cache->setCaching(true);
$cache->setLifeTime(60);

$balances = $cache->call( array( 'ModIntegradoBalanceHelper', 'getBalances' ) );

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

require JModuleHelper::getLayoutPath('mod_integrado_balance');