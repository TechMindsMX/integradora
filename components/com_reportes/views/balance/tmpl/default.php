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



<?php
var_dump($this->integrado);
?>

