<?php
defined ('_JEXEC') or die('Restricted Access');

JHtml::_ ('bootstrap.tooltip');

$items = $this->comision[0];

$accion = 'index.php?option=com_adminintegradora&view=adminintegradora';
?>

	<script type="text/javascript">
		Joomla.submitbutton = function (task) {
			if (task == 'comision.cancel' || document.formvalidator.isValid(document.id('comision-form'))) {
				Joomla.submitform(task, document.getElementById('comision-form'));
			}
		}

		jQuery(document).ready(function () {
			var $type = jQuery('#type');
			$type.change($type, checkType);
			$type.triggerHandler('change');
		});

		function checkType() {
			var $freqTime 	= jQuery('#frequency');
			var $rate		= jQuery('#percentage');

			if (this.value == '1') {
				$freqTime.hide();
				$rate.show('300');
			} else if (this.value == '0') {
				$rate.hide();
				$freqTime.show('300');
			}
		}
	</script>

	<form action="<?php echo $accion; ?>"
		  method="post" name="adminForm" id="comision-form" class="form-validate">

		<div class="form-horizontal">
			<div class="control-group">
				<label class="control-label" for="description">
					<?php echo JText::_ ('COM_ADMININTEGRADORA_COMISIONES_DESCRIPTION'); ?>
				</label>
				<input type="text" name="description" id="description" value="<?php echo @$items->description; ?>"
					   class="input-xxlarge input-large-text" required="" aria-required="true"
					   aria-invalid="true">
			</div>
		</div>

		<div class="control-group span6">
			<div class="control-group">
				<label class="control-label" for="type">
					<?php echo JText::_ ('COM_ADMININTEGRADORA_COMISIONES_TYPE'); ?>
				</label>
				<select id="type" name="type">
					<?php foreach ($this->cats->types as $key => $value):
						$selected = ($items->type == $key) ? 'selected' : '';
						?>
						<option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
					<?php endforeach; ?>
				</select>
			<label class="control-label" for="frequencyTimes">
				<?php echo JText::_ ('COM_ADMININTEGRADORA_COMISIONES_FREQUECY_TIME'); ?>
			</label>
			<select id="frequencyTimes" name="type">
				<?php foreach ($this->cats->frequencyTimes as $key => $value):
					$selected = ($items->frequencyTime == $value) ? 'selected' : '';
					?>
					<option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
				<?php endforeach; ?>
			</select>
		</div>

			<div class="control-group" id="frequency">
				<label class="control-label" for="monto">
					<?php echo JText::_ ('COM_ADMININTEGRADORA_COMISIONES_MONTO'); ?>
				</label>
				<input type="text" name="monto" id="monto" value="<?php echo @$items->amount; ?>"
					   class="" required="" aria-required="true"
					   aria-invalid="true">
			</div>
			<div class="control-group" id="percentage">
				<label class="control-label" for="rate">
					<?php echo JText::_ ('COM_ADMININTEGRADORA_COMISIONES_RATE'); ?>
				</label>
				<input type="text" name="rate" id="rate" value="<?php echo @$items->rate; ?>"
					   class="" required="required">
			</div>
		</div>

		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="boxchecked" value="0"/>
		<?php echo JHtml::_ ('form.token'); ?>
	</form>

<?php
