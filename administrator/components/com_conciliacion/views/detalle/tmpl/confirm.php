<?php
defined('_JEXEC') or die('Restricted Access');

$vars = JFactory::getApplication()->input->getArray(array('txId' => 'int', 'orderId' => 'int', 'órderType' => 'string' ));
var_dump($vars, $this);