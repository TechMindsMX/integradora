<?php
defined ('_JEXEC') or die('Restricted Access');

JHtml::_ ('bootstrap.tooltip');

$items = $this->items;

$accion =  JRoute::_ ('index.php?option=com_adminintegradora&view=adminintegradora');
?>
<form action="<?php echo $accion; ?>" method="post"
	  name="adminForm" id="adminForm">

	<table class="table table-striped" id="articleList">
		<thead>
		<tr>
			<th width="1%" style="min-width:55px" class="nowrap center">
				<a href="#" onclick="return false;" class="hasTooltip" data-order="a.state"
				   data-direction="ASC" data-name="ID" title=""
				   data-original-title="<strong>ID</strong><br /><?php echo JText::_ (
					   'COM_ADMININTEGRADORA_ID_TOOLTIP'
				   ); ?>">
					<?php echo JText::_ ('COM_ADMININTEGRADORA_COMISIONES_LIST_ID'); ?> </a>
			</th>
			<th>
				<a href="#" onclick="return false;" class="js-stools-column-order hasTooltip" data-order="a.title"
				   data-direction="ASC" data-name="Descripción" title=""
				   data-original-title="<strong>Descripción</strong><br /><?php echo JText::_ (
					   'COM_ADMININTEGRADORA_COMISIONES_LIST_DESCRIPCION_TOOLTIP'
				   ); ?>">
					<?php echo JText::_ ('COM_ADMININTEGRADORA_COMISIONES_LIST_DESCRIPCION'); ?> </a>
			</th>
			<th width="10%" class="nowrap hidden-phone">
				<a href="#" onclick="return false;" class="js-stools-column-order hasTooltip" data-order="a.access"
				   data-direction="ASC" data-name="Tipo" title=""
				   data-original-title="<strong>Tipo</strong><br /><?php echo JText::_ (
					   'COM_ADMININTEGRADORA_COMISIONES_LIST_TIPO_TOOLTIP'
				   ); ?>">
					<?php echo JText::_ ('COM_ADMININTEGRADORA_COMISIONES_LIST_TIPO'); ?> </a>
			</th>
			<th width="10%" class="nowrap hidden-phone">
				<a href="#" onclick="return false;" class="js-stools-column-order hasTooltip" data-order="a.created_by"
				   data-direction="ASC" data-name="Frecuencia" title=""
				   data-original-title="<strong>Frecuencia</strong><br /><?php echo JText::_ (
					   'COM_ADMININTEGRADORA_COMISIONES_LIST_FRECUENCIA_TOOLTIP'
				   ); ?>">
					<?php echo JText::_ ('COM_ADMININTEGRADORA_COMISIONES_LIST_FRECUENCIA'); ?> </a>
			</th>
			<th width="5%" class="nowrap hidden-phone">
				<a href="#" onclick="return false;" class="js-stools-column-order hasTooltip" data-order="language"
				   data-direction="ASC" data-name="Estado" title=""
				   data-original-title="<strong>Estado</strong><br /><?php echo JText::_ (
					   'COM_ADMININTEGRADORA_COMISIONES_LIST_ESTADO_TOOLTIP'
				   ); ?>">
					<?php echo JText::_ ('COM_ADMININTEGRADORA_COMISIONES_LIST_ESTADO'); ?> </a>
			</th>
		</tr>
		</thead>
		<tbody>

		<?php
		foreach ($items as $key => $value) :
			?>

			<tr class="row0">
				<td class="has-context">
					<div class="pull-left">
						<?php echo $value->id; ?>
					</div>
				</td>
				<td class="small hidden-phone">
					<a href="index.php?option=com_adminintegradora&task=comisions.editar&id=<?php echo $value->id; ?>"
					   title="Descripcion">
						<?php echo $value->description; ?></a>
				</td>
				<td class="small hidden-phone">
					<?php echo $value->typeName; ?>
				</td>
				<td class="small hidden-phone">
					<?php echo $value->frequencyTypeName; ?>
				</td>
				<td class="nowrap small hidden-phone">
					<?php echo $value->statusName; ?>
				</td>
			</tr>

		<?php
		endforeach;
		?>

		</tbody>
	</table>

	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<?php echo JHtml::_ ('form.token'); ?>
</form>