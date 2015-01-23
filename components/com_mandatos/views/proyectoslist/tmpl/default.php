<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

function proyectos ($parent, $proys){
    echo "<ul class='listaproyectos'>";
    foreach ($proys as $key => $value) {

        if($value->parentId == $parent){

            if($value->status == 1){
                $checked = 'checked';
                $disabled = 'disabled="disabled"';
            }else{
                $checked = '';
                $disabled = '';
            }

            echo '<li class="proyectoslist">'.
                '<div class="filas status'.$value->status.'">'.
                '<div class="columnas"><span>'.$value->name.'</span></div>'.
                '<div class="columnas"><input type="button" '.$disabled.' class="btn btn-primary editar" id="'.$value->id_proyecto.'" value="'.JText::_('COM_MANDATOS_PROYECTOS_LISTADO_EDITAR_PROYECTO').'" /></div>'.
                '<div class="columnas"><input type="checkbox" class="deshabilitar" data-id="'.$value->id_proyecto.'" id="'.$value->id_proyecto.'" '.$checked.' /></div>'.
                '</div>';

            proyectos($value->id_proyecto, $proys);
        }else{
            echo '</li>';
        }
    }
    echo "</ul>";
}

$proyectos = $this->data;

if( !is_null($proyectos) ){
    $agregarSubproyecto = '<a class="btn btn-primary span3" href="'.JRoute::_('index.php?option=com_mandatos&view=subproyectosform').'">'.JText::_('COM_MANDATOS_PROYECTOS_LISTADO_AGREGAR_SUBPROYECTO').'</a>';
}else{
    JFactory::getApplication()->enqueueMessage(JText::_('MSG_NO_PROJECTS'), 'Message');
    $agregarSubproyecto = '';
}
?>
<script>
    jQuery(document).ready(function($){
//        jQuery('.deshabilitar').on('click', deshabilita);
        jQuery('.deshabilitar').on('click', changeStatus);
        jQuery('.editar').on('click', editarProy);


        function changeStatus() {
            var $this = $(this);
            var itemId = $this.data('id');
            var valor = $this.is(':checked') ? 1 : 0;

            var request = $.ajax({
                url: 'index.php?option=com_mandatos&task=projectlist.changeStatus&format=raw',
                data: {id_proyecto: itemId,
                    status: valor},
                type: 'POST',
                async: false
            });

            request.done(function (result) {
                var $row = $this.parentsUntil('li.proyectoslist', 'div.filas');
                $row.removeClass('status0');
                if(typeof result.success.status != 'undefined'){
                    if(result.success.status == 0) {
                        $row.addClass('status0')
                    }
                }

                $('#system-message-container').remove();

                var $html = '<div id="system-message-container"><div id="system-message"><div class="alert alert-message"><a data-dismiss="alert" class="close">×</a><h4 class="alert-heading">Mensaje</h4><div>';
                $html += '<p>' + result.msg + '</p></div></div></div></div>';

                $('header').prepend($html);
            });
        }

    });


    function deshabilita(){
        var boton = jQuery(this);

        if(confirm('<?php echo JText::_('LAB_WARNING_DISABLED'); ?>')){
            if(!boton.prop('checked')){
                deshabilitaAjax(this,'enabled');
            }else{
                deshabilitaAjax(this, 'disabled');
            }
        }
    }

    function editarProy(){
        var id = jQuery(this).prop('id');

        window.location = 'index.php?option=com_mandatos&task=editarproyecto&id_proyecto='+id;
    }

    function deshabilitaAjax(campo, accion){
        var checkbox = jQuery(campo);
        var id = checkbox.prop('id');
        var parents = checkbox.parent().parent();
        var request = jQuery.ajax({
            url:'index.php?option=com_mandatos&task=disable&format=raw',
            data: {
                'type': 'proyecto',
                'id': id,
                'accion': accion
            },
            type: 'post',
            async: false
        });

        request.done(function(result){
            if(result.success){
                if(result.accion == 'enabled'){
                    parents.find('.editar').prop('disabled', false);
                    parents.find('span[class*="status"]').prop('class', 'status0');
                }else if(result.accion == 'disabled'){
                    checkbox.parent().parent().find('.editar').prop('disabled', true);
                    parents.find('span[class*="status"]').prop('class', 'status1');
                }
            }
        });
    }
</script>
<h1 style="margin-bottom: 40px;"><?php echo JText::_('COM_MANDATOS_PROYECTOS_LISTADO_TITULO'); ?></h1>

<a class="btn btn-primary span3" href="<?php echo JRoute::_('index.php?option=com_mandatos&view=proyectosform'); ?>">
    <?php echo JText::_('COM_MANDATOS_PROYECTOS_LISTADO_AGREGAR_PROYECTO'); ?>
</a>
<?php echo $agregarSubproyecto; ?>

<div class="proyectos">
    <div class="filas" style="margin-bottom: 30px;">
        <div class="columnas"><?php echo JText::_('COM_MANDATOS_PROYECTOS_LISTADO_TH_NAME_PROYECTO'); ?></div>
        <div class="columnas">&nbsp;</div>
        <div class="columnas"><?php echo JText::_('LBL_PUBLISHED'); ?></div>
    </div>
    <?php !is_null($proyectos)?proyectos(0, $proyectos):''; ?>
</div>

<div style="margin-top: 20px;">
    <a class="btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_mandatos'); ?>" >
    <?php echo JText::_('COM_MANDATOS_TITULO'); ?>
    </a>
</div>