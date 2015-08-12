<?php
defined('_JEXEC') or die('Restricted Access');

?>

<div class="">
	<?php echo JText::_('LBL_SECURITY_QUESTIONS_INSTRUCTIONS'); ?>
</div>

<div>
	<form class="form" method="post" enctype="application/x-www-form-urlencoded" action="index.php?option=com_usersinteg&task=savequestions">

		<?php

		for ($count = 1; $count < 6; $count++) {
			?>
			<div class="conttol-group">
				<label for="question<?php echo $count; ?>"><?php echo JText::_('LBL_PREGUNTA_'.$count); ?></label>
				<div>
					<select id="question<?php echo $count; ?>" name="question<?php echo $count; ?>" class="form-control span6">
						<?php
						foreach ( $this->getAllQuestions() as $id => $question ) {
							echo "<option value=\"$id\">$question->question</option>";
						}
						?>

					</select>
				</div>
				<label for="answer_<?php echo $count; ?>"><?php echo JText::_('LBL_ANSWER'); ?></label>
				<input type="text" class="form-control span6" name="answer_<?php echo $count; ?>" id="answer_<?php echo $count; ?>" />
			</div>
			<hr />
		<?php
		}

		echo JHtml::_( 'form.token' );

		?>
		<div class="form-control">
			<input class="btn btn-primary" type="submit" value="<?php echo JText::_('LBL_ENVIAR'); ?>" />
			<a class="btn btn-danger" href="index.php?option=com_user"><?php echo JText::_('LBL_CANCELAR'); ?></a>
			<a class="btn" href="index.php?option=com_content&view=article&id=8&Itemid=101"><?php echo JText::_('LBL_LATER'); ?></a>
		</div>
	</form>
</div>
