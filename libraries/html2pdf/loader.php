<?php
/**
 * Created by PhpStorm.
 * User: dev-ismael
 * Date: 27/08/2015
 * Time: 09:32 AM
 */

defined('JPATH_PLATFORM') or die;
define('JPATH_BASE_HTML2PDF', realpath(dirname(__FILE__).'/../..'));


jimport('joomla.html.html.bootstrap');
jimport('integradora.numberToWord');
JHtml::_('behavior.keepalive');
jimport('integradora.integralib.order');
jimport('integradora.integrado');
jimport('integradora.gettimone');
require('html2pdf.class.php');

if( ($_GET['task'] == 'odvpreview.authorize') || ($_GET['task'] == 'odcpreview.authorize') ){
    require('_class/Facpdf.php');
}

require('_class/odcPdf.php');
require('_class/oddPdf.php');
require('_class/odrPdf.php');
require('_class/mutuosPDF.php');
require('_class/odpPdf.php');
require('_class/odvPdf.php');
require('_class/cashoutPDF.php');
require('_class/cashinPDF.php');
require('_class/flujoPDF.php');
require('_class/resultPDF.php');