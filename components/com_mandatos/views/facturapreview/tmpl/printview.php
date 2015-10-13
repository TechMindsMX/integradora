<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html.bootstrap');
jimport('integradora.numberToWord');

JHtml::_('behavior.keepalive');

$document = JFactory::getDocument();
$app = JFactory::getApplication();

// Datos
$params      = $app->input->getArray();
$integrado   = $this->integCurrent->integrados[0];
$xml         = $this->factura->datosXML;
$integradora = new \Integralib\Integrado();
$integradora = new IntegradoSimple( $integradora->getIntegradoraUuid() );
$number2word = new AifLibNumber();
$document->addStyleSheet( JURI::base() . 'templates/' . $template . '/css/printviewcss.css' );
$fechaHOra = explode('T',$xml->comprobante['FECHA']);
$fecha = explode('-',$fechaHOra[0]);
$fecha = $fecha[2].'/'.$fecha[1].'/'.$fecha[0];
$hora = $fechaHOra[1];
?>

<div class="hidden-print form-group">
    <?php echo $this->printBtn; ?>
</div>
<table class="table">
    <tr>
        <td>
            <div><img width="200" src="<?php echo JUri::base() . 'images/logo_iecce.png'; ?>"/></div>
        </td>
</table>

<table class="table" id="data">
    <tr>
        <td colspan="4"><h4><?php echo JText::_('LBL_FACTURA_DE_VENTA'); ?></h4></td>
    </tr>
    <tr>
        <td style="text-align: left; padding-left: 35px;" colspan="4"><h4><?php echo $integradora->getDisplayName(); ?></h4></td>
    </tr>
    <tr>
        <td style="text-align: right; width: 17%;"><?php echo $integradora->integrados[0]->datos_empresa->rfc ?></td>
        <td style="text-align: left;"></td>
        <td style="text-align: right;"><?php echo JText::_('LBL_FOLIO'); ?></td>
        <td style="text-align: left; width: 20%;"><?php echo $xml->comprobante['FOLIO']; ?></td>
    </tr>
    <tr>
        <td style="text-align: left; padding-left: 35px;" colspan="2"><?php echo $integradora->integrados[0]->address; ?></td>
        <td style="text-align: right;"><?php echo JText::_('LBL_ELABORATION'); ?></td>
        <td style="text-align: left;"><?php echo $fecha ?></td>
    </tr>
    <tr>
        <td style="text-align: left;padding-left: 35px;" colspan="2"><?php echo JText::_('LBL_MONEDA').': '.$this->factura->currency; ?></td>
        <td style="text-align: right;">&nbsp;</td>
        <td style="text-align: left;"><?php echo $hora; ?></td>
    </tr>
    <tr>
        <td style="text-align: left; padding-left: 36px; padding-top: 15px;" colspan="4">
            <p><?php echo $this->factura->getReceptor()->getIntegradoRfc().' - '.$this->factura->proveedor->frontName; ?></p>
            <p>
                <?php echo $xml->receptor['children'][0]['attrs']['CALLE']; ?>
            </p>
        </td>
    </tr>
</table>

