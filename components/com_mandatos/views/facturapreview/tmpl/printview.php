<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html.bootstrap');
jimport('integradora.numberToWord');

JHtml::_('behavior.keepalive');

$document = JFactory::getDocument();
$app = JFactory::getApplication();

// Datos
$params = $app->input->getArray();

$integrado = $this->integCurrent->integrados[0];

$number2word = new AifLibNumber();
$document->addStyleSheet( JURI::base() . 'templates/' . $template . '/css/printviewcss.css' );
?>

<div class="hidden-print form-group">
    <?php echo $this->printBtn; ?>
</div>
<table class="table">
    <tr>
        <td>
            <div><img width="200" src="<?php echo JUri::base() . 'images/logo_iecce.png'; ?>"/></div>
        </td>
        <td style="text-align: right">
            <h3 class="text-right">No. <?php echo $this->factura->getId(); ?></h3>
        </td>
    </tr>
</table>

<table class="table" id="data">
    <tr>
        <td colspan="4"><h4><?php echo JText::_('LBL_FACTURA_DE_VENTA'); ?></h4></td>
    </tr>
    <tr>
        <td style="text-align: right; width: 17%;"><?php echo JText::_('LBL_SOCIO_INTEG'); ?></td>
        <td style="text-align: left;"><?php echo $this->integCurrent->getDisplayName(); ?></td>
        <td style="text-align: right;"><?php echo JText::_('LBL_DATE_CREATED'); ?></td>
        <td style="text-align: left; width: 20%;"><?php echo $this->factura->getCreatedDate(); ?></td>
    </tr>
    <tr>
        <td style="text-align: right;"><?php echo JText::_('LBL_PROY'); ?></td>
        <td style="text-align: left;"><?php echo $this->factura->project->name; ?></td>
        <td style="text-align: right;"><?php echo JText::_('LBL_PAYMENT_DATE'); ?></td>
        <td style="text-align: left;"><?php echo $this->factura->paymentDate; ?></td>
    </tr>
    <tr>
        <td style="text-align: right;"><?php echo JText::_('LBL_SUBPROY'); ?></td>
        <td style="text-align: left;"><?php echo isset($this->factura->subProject->name) ?  $this->factura->subProject->name : '';  ?></td>
        <td style="text-align: right;"><?php echo JText::_('LBL_FORMA_PAGO'); ?></td>
        <td style="text-align: left;"><?php echo JText::_($this->factura->paymentMethod->name); ?></td>
    </tr>
    <tr>
        <td style="text-align: right;"><?php echo JText::_('LBL_MONEDA'); ?></td>
        <td style="text-align: left;"><?php echo $this->factura->currency; ?></td>
        <td style="text-align: right;">&nbsp;</td>
        <td style="text-align: left;">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="5"><h5><?php echo JText::_('LBL_HEADER_DATOS_CLIENTE'); ?></h5></td>
    </tr>
    <tr>
        <td style="text-align: right;"><?php echo JText::_('LBL_RAZON_SOCIAL'); ?></td>
        <td style="text-align: left;"><?php echo $this->factura->proveedor->frontName; ?></td>
        <td style="text-align: right;"></td>
        <td style="text-align: left;"></td>
    </tr>
    <tr>
        <td style="text-align: right;"><?php echo JText::_('LBL_RFC'); ?></td>
        <td style="text-align: left;"><?php echo $this->factura->getReceptor()->getIntegradoRfc();; ?></td>
        <td style="text-align: right;"><?php echo JText::_('LBL_BANCOS'); ?></td>
        <td style="text-align: left;"><?php echo @$this->factura->getEmisor()->getAccountData($this->factura->account)->bankName; ?></td>
    </tr>
    <tr>
        <td style="text-align: right;"><?php echo JText::_('COM_MANDATOS_CLIENTES_CONTACT'); ?></td>
        <td style="text-align: left;"><?php echo $this->factura->proveedor->contact; ?></td>
        <td style="text-align: right;"><?php echo JText::_('LBL_BANCO_CUENTA'); ?></td>
        <td style="text-align: left;"><?php echo @$this->factura->getEmisor()->getAccountData($this->factura->account)->banco_cuenta; ?></td>
    </tr>
    <tr>
        <td style="text-align: right;"><?php echo JText::_('COM_MANDATOS_CLIENTES_PHONE'); ?></td>
        <td style="text-align: left;"><?php echo $this->factura->proveedor->phone; ?></td>
        <td style="text-align: right;"><?php echo JText::_('LBL_NUMERO_CLABE'); ?></td>
        <td style="text-align: left;"><?php echo @$this->factura->getEmisor()->getAccountData($this->factura->account)->banco_clabe; ?></td>
    </tr>
    <tr>
        <td style="text-align: right;"><?php echo JText::_('LBL_CORREO'); ?></td>
        <td style="text-align: left;"><?php echo $this->factura->getEmisor()->getIntegradoEmail(); ?></td>
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
        <td>
            <?php echo JText::_('LBL_DATOS_DEPOSITO'); ?>
        </td></tr>
    <tr>
        <td>
            <?php echo JText::_('LBL_AUTORIZO_ODV'); ?>
        </td>
    </tr>
    <tr>
        <td>
            <p class="text-capitalize"><?php echo JText::_('LBL_INTEGRADORA'); ?></p>
            <p><?php echo JText::_('LBL_INTEGRADORA_DIRECCION'); ?></p>
        </td>
    </tr>
</table>
