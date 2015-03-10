<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');
$params = $this->mutuo;
$contenido = JText::_('CONTENIDO_MUTUO');
$date = new DateTime();

$contenido = str_replace('$emisor', '<strong style="color: #000000">'.$params->integradoAcredor->nombre.'</strong>',$contenido);
$contenido = str_replace('$receptor', '<strong style="color: #000000">'.$params->integradoDeudor->nombre.'</strong>',$contenido);
$contenido = str_replace('$totalAmount', '<strong style="color: #000000">$'.number_format($params->totalAmount,2).'</strong>',$contenido);
$contenido = str_replace('$todayDate', '<strong style="color: #000000">'.date('d-m-Y', $date->getTimestamp()).'</strong>',$contenido);

$tabla = json_decode($params->jsonTabla);

$table = '';
$totalInteres = 0;
$totalIVA = 0;

if($params->cuotaOcapital == 0){
    $titulo     = '<h3 style="margin-top: 60px;">ANEXO 3: Tabla de amortizacion a Capital Fijo</h3>';
    $encabezado = '<div class="span3">Abono a Capital: $'.number_format($tabla->capital_fija,2).'</div>';

    foreach ($tabla->amortizacion_capital_fijo as $value) {
        $table .= '<tr class="row">';
        $table .= '<td>'.$value->periodo.'</td>';
        $table .= '<td>$'.number_format($value->inicial, 2).'</td>';
        $table .= '<td>$'.number_format($value->cuota, 2).'</td>';
        $table .= '<td>$'.number_format($value->intiva, 2).'</td>';
        $table .= '<td>$'.number_format($value->intereses, 2).'</td>';
        $table .= '<td>$'.number_format($value->iva, 2).'</td>';
        $table .= '<td>$'.number_format($value->acapital, 2).'</td>';
        $table .= '<td>$'.number_format($value->final, 2).'</td>';
        $table .= '</tr>';

        $totalInteres = $totalInteres + $value->intereses;
        $totalIVA     = $totalIVA + $value->iva;
    }
    $encabezado .= '<div class="span3">Total de interes: $'.number_format($totalInteres,2).'</div>';
    $encabezado .= '<div class="span3">Total de IVA: $'.number_format($totalIVA,2).'</div>';

}elseif($params->cuotaOcapital == 1){
    $titulo = '<h3 style="margin-top: 60px;">ANEXO 3:Tabla de amortizacion a Cuota Fija</h3>';
    $encabezado = '<div class="span3">Monto de la Cuota: $'.number_format($tabla->cuota_Fija,2).'</div>';

    foreach ($tabla->amortizacion_cuota_fija as $value) {
        $table .= '<tr class="row">';
        $table .= '<td>'.$value->periodo.'</td>';
        $table .= '<td>$'.number_format($value->inicial, 2).'</td>';
        $table .= '<td>$'.number_format($value->cuota, 2).'</td>';
        $table .= '<td>$'.number_format($value->intiva, 2).'</td>';
        $table .= '<td>$'.number_format($value->intereses, 2).'</td>';
        $table .= '<td>$'.number_format($value->iva, 2).'</td>';
        $table .= '<td>$'.number_format($value->acapital, 2).'</td>';
        $table .= '<td>$'.number_format($value->final, 2).'</td>';
        $table .= '</tr>';

        $totalInteres = $totalInteres + $value->intereses;
        $totalIVA     = $totalIVA + $value->iva;
    }
    $encabezado .= '<div class="span3">Total de interes: $'.number_format($totalInteres,2).'</div>';
    $encabezado .= '<div class="span3">Total de IVA: $'.number_format($totalIVA,2).'</div>';
}

