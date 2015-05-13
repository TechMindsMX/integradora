<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html.bootstrap');
jimport('integradora.numberToWord');

$report = $this->report;
$integ = $this->integrado;

?>
<div class="hidden-print form-group">
	<?php echo $this->printBtn; ?>
</div>

<div class="clearfix" id="logo">
	<div class="span6"><img width="200" src="<?php echo JUri::base().'images/logo_iecce.png'; ?>" /></div>
</div>
<div class="">
	<div class="header">
		<div class="span6">
			<h3>
				<?php echo JText::_('INTEGRADORA_NAME'); ?>
			</h3>
			<p>
				<?php echo JText::_('INTEGRADORA_ADDRESS'); ?>
			</p>
			<p>
				<?php echo JText::_('INTEGRADORA RFC'); ?>
			</p>
		</div>

		<div class="span6">
			<h3>
				<?php echo $integ->getDisplayName(); ?>
			</h3>
			<p>
				<?php echo $integ->getAddressFormatted(); ?>
			</p>
			<p>
				<?php echo $integ->getIntegradoRfc(); ?>
			</p>
		</div>
	</div>
</div>

<br class="row-separator">

<h1 class="t-center"><?php echo JText::_('LBL_BALANCE'); ?></h1>

<div id="report resumen content">
	<div class="span6">
		<h3><?php echo JText::_('LBL_PERIOD'); ?></h3>
		<div class="row-fluid">
			<div class="span6"><?php echo JText::_('LBL_FROM_DATE'); ?></div>
			<div class="span6"><?php echo $report->getFechaInicio(); ?></div>
		</div>
		<div class="row-fluid">
			<div class="span6"><?php echo JText::_('LBL_TO_DATE'); ?></div>
			<div class="span6"><?php echo $report->getFechaFin(); ?></div>
		</div>
	</div>
	<div class="span6">
		<h3><?php echo JText::_('LBL_RESUNE_OPERATIONS'); ?></h3>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span6"><?php echo JText::_('LBL_ACTIVOS'); ?></div>
					<div class="span6 num"><?php echo number_format($report->getActivos()->total,2) ;?></div>
				</div>
				<div class="row-fluid">
					<div class="span6"><?php echo JText::_(''); ?></div>
					<div class="span6"><?php echo '' ?></div>
				</div>
				<div class="row-fluid">
					<div class="span6"><?php echo JText::_('LBL_TOTAL'); ?></div>
					<div class="span6 num"><?php echo number_format($report->getActivos()->total,2) ;?></div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span6"><?php echo JText::_('LBL_PASIVOS'); ?></div>
					<div class="span6 num"><?php echo number_format($report->getPasivos()->total,2) ;?></div>
				</div>
				<div class="row-fluid">
					<div class="span6"><?php echo JText::_('LBL_CAPITAL'); ?></div>
					<div class="span6 num"><?php echo number_format($report->capital->total,2) ;?></div>
				</div>
				<div class="row-fluid">
					<div class="span6"><?php echo JText::_('LBL_TOTAL'); ?></div>
					<div class="span6 num"><?php echo number_format($report->getPasivos()->total + $report->capital->total,2) ;?></div>
				</div>
			</div>
		</div>
	</div>
</div>

<h3 class="t-center"><?php echo JText::_('LBL_DETAIL_OPERATIONS'); ?></h3>
<div class="clearfix" id="report detalle content">
	<div class="span6" id="col-izquierda">
		<h2 class=""><?php echo JText::_('LBL_ACTIVOS'); ?></h2>
		<div class="row-fluid">
			<div class="span6 "><?php echo JText::_('LBL_BANCOS_TOTAL'); ?></div>
			<div class="span6 num"><?php echo number_format($report->getActivos()->banco,2); ?></div>
		</div>
		<div class="row-fluid">
			<div class="span6 "><?php echo JText::_('LBL_CXC'); ?></div>
			<div class="span6 num"><?php echo number_format($report->getActivos()->netoSaldoVentas,2); ?></div>
		</div>
		<div class="row-fluid">
			<div class="span6 "><?php echo JText::_('LBL_IVA_CXC'); ?></div>
			<div class="span6 num"><?php echo number_format($report->getActivos()->ivaCompras,2); ?></div>
		</div>
		<div class="row-fluid">
			<div class="span6 "><?php echo JText::_('LBL_TOTAL'); ?></div>
			<div class="span6 num"><?php echo number_format($report->getActivos()->total,2); ?></div>
		</div>
	</div>
	<div class="span6" id="col-derecha">
		<h2 class=""><?php echo JText::_('LBL_PASIVOS'); ?></h2>
		<div class="row-fluid">
			<div class="span6 "><?php echo JText::_('LBL_CXP'); ?></div>
			<div class="span6 num"><?php echo number_format($report->getPasivos()->cuentasPorPagar,2); ?></div>
		</div>
		<div class="row-fluid">
			<div class="span6 "><?php echo JText::_('LBL_IVA_CXP'); ?></div>
			<div class="span6 num"><?php echo number_format($report->getPasivos()->ivaEnVentas,2); ?></div>
		</div>
		<div class="row-fluid">
			<div class="span6 "><?php echo JText::_('LBL_TOTAL'); ?></div>
			<div class="span6 num"><?php echo number_format($report->getPasivos()->total,2); ?></div>
		</div>

		<br class="row-separator clearfix">

		<div class="row-fluid">
			<div class="span6 "><?php echo JText::_('LBL_CAPITAL_ANTERIOR'); ?></div>
			<div class="span6 num"><?php echo number_format($report->getPasivos()->ejecicioAnterior,2); ?></div>
		</div>
		<div class="row-fluid">
			<div class="span6 "><?php echo JText::_('LBL_CAPITAL'); ?></div>
			<div class="span6 num"><?php echo number_format($report->getPasivos()->resultado,2); ?></div>
		</div>

		<br class="row-separator clearfix">

		<div class="row-fluid">
			<div class="span6 "><?php echo JText::_('LBL_DEPOSITOS_ANTERIOR'); ?></div>
			<div class="span6 num"><?php echo number_format($report->depositos->ejecicioAnterior,2); ?></div>
		</div>
		<div class="row-fluid">
			<div class="span6 "><?php echo JText::_('LBL_RETIROS_ANTERIOR'); ?></div>
			<div class="span6 num"><?php echo number_format($report->retiros->ejecicioAnterior,2); ?></div>
		</div>
		<div class="row-fluid">
			<div class="span6 "><?php echo JText::_('LBL_DEPOSITOS'); ?></div>
			<div class="span6 num"><?php echo number_format($report->getPasivos()->depositos,2); ?></div>
		</div>
		<div class="row-fluid">
			<div class="span6 "><?php echo JText::_('LBL_RETIROS'); ?></div>
			<div class="span6 num"><?php echo number_format($report->getPasivos()->retiros,2); ?></div>
		</div>

		<br class="row-separator clearfix">

		<div class="row-fluid">
			<div class="span6 "><?php echo JText::_('LBL_TOTAL'); ?></div>
			<div class="span6 num"><?php echo number_format($report->capital->total,2); ?></div>
		</div>
	</div>
</div>
<br class="row-separator clearfix" />
<div id="footer">
	<div class="container text-center">
		<p class="text-capitalize"><?php echo JText::_('LBL_INTEGRADORA'); ?></p>
		<p><?php echo JText::_('LBL_INTEGRADORA_DIRECCION'); ?></p>
	</div>
</div>

