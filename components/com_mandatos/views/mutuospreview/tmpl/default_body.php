<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');
$params = $this->mutuo;
$contenido = JText::_('CONTENIDO_MUTUO');

$contenido = str_replace('$emisor', '<strong style="color: #000000">'.$params->integradoEmisor.'</strong>',$contenido);
$contenido = str_replace('$receptor', '<strong style="color: #000000">'.$params->integradoReceptor.'</strong>',$contenido);
$contenido = str_replace('$totalAmount', '<strong style="color: #000000">$'.number_format($params->totalAmount,2).'</strong>',$contenido);
$contenido = str_replace('$expirationDate', '<strong style="color: #000000">28-03-2014</strong>',$contenido);

$tabla = json_decode($params->jsonTabla);
var_dump($params);
?>

<div class="row1 clearfix">
    <div class="span3">
        <img src="integradora/images/logo_iecce.png" alt="Integradora - ">
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

        <?php
        $table = '';
        if($params->cuotaOcapital == 0){
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

            }
        }elseif($params->cuotaOcapital == 1){
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

            }
        }
        ?>
        <table class="table-bordered" style="width: 100%;">
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