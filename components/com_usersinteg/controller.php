<?php
defined('_JEXEC') or die('Restricted Access');


class UsersIntegController extends JControllerLegacy {

	function __construct() {

		parent::__construct();
	}

	public function validate() {
		$app = JFactory::getApplication();

		$fields = array(
			'answer_1'  => 'STRING',
			'answer_2'  => 'STRING',
			'answer_3'  => 'STRING'
		);
		$fieldsquestions = array(
			'q1'        => 'INT',
			'q2'        => 'INT',
			'q3'        => 'INT'
		);

		$model = $this->getModel();

		$post = $app->input->getArray($fields);
		$postQuestions = $app->input->getArray($fieldsquestions);

		$diccionario = array (
			'answer_1'  => array ( 'alphaNum'   => true,    'required' => true, 'minlenght' => 5),
			'answer_2'  => array ( 'alphaNum'   => true,    'required' => true, 'minlenght' => 5),
			'answer_3'  => array ( 'alphaNum'   => true,    'required' => true, 'minlenght' => 5),
			'q1'        => array ( 'number'     => true,    'required' => true, 'min' => 1,     'max' => $this->countQuestionsDb() ),
			'q2'        => array ( 'number'     => true,    'required' => true, 'min' => 1,     'max' => $this->countQuestionsDb() ),
			'q3'        => array ( 'number'     => true,    'required' => true, 'min' => 1,     'max' => $this->countQuestionsDb() )
		);


		try {
			$model->validateTypeLength( $diccionario, array_merge($post, $postQuestions) );

			$model->checkAnswers( $post, $postQuestions );

			$token = JSession::getFormToken() .'=1';

		} catch (Exception $e) {
			$app->enqueueMessage($e->getMessage(), 'error');
			$app->redirect( 'index.php?option=com_usersinteg' );
		}

		// Go to password remind screen
		$app->redirect('index.php?option=com_users&view=reset&'. $token );
	}

	public function savequestions() {
		$app = JFactory::getApplication();

		$fields = array(
			'answer_1'  => 'STRING',
			'answer_2'  => 'STRING',
			'answer_3'  => 'STRING',
			'answer_4'  => 'STRING',
			'answer_5'  => 'STRING'
		);
		$fieldsquestions = array(
			'question1'        => 'INT',
			'question2'        => 'INT',
			'question3'        => 'INT',
			'question4'        => 'INT',
			'question5'        => 'INT'
		);

		$model = $this->getModel();

		$answers = $app->input->getArray($fields);
		$questions = $app->input->getArray($fieldsquestions);

		$db = JFactory::getDbo();

		$diccionario = array (
			'answer_1'  => array ( 'alphaNum'   => true,    'required' => true, 'minlenght' => 5),
			'answer_2'  => array ( 'alphaNum'   => true,    'required' => true, 'minlenght' => 5),
			'answer_3'  => array ( 'alphaNum'   => true,    'required' => true, 'minlenght' => 5),
			'answer_4'  => array ( 'alphaNum'   => true,    'required' => true, 'minlenght' => 5),
			'answer_5'  => array ( 'alphaNum'   => true,    'required' => true, 'minlenght' => 5)
		);

		try {
			$model->validateTypeLength( $diccionario, array_merge($fields, $answers) );

			$model->saveUserQuestionsAndAnswers($questions, $answers, $db);

		} catch (Exception $e) {

			$db->transactionRollback();
			$app->enqueueMessage($e->getMessage(), 'error');
			$app->redirect('index.php?option=com_usersinteg&layout=questions');

		}

		$app->enqueueMessage('LBL_SAVE_SUCCESSFUL');
		$app->redirect('index.php?option=com_content&view=article&id=8&Itemid=101');

	}

}