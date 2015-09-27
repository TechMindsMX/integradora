<?php
defined('_JEXEC') or die('Restricted Access');

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
$integrados = $this->integrados;

var_dump($integrados);
?>

<form>
    <div class="form-control">
        <label for="integradoId">Integrados</label>
        <select id="integradoId" name="integradoId">
            <option value="">Selecione su opción</option>
            <?php foreach ($integrados as $key => $integrados) {
                $integrado = new IntegradoSimple($integrados->integradoId);
                ?>
                <option value="<?php echo $integrados->getId(); ?>"><?php echo $integrados->getDisplayName(); ?></option>
            <?php } ?>
        </select>
    </div>
    <input type="submit" value="Identificar">
</form>
