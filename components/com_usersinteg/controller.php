<?php
defined('_JEXEC') or die('Restricted Access');


class UsersIntegController extends JControllerLegacy {

	function __construct() {

		parent::__construct();
	}

	public function request() {
		// Check the request token.
		JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));

		$app   = JFactory::getApplication();
		$data  = $this->input->post->get('jform', array(), 'array');


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

	public function sendReset() {
		$vars[JSession::getFormToken()] = '=1';
		$vars['email'] = 'remy.ochoa@trama.mx';

		// Submit the password reset request.
		$return	= $this->processResetRequest($vars);

	}

	private function processResetRequest($data)
	{
		$config = JFactory::getConfig();

		$data['email'] = JStringPunycode::emailToPunycode($data['email']);

		// Find the user id for the given email address.
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
		            ->select('id')
		            ->from($db->quoteName('#__users'))
		            ->where($db->quoteName('email') . ' = ' . $db->quote($data['email']));

		// Get the user object.
		$db->setQuery($query);

		try
		{
			$userId = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			$this->setError(JText::sprintf('COM_USERS_DATABASE_ERROR', $e->getMessage()), 500);

			return false;
		}

		// Check for a user.
		if (empty($userId))
		{
			$this->setError(JText::_('COM_USERS_INVALID_EMAIL'));

			return false;
		}

		// Get the user object.
		$user = JUser::getInstance($userId);

		// Make sure the user isn't blocked.
		if ($user->block)
		{
			$this->setError(JText::_('COM_USERS_USER_BLOCKED'));

			return false;
		}

		// Make sure the user isn't a Super Admin.
		if ($user->authorise('core.admin'))
		{
			$this->setError(JText::_('COM_USERS_REMIND_SUPERADMIN_ERROR'));

			return false;
		}

		jimport('joomla.application.component.model');
		JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_users/models');
		$usersModel = JModelLegacy::getInstance( 'Reset', 'UsersModel' );
		// Make sure the user has not exceeded the reset limit
		if (!$usersModel->checkResetLimit($user))
		{
			$resetLimit = (int) JFactory::getApplication()->getParams()->get('reset_time');
			$this->setError(JText::plural('COM_USERS_REMIND_LIMIT_ERROR_N_HOURS', $resetLimit));

			return false;
		}

		// Set the confirmation token.
		$token = JApplicationHelper::getHash(JUserHelper::genRandomPassword());
		$salt = JUserHelper::getSalt('crypt-md5');
		$hashedToken = md5($token . $salt) . ':' . $salt;
		$user->activation = $hashedToken;

		// Save the user to the database.
		if (!$user->save(true))
		{
			return new JException(JText::sprintf('COM_USERS_USER_SAVE_FAILED', $user->getError()), 500);
		}

		// Assemble the password reset confirmation link.
		$mode = $config->get('force_ssl', 0) == 2 ? 1 : (-1);

		require_once( JPATH_SITE.'/components/com_users/helpers/route.php');
		$itemid = UsersHelperRoute::getLoginRoute();
		$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
		$link = 'index.php?option=com_users&view=reset&layout=confirm&token=' . $token . $itemid;

		echo $link;
		// Put together the email template data.
		$data = $user->getProperties();
		$data['fromname'] = $config->get('fromname');
		$data['mailfrom'] = $config->get('mailfrom');
		$data['sitename'] = $config->get('sitename');
		$data['link_text'] = JRoute::_($link, false, $mode);
		$data['link_html'] = JRoute::_($link, true, $mode);
		$data['token'] = $token;

		$subject = JText::sprintf(
			'COM_USERS_EMAIL_PASSWORD_RESET_SUBJECT',
			$data['sitename']
		);

		$body = JText::sprintf(
			'COM_USERS_EMAIL_PASSWORD_RESET_BODY',
			$data['sitename'],
			$data['token'],
			$data['link_text']
		);

		// Send the password reset request email.
		$return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $user->email, $subject, $body);

		// Check for an error.
		if ($return !== true)
		{
			return new JException(JText::_('COM_USERS_MAIL_FAILED'), 500);
		}

		return true;
	}

}