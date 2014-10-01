<?php
defined ('_JEXEC') or die('Restricted Access');

JHtml::_ ('bootstrap.tooltip');

$items = $this->comision[0];

$accion = 'index.php?option=com_adminintegradora&view=adminintegradora';
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'comision.cancel' || document.formvalidator.isValid(document.id('comision-form')))
		{
			Joomla.submitform(task, document.getElementById('comision-form'));
		}
	}

	jQuery(document).ready(function(){
		jQuery('#type').checkType();
	});
	(function($){
		//Attach this new method to jQuery
		$.fn.extend({

			//This is where you write your plugin's name
			checkType: function() {
				//Iterate over the current set of matched elements
				return this.each(function() {

					var $freqTime = $('#frequency');

					if($type.val() == 1) {
						$freqTime.hide();
					} else {
						$freqTime.show('300');
					}

				});
			}
		});

//pass jQuery to the function,
//So that we will able to use any valid Javascript variable name
//to replace "$" SIGN. But, we'll stick to $ (I like dollar sign: ) )
	})(jQuery);
</script>

<form action="<?php echo $accion; ?>"
	  method="post" name="adminForm" id="comision-form" class="form-validate">

	<div class="form-horizontal">
		<div class="control-group">
			<label class="control-label" for="description">
				<?php echo JText::_ ('COM_ADMININTEGRADORA_COMISIONES_DESCRIPTION'); ?>
			</label>
			<input type="text" name="description" id="description" value="<?php echo @$items->description; ?>" class="input-xxlarge input-large-text invalid" required="" aria-required="true" aria-invalid="true">
		</div>
	</div>

	<div class="control-group span6">
		<div class="span6 control-group">
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
		</div>
		<label class="control-label" for="frequency">
			<?php echo JText::_ ('COM_ADMININTEGRADORA_COMISIONES_FREQUECY_TIME'); ?>
		</label>
		<select id="type" name="type">
			<?php foreach ($this->cats->frequencyTimes as $key => $value):
				$selected = ($items->frequencyTime == $value) ? 'selected' : '';
				?>
				<option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
			<?php endforeach; ?>
		</select>
	</div>

	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<?php echo JHtml::_ ('form.token'); ?>
</form>

<?php