?>
<style>
    .form-group {
        margin-bottom: 1em;
    }
    #gk-logo img {
        height: 70px;
    }
    .esconder {
        display: none;
    }
    .forceinline {
        display: inline-block !important;
    }
    .proyectos{
        width: 100%;
        display: inline-table;
        margin-top: 50px;
    }
    li.proyectoslist {
        list-style-type: disc;
    }
    .columnas:nth-of-type(2) {
        position: absolute;
        left: 700px;
    }
    .columnas:nth-of-type(3) {
        position: absolute;
        left: 820px;
    }
    .filas {
        display: inline-flex;
        height: 30px;
    }
    .status0 {
        color:#FF0000;
    }
    .etiqueta{
        font-weight: bolder;
        color: #000000;
    }
    .btn-baja{
        border-radius: 15px;
    }
    table.tablesorter thead tr .header {
        background-image: url('../../../libraries/integradora/js/themes/blue/bg.gif');
        background-repeat: no-repeat;
        background-position: center right;
        cursor: pointer;
    }
    table.tablesorter thead tr .headerSortUp {
        background-image: url('../../../libraries/integradora/js/themes/blue/asc.gif');
    }
    table.tablesorter thead tr .headerSortDown {
        background-image: url('../../../libraries/integradora/js/themes/blue/desc.gif');
    }
    .tableOrders th, .tableOrders td {
        text-align: center;
        vertical-align: middle;
    }
    .alto{
        height: 34px !important;
    }
    .col-md-4 {
        float: left;
        margin: 15px 20px;
    }
    #msg_busqueda{
        display: none;
    }
    .text-uppercase {
        text-transform: uppercase;
    }
    .bordes-box {
        border: 1px solid #ccc;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.024) inset;
        color: #999;
    }
    .factura{
        margin-left: 37px;
    }

    .margen-fila {
        margin: 20px 0;
    }
    #odv {
        display: table;
        border: 1px solid #000;
        width: 95%;
        text-align: center;
        margin: 0 auto;
    }
    .contenidos, .head, #content {
        display: table-row;
    }
    #columna1, #columna2{
        display: table-cell;
        border: 1px solid #000;
        vertical-align: middle;
        padding: 5px;
    }
    .cantidades{
        width: 70px;
    }
    input.cantidad, input.iva, input.ieps{
        width: 38px;
    }
    select.productos{
        width: 80px;
    }


    @media print {
        #system-message-container, #gk-print-top, .btn {
            display: none;
        }
    }
    #gk-print-top {
        display: none;
    }

    .divdetalleODC {
    }
    .tt-dropdown-menu{
        position: absolute;
        top: 100%;
        left: 0px;
        z-index: 1000048;
        right: auto;
        display: none;
        width: 100%;
        background-color: rgb(255, 255, 255);
    }

    .contenedor_mutuo{
        margin: 0 auto;
        text-align: justify;
    }
    .cabeceras_mutuo, .t-center{
        text-align: center;
    }

    .table-mutuo {
        width: 100%;
        text-align: center;
        margin-top: 50px;
    }
    .expiration{
        text-align: center;
        font-size: 14px;
        font-weight: bolder;
    }
    .num {
        text-align: right;
    }
    .row-separator {
        padding: 1em 0;
    }

    /* Fix para los inputs de fecha */
    select, textarea, input[type="text"], input[type="password"], input[type="datetime"], input[type="datetime-local"], input[type="date"], input[type="month"], input[type="time"], input[type="week"], input[type="number"], input[type="email"], input[type="url"], input[type="search"], input[type="tel"], input[type="color"], .uneditable-input {
        box-sizing: initial;
    }
</style>

<div class="row1 clearfix">
    <div class="span3">
        <img src="../integradora/images/logo_iecce.png" alt="Integradora - ">
    </div>
    <div class="span7" style="text-align: right; font-size: 18px; padding-top: 30px; font-weight: bolder;">
        No. Orden <?php echo $params->id; ?>
    </div>
</div>

<div class="row1 clearfix" style="padding-top: 80px;">
    <div class="span12">
        <?php echo $contenido; ?>
    </div>

    <div class="tabla-amortizacion">
        <div class="clearfix">
            <?php echo $titulo; ?>
        </div>
        <?php echo $encabezado; ?>
        <table class="table table-bordered" style="width: 100%; text-align: center;">
            <thead>
            <tr class="row">
                <th>Periodo</th>
                <th>Saldo Inicial</th>
                <th>Couta</th>
                <th>Interes con IVA</th>
                <th>Interes</th>
                <th>IVA</th>
                <th>Abono a Capital</th>
                <th>Saldo Final</th>
            </tr>
            </thead>
            <tbody>
            <?php echo $table; ?>
            </tbody>
        </table>
    </div>
</div>