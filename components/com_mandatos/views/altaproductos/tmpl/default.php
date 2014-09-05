<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

JFactory::getDocument()->addStyleSheet('templates/meet_gavern/css/bootstrap.css');
JFactory::getDocument()->addStyleSheet('templates/meet_gavern/css/bootstrap.min.css');

$producto 	= isset($this->producto)?$this->producto:null;

?>
<script>
	jQuery(document).ready(function(){
		jQuery('#cancel').on('click', cancelfunction);
		
		var data = <?php echo json_encode($producto); ?>;
		if(!(data == null)){
			jQuery.each(data, function(key, value){
				jQuery('#'+key).val(value);
			});
		}
	});
	
	function cancelfunction(){
		window.history.back();
	}
</script>

<h1><?php echo ucwords(JText::_($this->titulo)); ?></h1>

<form class="form-inline" role="form" method="post" action="index.php/component/mandatos/?task=simulaenvio">
	<div class="row">
		<div class="col-md-6">
	    <label for="productName"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_NAME'); ?></label>
	    <input type="text" class="alto form-control" id="productName" name="productName" placeholder="<?php echo JText::_('COM_MANDATOS_PRODUCTOS_INPUT_NAME').JText::_('COM_MANDATOS_PRODUCTOS_LBL_NAME') ?>">
	  </div>
	  <div class="col-md-6">
	  	<label for="currency"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_MONEDA'); ?></label>
	  	<select name="currency" id="currency">
	  		<option><?php echo JText::_('COM_MANDATOS_PRODUCTOS_INPUT_MEDIDAS'); ?></option><option value="MXN">MXN</option>
	  	</select>
	  </div>
	</div>
	
	<div class="clearfix">&nbsp;</div>
	
	<div class="row">
		<div class="col-md-6">
	    	<label for="price"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_PRECIO'); ?>: </label>
	    	<input type="text" class="alto form-control" id="price" name="price" placeholder="<?php echo JText::_('COM_MANDATOS_PRODUCTOS_INPUT_NAME').JText::_('COM_MANDATOS_PRODUCTOS_LBL_PRECIO') ?>" />
	 	</div>
		<div class="col-md-6">
	  		<label for="iva"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_IVA'); ?>: </label>
	  		<input type="text" class="alto form-control" id="iva" name="iva" placeholder="<?php echo JText::_('COM_MANDATOS_PRODUCTOS_INPUT_NAME').JText::_('COM_MANDATOS_PRODUCTOS_LBL_IVA'); ?>" />
	  	</div>
	</div>
	
	<div class="clearfix">&nbsp;</div>
	
	<div class="row">
		<div class="col-md-6">
	  	<label for="measure"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_MEDIDAS'); ?>: </label>
	  	<select name="measure" id="measure">
	  		<option><?php echo JText::_('COM_MANDATOS_PRODUCTOS_INPUT_MEDIDAS'); ?></option>
	  		<option value="Metros" >Metros</option>
	  		<option value="Metros Cúbicos" >Metros Cúbicos</option>
	  		<option value="Litros" >Litros</option>
	  	</select>
	  </div>
		<div class="col-md-6">
	    <label for="ieps"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_IEPS'); ?>: </label>
	    <input type="text" class="alto form-control" id="ieps" name="ieps"  placeholder="<?php echo JText::_('COM_MANDATOS_PRODUCTOS_INPUT_NAME').JText::_('COM_MANDATOS_PRODUCTOS_LBL_IEPS') ?>"/>
	  </div>
	</div>
	
	<div class="clearfix">&nbsp;</div>
	
	<div class="row">
		<div class="col-md-6">
	    	<label for="description"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_DESCRIPTION'); ?>: </label>
	    	<textarea name="description" id="description" rows="7" style="width: 304px;" placeholder="<?php echo JText::_('COM_MANDATOS_PRODUCTOS_INPUT_NAME').JText::_('COM_MANDATOS_PRODUCTOS_LBL_DESCRIPTION'); ?>"></textarea>
	  	</div>
	</div>
	
	<div class="clearfix">&nbsp;</div>
	
	<button type="button" class="btn btn-primary" id="cancel"><?php echo JText::_('LBL_CANCELAR'); ?></button>
	
	<button type="submit" class="btn btn-primary"><?php echo JText::_('LBL_ENVIAR'); ?></button>
</form>