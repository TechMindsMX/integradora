<?php
defined('_JEXEC') or die('Restricted Access');

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
$integrados = $this->integrados;
?>

<form action="index.php?option=com_adminintegradora&view=txsform&layout=confirm" method="post">
    <input type="hidden" value="<?php echo $this->input['idtx']; ?>" name="idtx">
    <div class="form-control">
        <label for="integradoId">Integrados</label>
        <select id="integradoId" name="integradoId">
            <option value="">Selecione su opci&oacute;n</option>
            <?php foreach ($integrados as $key => $integrados) { ?>
                <option value="<?php echo $integrados->integradoId; ?>"><?php echo $integrados->displayName; ?></option>
            <?php } ?>
        </select>
    </div>
    <input type="submit" class="btn btn-primary" value="Identificar">
</form>
