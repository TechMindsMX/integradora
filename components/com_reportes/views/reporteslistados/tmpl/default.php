<?php
defined('_JEXEC') or die('Restricted access');
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 05-May-15
 * Time: 1:19 PM
 */

$baseUrl = 'index.php?option=com_reportes&view=reporteslistados&layout=';
$buttonClasses = 'btn btn-primary btn-large span8';
?>
<div class="">
	<h2><?php echo JText::_('LBL_REPORTES'); ?></h2>
</div>
<div class="form-actions">
	<div class="row row-fluid">
		<div class="span4">
			<a class="<?php echo $buttonClasses; ?>" href="<?php echo $baseUrl, 'balance'; ?>">
				<i class="icon-file"></i>
				<br/><br/>
				<p><?php echo JText::_('LBL_BALANCE'); ?></p>
			</a>
		</div>
		<div class="span4">
			<a class="<?php echo $buttonClasses; ?>" href="<?php echo $baseUrl, 'flujo'; ?>">
				<i class="icon-file"></i>
				<br/><br/>
				<p><?php echo JText::_('LBL_ESTADO_FLUJO'); ?></p>
			</a>
		</div>
		<div class="span4">
			<a class="<?php echo $buttonClasses; ?>" href="<?php echo $baseUrl, 'resultados'; ?>">
				<i class="icon-file"></i>
				<br/><br/>
				<p><?php echo JText::_('LBL_ESTADORESULTS'); ?></p>
			</a>
		</div>
	</div>
</div>
<div class="row-fluid">
	<div class="alert alert-info">
		<p><?php echo JText::_('LBL_REPORTES_MSG'); ?></p>
	</div>
</div>
