<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html.bootstrap');
jimport('integradora.numberToWord');

JHtml::_('behavior.keepalive');

$document	= JFactory::getDocument();
$app 		= JFactory::getApplication();

// Datos
$params 	= $app->input->getArray();

$integrado 	= $this->integCurrent->integrados[0];

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
            <h3 class="text-right">No. <?php echo $this->odv->numOrden; ?></h3>
        </td>
    </tr>
</table>
<table class="table" id="data">
    <tr>
        <td colspan="4"><h4><?php echo JText::_('LBL_ORDEN_DE_VENTA'); ?></h4></td>
    </tr>
    <tr>
        <td style="text-align: right; width: 17%;"><?php echo JText::_('LBL_SOCIO_INTEG'); ?></td>
        <td style="text-align: left;"><?php echo $this->odv->getEmisor()->getDisplayName(); ?></td>
        <td style="text-align: right;"><?php echo JText::_('LBL_DATE_CREATED'); ?></td>
        <td style="text-align: left; width: 20%;"><?php echo $this->odv->getCreatedDate(); ?></td>
    </tr>
    <tr>
        <td style="text-align: right;"><?php echo JText::_('LBL_PROY'); ?></td>
        <td style="text-align: left;"><?php echo isset( $this->odv->proyecto->name ) ? $this->odv->proyecto->name : ''; ?></td>
        <td style="text-align: right;"><?php echo JText::_('LBL_PAYMENT_DATE'); ?></td>
        <td style="text-align: left;"><?php echo $this->odv->paymentDate; ?></td>
    </tr>
    <tr>
        <td style="text-align: right;"><?php echo JText::_('LBL_SUBPROY'); ?></td>
        <td style="text-align: left;"><?php echo isset($this->odv->subproyecto->name) ? $this->odv->subproyecto->name : ''; ?></td>
        <td style="text-align: right;"><?php echo JText::_('LBL_FORMA_PAGO'); ?></td>
        <td style="text-align: left;"><?php echo JText::_($this->odv->paymentMethod->name); ?></td>
    </tr>
    <tr>
        <td style="text-align: right;"><?php echo JText::_('LBL_MONEDA'); ?></td>
        <td style="text-align: left;"><?php echo isset($this->odv->currency) ? $this->odv->currency : 'MXN'; ?></td>
        <td style="text-align: right;"><?php echo JText::_('LBL_BANCO_CUENTA'); ?></td>
        <td style="text-align: left;"><?php echo isset($this->odv->account[0]->banco_cuenta) ? 'XXXXXX' . substr($this->odv->account[0]->banco_cuenta, -4, 4) : ''; ?></td>
    </tr>
    <tr>
        <td colspan="5"><h5><?php echo JText::_('LBL_HEADER_DATOS_CLIENTE'); ?></h5></td>
    </tr>
    <tr>
        <td style="text-align: right;"><?php echo JText::_('LBL_RAZON_SOCIAL'); ?></td>
        <td style="text-align: left;"><?php echo $this->odv->getReceptor()->getDisplayName(); ?></td>
        <td style="text-align: right;">&nbsp;</td>
        <td style="text-align: left;">&nbsp;</td>
    </tr>
    <tr>
        <td style="text-align: right;"><?php echo JText::_('LBL_RFC'); ?></td>
        <td style="text-align: left;"><?php echo $this->odv->getReceptor()->getIntegradoRfc(); ?></td>
        <td style="text-align: right;">&nbsp;</td>
        <td style="text-align: left;">&nbsp;</td>
    </tr>
    <tr>
        <td style="text-align: right;"><?php echo JText::_('COM_MANDATOS_CLIENTES_CONTACT'); ?></td>
        <td style="text-align: left;"><?php echo $this->odv->getReceptor()->getContactName(); ?></td>
        <td style="text-align: right;">&nbsp;</td>
        <td style="text-align: left;">&nbsp;</td>
    </tr>
    <tr>
        <td style="text-align: right;"><?php echo JText::_('COM_MANDATOS_CLIENTES_PHONE'); ?></td>
        <td style="text-align: left;"><?php echo $this->odv->getReceptor()->getIntegradoPhone(); ?></td>
        <td style="text-align: right;">&nbsp;</td>
        <td style="text-align: left;">&nbsp;</td>
    </tr>
    <tr>
        <td style="text-align: right;"><?php echo JText::_('LBL_CORREO'); ?></td>
        <td style="text-align: left;"><?php echo $this->odv->getReceptor()->getIntegradoEmail(); ?></td>
        <td style="text-align: right;">&nbsp;</td>
        <td style="text-align: left;">&nbsp;</td>
    </tr>
