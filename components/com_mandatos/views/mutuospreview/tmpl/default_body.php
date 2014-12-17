<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');
$params = $this->mutuo;
$contenido = JText::_('CONTENIDO_MUTUO');

$contenido = str_replace('$emisor', '<strong style="color: #000000">'.$params->integradoAcredor->nombre.'</strong>',$contenido);
$contenido = str_replace('$receptor', '<strong style="color: #000000">'.$params->integradoDeudor->nombre.'</strong>',$contenido);
$contenido = str_replace('$totalAmount', '<strong style="color: #000000">$'.number_format($params->totalAmount,2).'</strong>',$contenido);
$contenido = str_replace('$expirationDate', '<strong style="color: #000000">28-03-2014</strong>',$contenido);

$tabla = json_decode($params->jsonTabla);

$table = '';
$totalInteres = 0;
$totalIVA = 0;

if($params->cuotaOcapital == 0){
    $titulo     = '<h3 style="margin-top: 60px;">Tabla de amortizacion a Capital Fijo</h3>';
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
    $titulo = '<h3 style="margin-top: 60px;">Tabla de amortizacion a Cuota Fija</h3>';
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

<div class="row1 clearfix">
    <div class="span3">
        <img src="/integradora/images/logo_iecce.png" alt="Integradora - ">
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
        <table class="table-bordered" style="width: 100%; text-align: center;">
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