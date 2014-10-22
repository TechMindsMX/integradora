<?php
// no direct access
defined( '_JEXEC' ) or die;



?>

<script type="text/javascript">
	jQuery(document).ready(function ($) {
		var odv = <?php echo $this->odv; ?>;
		var odc = <?php echo $this->odc; ?>;
		var odd = <?php echo $this->odd; ?>;
		var odr = <?php echo $this->odr; ?>;

		var $integ = $('#integrado').change(function() {
			filterTable($integ);
		});
	});
</script>

<label for="integrado"><?php echo JText::_('LBL_INTEGRADO');?><input name="integrado" id="integrado" type="text"/></label>
<label for="mandato"><?php echo JText::_('LBL_MANDATO');?><input name="mandato" id="mandato" type="text"/></label>


<?php
$html = null;
foreach ( $this->odv as $odv ) {
	$html = '<div class="span4">'.JText::_($odv->id).'</div>';
	$html = '<div class="span4">'.$odv->id.'</div>';
}

echo $html;
var_dump($this->odv);
