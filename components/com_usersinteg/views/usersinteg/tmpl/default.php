<?php
defined('_JEXEC') or die('Restricted Access');
?>

<div class="">
	<?php echo JText::_('LBL_SECURITY_QUESTIONS_ANSWERS_INSTRUCTIONS'); ?>
</div>

<form class="form" method="post" enctype="application/x-www-form-urlencoded" action="index.php?option=com_usersinteg&task=validate" autocomplete="off">

<?php
$count = 0;
foreach ( $this->getChallengeQuestions() as $id => $question ) {
	$count++;
?>

	<div class="conttol-group">
		<label for="question<?php echo $count; ?>"><?php echo $question->question; ?></label>
		<input type="text" class="form-control" name="answer_<?php echo $count; ?>" />
		<input type="hidden" name="q<?php echo $count; ?>" value="<?php echo $question->id; ?>" />
	</div>

<?php
}

echo JHtml::_( 'form.token' );

?>
	<div class="form-control">
		<input class="btn btn-primary" type="submit" value="<?php echo JText::_('LBL_ENVIAR'); ?>" />
		<a class="btn btn-danger" href="index.php?option=com_user"><?php echo JText::_('LBL_CANCELAR'); ?></a>
	</div>
</form>
