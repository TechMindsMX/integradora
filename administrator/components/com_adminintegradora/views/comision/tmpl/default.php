<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

JHtml::_( 'bootstrap.tooltip' );

$items = $this->comision;

// triggers recurrent
$triggersRecurrent['fecha'] = array_pop($this->cats->triggers);

// triggers por Tx
$triggersByTx = $this->cats->triggers;

JFactory::getDocument()->addScript( JUri::root() . 'libraries/integradora/js/tim-validation.js' );
JFactory::getDocument()->addScript( JUri::root() . 'libraries/integradora/js/form_helper.js' );

$accion = 'index.php?option=com_adminintegradora';
?>
	<div class="btn-toolbar">
		<button id="toolbar-apply" class="btn btn-small btn-success"><span class="icon-apply icon-white"></span><?php echo JText::_( 'LBL_GUARDAR' ); ?></button>
		<button id="toolbar-clear" class="btn btn-small btn-default"><span class=""></span><?php echo JText::_( 'LBL_LIMPIAR' ); ?></button>
		<button id="toolbar-cancel" class="btn btn-small btn-danger"><span class=""></span><?php echo JText::_( 'LBL_CANCEL' ); ?></button>
	</div>

	<form action="<?php echo $accion; ?>" method="post" name="adminForm" id="comision-form" class="form-validate" autocomplete="off">

		<div class="control-group">
			<div class="control-group">
				<label class="control-label" for="description">
					<?php echo JText::_( 'COM_ADMININTEGRADORA_COMISIONES_DESCRIPTION' ); ?>
				</label>
				<input type="text" name="description" id="description" value="<?php echo @$items->description; ?>"
				       class="input-xxlarge input-large-text" required="" aria-required="true"
				       aria-invalid="true" maxlength="255">
			</div>
		</div>

		<div class="control-group">
			<div class="control-group">
				<label class="control-label" for="type">
					<?php echo JText::_( 'COM_ADMININTEGRADORA_COMISIONES_TYPE' ); ?>
				</label>
				<select id="type" name="type">
					<?php foreach ( $this->cats->types as $key => $value ):
						if ( isset( $items->type ) ) {
							$selected = ( $items->type == $key ) ? 'selected' : '';
						} else {
							$selected = '';
						}
						?>
						<option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
					<?php endforeach; ?>
				</select>
				<label class="control-label frequency" for="frequencyTimes">
					<?php echo JText::_( 'COM_ADMININTEGRADORA_COMISIONES_FREQUECY_TIME' ); ?>
				</label>
				<select class="frequency" id="frequencyTimes" name="frequencyTimes">
					<?php foreach ( $this->cats->frequencyTimes as $key => $value ):
						if ( isset( $items->frequencyTime ) ) {
							$selected = ( $items->frequencyTime == $value ) ? 'selected' : '';
						} else {
							$selected = '';
						}
						?>
						<option value="<?php echo $value; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
					<?php endforeach; ?>
				</select>
			</div>

			<div class="control-group">
				<div class="control-group" id="frequency">
					<label class="control-label" for="monto">
						<?php echo JText::_( 'COM_ADMININTEGRADORA_COMISIONES_MONTO' ); ?>
					</label>
					<input type="text" name="monto" id="monto" value="<?php echo @$items->monto; ?>"
					       class="" required="" aria-required="true"
					       aria-invalid="true" maxlength="10">
				</div>
				<div class="control-group" id="percentage">
					<label class="control-label" for="rate">
						<?php echo JText::_( 'COM_ADMININTEGRADORA_COMISIONES_RATE' ); ?>
					</label>
					<input type="text" name="rate" id="rate" value="<?php echo @$items->rate; ?>"
					       class="" required="required" maxlength="5">
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="trigger">
					<?php echo JText::_( 'COM_ADMININTEGRADORA_COMISIONES_TRIGGERS' ); ?>
				</label>
				<select id="trigger" name="trigger">
					<?php foreach ( $this->cats->triggers as $key => $value ):
						if ( isset( $items->trigger ) ) {
							$selected = ( $items->trigger == $key ) ? 'selected' : '';
						} else {
							$selected = '';
						}
						?>
						<option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
					<?php endforeach; ?>
				</select>
			</div>

			<div class="control-group">
				<label class="control-label" for="statusSelect">
					<?php echo JText::_( 'LBL_BASIC_STATUSES' ); ?>
				</label>
				<select id="statusSelect" name="status">
					<?php foreach ( $this->cats->status as $key => $value ):
						if ( isset( $items ) ) {
							$selected = ( $items->status == $key ) ? 'selected' : '';
						} else {
							$selected = '';
						}
						?>
						<option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
					<?php endforeach; ?>
				</select>
			</div>


			<?php echo JHtml::_( 'form.token' ); ?>
	</form>


	<script type="text/javascript">

		jQuery(document).ready(function () {

			jQuery('#toolbar-cancel').click(function () {
				jQuery('#comision-form').prop('action', 'index.php?option=com_adminintegradora&view=comisions').submit();
			});

			jQuery('#toolbar-clear').click(function () {
				jQuery('#comision-form').clearForm();
			});

			var $type = jQuery('#type');
			$type.change($type, checkType);

			$type.triggerHandler('change');

			jQuery('#toolbar-apply').click(function () {
				var data = jQuery('#comision-form').find('select, input').serialize();

				var parametros = {
					'link': '<?php echo $accion; ?>&task=comision.savecomision&format=raw',
					'datos': data
				};

				var request = jQuery.ajax({
					url: parametros.link,
					data: parametros.datos,
					type: 'post'
				});

				request.done(function (response) {
					if (response.redirect === true) {
						window.location = 'index.php?option=com_adminintegradora&view=comisions';
					} else {
						mensajesValidaciones(response);
					}
				});
			});

		});

		function checkType() {
			var $freqTime = jQuery('#frequency, .frequency');
			var $rate = jQuery('#percentage');

			function optionsTrigger(myObj) {
				var $triggers = jQuery('#trigger');
				$triggers.empty();

				jQuery.each(myObj, function(index, value){
					$triggers.append(jQuery("<option>",{
						value: index,
						text: value
					}));
				});
			}

			if (this.value == '1') { // por Tx
				$freqTime.hide();
				$rate.show('300');

				optionsTrigger(<?php echo json_encode($triggersByTx); ?>);
			} else if (this.value == '0') { // recurrente
				$rate.hide();
				$freqTime.show('300');

				optionsTrigger(<?php echo json_encode($triggersRecurrent); ?>);
			}


		}
	</script>

<?php
