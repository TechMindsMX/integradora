<?php
/**
 * Created by PhpStorm.
 * User: dev-ismael
 * Date: 27/08/2015
 * Time: 09:32 AM
 */

defined('JPATH_PLATFORM') or die;

define('JPATH_BASE', realpath(dirname(__FILE__).'/../..'));
jimport('joomla.html.html.bootstrap');
jimport('integradora.numberToWord');
JHtml::_('behavior.keepalive');
jimport('integradora.integralib.order');
jimport('integradora.integrado');
jimport('integradora.gettimone');
require('html2pdf.class.php');
require('_class/Facpdf.php');
require('_class/odcPdf.php');
require('_class/oddPdf.php');
require('_class/odrPdf.php');
require('_class/mutuosPDF.php');
require('_class/odpPdf.php');
require('_class/odvPdf.php');