<table class="table table-bordered">
    <thead>
    <tr>
        <th class="span1">#</th>
        <th class="span1"><?php echo JText::_('LBL_CANTIDAD'); ?></th>
        <th class="span4"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_DESCRIPTION'); ?></th>
        <th class="span2"><?php echo JText::_('LBL_UNIDAD'); ?></th>
        <th class="span2"><?php echo JText::_('LBL_P_UNITARIO'); ?></th>
        <th class="span2"><?php echo JText::_('LBL_IMPORTE'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($this->factura->productosData as $key => $prod) :
        $ivasProd = array();
        array_push($ivasProd, floatval($prod->iva) );
        ?>
        <tr>
            <td><?php echo $key+1; ?></td>
            <td><?php echo $prod->cantidad; ?></td>
            <td><?php echo $prod->descripcion; ?></td>
            <td><?php echo $prod->unidad; ?></td>
            <td>
                <div class="text-right">
                    <?php echo number_format($prod->p_unitario, 2); ?>
                </div>
            </td>
            <td>
                <div class="text-right">
                    <?php echo number_format(floatval($prod->cantidad) * floatval($prod->p_unitario), 2); ?>
                </div>
            </td>
        </tr>
    <?php
    endforeach;
    ?>
    <tr>
        <td colspan="4" rowspan="3">
            <?php echo JText::_('LBL_MONTO_LETRAS'); ?>
            <span><?php echo $number2word->toCurrency('$' . number_format($this->factura->getTotalAmount(), 2)); ?></span>
        </td>
        <td class="span2">
            <?php echo JText::_('LBL_SUBTOTAL'); ?>
        </td>
        <td>
            <div class="text-right">
                <?php echo number_format($this->factura->subTotalAmount, 2); ?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="span2">
            <?php echo array_sum($ivasProd)/count($ivasProd) . '% ' . JText::_('COM_MANDATOS_PRODUCTOS_LBL_IVA'); ?>
        </td>
        <td>
            <div class="text-right">
                <?php echo number_format($this->factura->iva, 2); ?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="span2">
            <?php echo JText::_('LBL_TOTAL'); ?>
        </td>
        <td>
            <div class="text-right">
                <?php echo number_format($this->factura->getTotalAmount(), 2); ?>
            </div>
        </td>
    </tr>
    </tbody>
</table>

<table class="table" id="printFooter">
    <tr>
        <td colspan="4">
            <?php echo JText::sprintf('LBL_AUTORIZO_ODV',$this->integCurrent->getDisplayName(),$this->integCurrent->getIntegradoRfc()); ?>
        </td>
    </tr>
    <tr>
        <td colspan="4" style="border-top: 1px solid rgba(0, 0, 0, 0.21); margin-top: 2px;">&nbsp;</td>
    </tr>
    <tr>
        <td style="text-align: right; width: 20%;"><strong><?php echo JText::_('LBL_FOLIO_FISCAL'); ?></strong> </td>
        <td style="text-align: left;"><?php echo $xml->complemento['children'][0]['attrs']['UUID']; ?></td>
        <td style="text-align: right;" colspan="2" rowspan="5">
            <img src="media/qrCodes/<?php echo $this->factura->qrName; ?>">
        </td>
    </tr>
    <tr>
        <td style="text-align: right; width: 20%;"><strong><?php echo JText::_('LBL_ISSUING_SERIES_NUMBER'); ?></strong> </td>
        <td style="text-align: left;"><?php echo $xml->comprobante['NOCERTIFICADO']; ?></td>
    </tr>
    <tr>
        <td style="text-align: right; width: 20%;"><strong><?php echo JText::_('LBL_CERTIF_DATE'); ?></strong>:</td>
        <td style="text-align: left;"><?php echo $fecha.' '.$hora; ?></td>
    </tr>
    <tr>
        <td style="text-align: right; width: 20%;"><strong><?php echo JText::_('LBL_CREATION_DATETIME'); ?></strong> </td>
        <td style="text-align: left;"><?php echo $fecha.' '.$hora; ?></td>
    </tr>
    <tr>
        <td style="text-align: right; width: 20%;"><strong><?php echo JText::_('LBL_SAT_SERIES_NUMBER'); ?></strong> </td>
        <td style="text-align: left;"><?php echo $xml->complemento['children'][0]['attrs']['NOCERTIFICADOSAT']; ?></td>
    </tr>
    <tr>
        <td colspan="4">
            <p><span><strong><?php echo JText::_('LBL_CFDI_STAMP'); ?></strong></span></p>
            <p style="font-size: 5px;"><?php echo $xml->complemento['children'][0]['attrs']['SELLOCFD']; ?></p>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <p><span><strong><?php echo JText::_('LBL_SAT_ORIGINAL_STRING'); ?></strong></span></p>
            <p style="font-size: 5px;"><?php echo chunk_split($xml->comprobante['CERTIFICADO'], 200); ?></p>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <p><span><strong><?php echo JText::_('LBL_SAT_STAMP'); ?></strong></span></p>
            <p style="font-size: 5px;"><?php echo $xml->complemento['children'][0]['attrs']['SELLOSAT']; ?></p>
        </td>
    </tr>
</table>
