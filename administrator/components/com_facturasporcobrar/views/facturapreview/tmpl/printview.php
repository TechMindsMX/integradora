<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html.bootstrap');
jimport('integradora.numberToWord');
JHtml::_('behavior.keepalive');

$document = JFactory::getDocument();
$app = JFactory::getApplication();

// Datos
$params = $app->input->getArray();

$number2word = new AifLibNumber();
$document->addStyleSheet( '../templates/meet_gavern/css/printviewcss.css' );
?>
<style>
    body {
        color: #777 !important;
        font-weight: normal!important;
        line-height: 24.05px!important;
    }
</style>
<div class="hidden-print form-group">
    <?php echo $this->printBtn; ?>
</div>

<table class="table">
    <tr>
        <td>
            <div><img width="200" src="<?php echo JUri::base() . '../images/logo_iecce.png'; ?>"/></div>
        </td>
        <td style="text-align: right">
            <h3 class="text-right">No. <?php echo $this->factura->id; ?></h3>
        </td>
    </tr>
</table>

<table class="table" id="data">
    <tr>
        <td colspan="4"><h4><?php echo JText::_('LBL_FACTURA_DE_VENTA'); ?></h4></td>
    </tr>
    <tr>
        <td style="text-align: right; width: 17%;"><?php echo 'Socio Integrado'; ?></td>
        <td style="text-align: left;"><?php echo $this->factura->integradoName; ?></td>
        <td style="text-align: right;"><?php echo 'Fecha de elaboración'; ?></td>
        <td style="text-align: left; width: 20%;"><?php echo $this->factura->createdDate; ?></td>
    </tr>
    <tr>
        <td style="text-align: right;"><?php echo 'Proyecto'; ?></td>
        <td style="text-align: left;"><?php echo $this->factura->proyecto->name; ?></td>
        <td style="text-align: right;"><?php echo 'Fecha de pago'; ?></td>
        <td style="text-align: left;"><?php echo $this->factura->paymentDate; ?></td>
    </tr>
    <tr>
        <td style="text-align: right;"><?php echo 'Sub-proyecto'; ?></td>
        <td style="text-align: left;"><?php echo isset($this->factura->sub_proyecto->name) ? $this->factura->sub_proyecto->name : ''; ?></td>
        <td style="text-align: right;"><?php echo 'Forma de pago'; ?></td>
        <td style="text-align: left;"><?php echo JText::_($this->factura->paymentMethod->name); ?></td>
    </tr>
    <tr>
        <td style="text-align: right;"><?php echo 'Moneda'; ?></td>
        <td style="text-align: left;"><?php //echo $this->factura->currency; ?></td>
        <td style="text-align: right;"></td>
        <td style="text-align: left;"></td>
    </tr>
    <tr>
        <td colspan="5"><h5><?php echo JText::_('LBL_HEADER_DATOS_CLIENTE'); ?></h5></td>
    </tr>
    <tr>
        <td style="text-align: right;"><?php echo 'Denominación o Razón social'; ?></td>
        <td style="text-align: left;"><?php echo $this->factura->proveedor->corporateName; ?></td>
        <td style="text-align: right;"></td>
        <td style="text-align: left;"></td>
    </tr>
    <tr>
        <td style="text-align: right;"><?php echo 'RFC'; ?></td>
        <td style="text-align: left;"><?php echo $this->factura->proveedor->pRFC; ?></td>
        <td style="text-align: right;">&nbsp;</td>
        <td style="text-align: left;">&nbsp;</td>
    </tr>
    <tr>
        <td style="text-align: right;"><?php echo 'Contacto'; ?></td>
        <td style="text-align: left;"><?php echo $this->factura->proveedor->contact; ?></td>
        <td style="text-align: right;">&nbsp;</td>
        <td style="text-align: left;">&nbsp;</td>
    </tr>
    <tr>
        <td style="text-align: right;"><?php echo 'Teléfono'; ?></td>
        <td style="text-align: left;"><?php echo $this->factura->proveedor->phone; ?></td>
        <td style="text-align: right;">&nbsp;</td>
        <td style="text-align: left;">&nbsp;</td>
    </tr>
    <tr>
        <td style="text-align: right;"><?php echo 'Correo electrónico'; ?></td>
        <td style="text-align: left;"></td>
        <td style="text-align: right;"></td>
        <td style="text-align: left;"></td>
    </tr>
    <tr>
        <td colspan="4"><h6><?php echo JText::_('LBL_DESCRIP_PRODUCTOS'); ?></h6></td>
    </tr>
</table>

<table class="table table-bordered">
    <thead>
    <tr>
        <th class="span1">#</th>
        <th class="span1"><?php echo 'Cantidad'; ?></th>
        <th class="span4"><?php echo 'Descripción'; ?></th>
        <th class="span2"><?php echo 'Unidad'; ?></th>
        <th class="span2"><?php echo 'Precio Unitario'; ?></th>
        <th class="span2"><?php echo 'Importe'; ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($this->factura->productosData as $key => $prod) :
        $iva = $prod->iva;
        ?>
        <tr>
            <td><?php  echo $key; ?></td>
            <td><?php echo $prod->cantidad; ?></td>
            <td><?php echo $prod->descripcion; ?></td>
            <td><?php echo $prod->unidad; ?></td>
            <td><div class="text-right"><?php echo number_format($prod->p_unitario, 2); ?></div></td>
            <td><div class="text-right"><?php echo number_format(floatval($prod->cantidad) * floatval($prod->p_unitario), 2); ?></div></td>
        </tr>
    <?php
    endforeach;
    ?>
    <tr>
        <td colspan="4" rowspan="3">
            <?php echo 'Cantidad en letra:'; ?>
            <span><?php echo $number2word->toCurrency('$' . number_format($this->factura->totalAmount + $this->factura->iva, 2)); ?></span>
        </td>
        <td class="span2">
            <?php echo 'SubTotal'; ?>
        </td>
        <td>
            <div class="text-right">
                <?php echo number_format($this->factura->totalAmount, 2); ?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="span2">
            <?php echo ($iva ) . '% ' . 'IVA'; ?>
        </td>
        <td>
            <div class="text-right">
                <?php echo number_format($this->factura->iva, 2); ?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="span2">
            <?php echo 'Monto Total'; ?>
        </td>
        <td>
            <div class="text-right">
                <?php echo number_format($this->factura->totalAmount +  $this->factura->iva, 2); ?>
            </div>
        </td>
    </tr>
    </tbody>
</table>

<table class="table" id="printFooter">
    <tr>
        <td>
            <?php echo JText::_('LBL_CON_FACTURA'); ?>
        </td></tr>
    <tr>
        <td style="text-align: center;"><?php echo JText::_('LBL_AUTORIZO_FACTURA'); ?></td>
    </tr>
    <tr>
        <td style="text-align: center;">
            <p class="text-capitalize"><?php echo JText::_('LBL_INTEGRADORA'); ?></p>
            <p><?php echo JText::_('LBL_INTEGRADORA_DIRECCION'); ?></p>
        </td>
    </tr>
</table>