</table>
<div class="clearfix"><h6><?php echo JText::_('LBL_DESCRIP_PRODUCTOS'); ?></h6></div>
<table class="table table-bordered">
    <thead>
    <tr>
        <th class="span1">#</th>
        <th class="span2"><?php echo JText::_('LBL_CANTIDAD'); ?></th>
        <th class="span4"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_DESCRIPTION'); ?></th>
        <th class="span1"><?php echo JText::_('LBL_UNIDAD'); ?></th>
        <th class="span2"><?php echo JText::_('LBL_P_UNITARIO'); ?></th>
        <th class="span2"><?php echo JText::_('LBL_IMPORTE'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($this->odv->productosData as $key => $prod) :
        ?>
        <tr>
            <td><?php echo $key+1; ?></td>
            <td><?php if ( ! empty( $prod->cantidad ) ) {
                    echo $prod->cantidad;
                } ?></td>
            <td><?php if ( ! empty( $prod->descripcion ) ) {
                    echo '<strong>'. $prod->producto .'</strong><br />'. $prod->descripcion;
                } ?></td>
            <td><?php if ( ! empty( $prod ) ) {
                    echo $prod->unidad;
                } ?></td>
            <td><div class="text-right">$
                    <?php if ( ! empty( $prod->p_unitario ) ) {
                        echo number_format($prod->p_unitario,2);
                    } ?>
                </div></td>
            <td><div class="text-right">$
                    <?php if ( ! empty( $prod->cantidad ) ) {
                        echo number_format(floatval($prod->cantidad) * floatval($prod->p_unitario),2);
                    } ?>
                </div></td>
        </tr>
    <?php
    endforeach;
    ?>
    <tr>
        <td colspan="4" rowspan="4">
            <?php echo JText::_('LBL_MONTO_LETRAS'); ?> <span><?php echo $number2word->toCurrency('$'.number_format($this->odv->getTotalAmount(), 2)); ?></span>
        </td>
        <td class="span2">
            <?php echo JText::_('LBL_SUBTOTAL'); ?>
        </td>
        <td><div class="text-right">
                $<?php echo number_format($this->odv->subTotalAmount,2); ?>
            </div></td>
    </tr>
    <tr>
        <td class="span2">
            <?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_IVA'); ?>
        </td>
        <td><div class="text-right">
                $<?php echo number_format($this->odv->iva, 2); ?>
            </div></td>
    </tr>
    <tr>
        <td class="span2">
            <?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_IEPS'); ?>
        </td>
        <td><div class="text-right">
                $<?php echo number_format($this->odv->ieps, 2); ?>
            </div></td>
    </tr>
    <tr>
        <td class="span2">
            <?php echo JText::_('LBL_TOTAL'); ?>
        </td>
        <td><div class="text-right">
                $<?php echo number_format($this->odv->getTotalAmount(), 2); ?>
            </div></td>
    </tr>
    </tbody>
</table>
<table class="table" id="printFooter">
    <tr>
        <td>
            <?php echo JText::_('LBL_DATOS_DEPOSITO'); ?>
        </td>
    </tr>
    <tr>
        <td>
            <?php echo JText::_('LBL_AUTORIZO_ODV'); ?>
        </td>
    </tr>
    <tr>
        <td style="text-align: center;">
            <p class="text-capitalize"><?php echo JText::_('LBL_INTEGRADORA'); ?></p>
            <p><?php echo JText::_('LBL_INTEGRADORA_DIRECCION'); ?></p>
        </td>
    </tr>
</table>