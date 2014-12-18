<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html.bootstrap');
jimport('integradora.numberToWord');

$report = $this->report;
$integ = $this->integrado;

?>

<div class="">
	<div class="header">
		<div class="span6">
			<h2>
				<?php echo JText::_('INTEGRADORA_NAME'); ?>
			</h2>
			<p>
				<?php echo JText::_('INTEGRADORA_ADDRESS'); ?>
			</p>
			<p>
				<?php echo JText::_('INTEGRADORA RFC'); ?>
			</p>
		</div>

		<div class="span6">
			<h2>
				<?php echo $integ->displayName; ?>
			</h2>
			<p>
				<?php echo $integ->address; ?>
			</p>
			<p>
				<?php echo $integ->datos_empresa->rfc; ?>
			</p>
		</div>
	</div>
</div>

<div class="">
	<h1 class="">
		<?php echo JText::_('LBL_BALANCE'); ?>
	</h1>
</div>

<div id="report resumen content">
	<div class="span6">
		<div class=""><?php echo JText::_('LBL_PERIOD'); ?></div>
		<div class="row-fluid">
			<div class="span6"><?php echo JText::_('LBL_FROM_DATE'); ?></div>
			<div class="span6"><?php echo $report->period->startDate; ?></div>
		</div>
		<div class="row-fluid">
			<div class="span6"><?php echo JText::_('LBL_TO_DATE'); ?></div>
			<div class="span6"><?php echo $report->period->endDate; ?></div>
		</div>
	</div>
	<div class="span6">
		<h3><?php echo JText::_('LBL_RESUNE_OPERATIONS'); ?></h3>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span6"><?php echo JText::_('LBL_ACTIVOS'); ?></div>
					<div class="span6 num"><?php echo number_format($report->activo->total,2) ;?></div>
				</div>
				<div class="row-fluid">
					<div class="span6"><?php echo JText::_(''); ?></div>
					<div class="span6"><?php echo '' ?></div>
				</div>
				<div class="row-fluid">
					<div class="span6"><?php echo JText::_('LBL_TOTAL'); ?></div>
					<div class="span6 num"><?php echo number_format($report->activo->total,2) ;?></div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span6"><?php echo JText::_('LBL_PASIVOS'); ?></div>
					<div class="span6 num"><?php echo number_format($report->pasivo->total,2) ;?></div>
				</div>
				<div class="row-fluid">
					<div class="span6"><?php echo JText::_('LBL_CAPITAL'); ?></div>
					<div class="span6 num"><?php echo number_format($report->capital->total,2) ;?></div>
				</div>
				<div class="row-fluid">
					<div class="span6"><?php echo JText::_('LBL_TOTAL'); ?></div>
					<div class="span6 num"><?php echo number_format($report->pasivo->total + $report->capital->total,2) ;?></div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="clearfix"></div>
<div class="" id="reporrt detalle content">
	<div class="span6">
		<h2 class=""><?php echo JText::_('LBL_ACTIVOS'); ?></h2>
		<div class="span6 num"></div>
		<div class="span6 num"><?php echo number_format($report->activo->total,2); ?></div>
	</div>
	<div class="span6">
		<h2 class=""><?php echo JText::_('LBL_PASIVOS'); ?></h2>
		<div class="span6 num"></div>
		<div class="span6 num"></div>
	</div>
</div>

<?php var_dump($this->report); ?>
