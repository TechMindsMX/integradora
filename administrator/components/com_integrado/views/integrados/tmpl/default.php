<?php
defined('_JEXEC') or die('Restricted Access');

JHtml::_('behavior.tooltip');
?>

<script language="javascript" type="text/javascript">
	function tableOrdering(order, dir, task) {
		var form = document.adminForm;

		form.filter_order.value = order;
		form.filter_order_Dir.value = dir;
		document.adminForm.submit(task);
	}

    function cargabotones() {
        jQuery('#toolbar').html('<div id="toolbar-edit" class="btn-wrapper">'+
        ' <button class="btn btn-small" id="validacion">'+
        '  <span class="icon-edit"></span>'+
        '  Validación de Integrado'+
        ' </button>'+
        '</div>'+
        '<div id="toolbar-edit" class="btn-wrapper">'+
        ' <button class="btn btn-small" id="parametrizacion">'+
        '  <span class="icon-edit"></span>'+
        '  Parametrización'+
        ' </button>'+
        '</div>');
    }

    function envio() {
        var boton = jQuery(this);
        var form = jQuery('#adminForm');
        var integradoId = checkRadioButon();
        var url = 'index.php?option=com_integrado';

        if(boton.prop('id') == 'validacion') {
                url = 'index.php?option=com_integrado&view=integrado&layout=edit&integrado_id=' + integradoId;
        }else{
                url = 'index.php?option=com_integrado&view=integradoparams&layout=edit&id=' + integradoId;
        }

        if(integradoId != ''){
            form.prop('action', url);
            form.submit();
        }
    }

    function checkRadioButon(){
        var retorno = '';

        var radios = jQuery('#adminForm').find('input:radio');
        jQuery.each(radios,function(k,v){
            var radio = jQuery(v);
            if (radio.prop('checked')) {
                retorno = radio.val();
            }
        });

        if(retorno == ''){
            alert('Debe Seleccionar un integrado');
        }

        return retorno;
    }

    jQuery(document).ready(function() {
        cargabotones();
        jQuery('#validacion').on('click',envio);
        jQuery('#parametrizacion').on('click',envio);

		jQuery("input:checkbox").click(function(){
	        var group = "input:checkbox[name='"+jQuery(this).prop("name")+"']";
	        jQuery(group).not(this).prop("checked",false);
    	});
	});
</script>


<form action="<?php echo JRoute::_('index.php?option=com_integrado'); ?>" method="post" name="adminForm" id="adminForm">

<table class="adminlist table">
 	<thead>
		<tr>
			<th width="20">
			</th>
			<th>
				<?php echo JHTML::_('grid.sort', JText::_('COM_INTEGRADO_INTEGRADO_HEADING_ID'), 'a.integradoId', $this -> sortDirection, $this -> sortColumn); ?>
			</th>
			<th>
			<?php echo JText::_('COM_INTEGRADO_INTEGRADO_HEADING_R_SOCIAL'); ?>
			</th>
			<th>
			<?php echo JText::_('COM_INTEGRADO_INTEGRADO_HEADING_NOM_COMERCIAL'); ?>
			</th>
			<th>
			<?php echo JText::_('COM_INTEGRADO_INTEGRADO_HEADING_CONTACTO'); ?>
			</th>
			<th>
				<?php echo JHTML::_('grid.sort', JText::_('COM_INTEGRADO_INTEGRADO_HEADING_CREATED'), 'a.createdDate', $this -> sortDirection, $this -> sortColumn); ?>
			</th>
			<th>
				<?php echo JText::_('COM_INTEGRADO_INTEGRADO_HEADING_STATUS'); ?>
			</th>
			<th>
			<?php echo JText::_('COM_INTEGRADO_INTEGRADO_HEADING_PERS_JURIDICA'); ?>
			</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($this->items as $i => $item): ?>
		<tr class="row<?php echo $i % 2; ?>">
			<td>
			   <input type="radio" name="integradoId" value="<?php echo $item -> integradoId; ?>">
			</td>
			<td>
			    <?php echo $item -> integradoId; ?>
			</td>
			<td>
                <?php
                    $nombre = ($item -> razon_social) ? $item -> razon_social : $item -> nombre_representante;
                    echo $nombre;
                ?>
			</td>
			<td>
			    <?php echo $item -> nom_comercial; ?>
			</td>
			<td>
			    <?php echo $item -> nombre_representante; ?>
			</td>
			<td>
				<?php echo is_null($item->createdDate)?'':date('d-m-Y',$item->createdDate); ?>
			</td>
			<td>
                <?php
                foreach ( $this->catalogos->statusSolicitud as $value ) {
                    if ( $item->status == $value->status ) {
                        echo $value -> status_name;
                    }
                }
                ?>
			</td>
			<td>
			    <?php echo $this->catalogos->pers_juridica[$item -> pers_juridica]; ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>

	<tfoot>
		<tr>
			<td colspan="8"><?php echo $this -> pagination -> getListFooter(); ?></td>
		</tr>
	</tfoot>
</table>

        <input type="hidden" name="filter_order" value="<?php echo $this -> sortColumn; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $this -> sortDirection; ?>" />
       <div>
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="boxchecked" value="0" />
            <?php echo JHtml::_('form.token'); ?>
        </div>

</form>