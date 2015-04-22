<?php
use Integralib\IntFactory;

defined('_JEXEC') or die('Restricted Access');

class UsersintegModelUsersinteg extends JModelLegacy {

	protected $user;
	private $salt;

	function __construct() {
		$this->user = JFactory::getUser($this->getUserFromEmail());
		$this->salt = 'u-d-6wBa6/E?wTwqmm$}K_EQC0Dh,|y&W*+Gzx?4HV?_XaP>;q%nthuN}d+sZs54';

		parent::__construct();
	}

	public function getRandomQuestionsFromUserQuestions() {
		$allUserQuestions = $this->getAllUserQuestions();

		foreach ( array_rand($allUserQuestions, 2) as $qId ) {
			$questions[$qId] = $allUserQuestions[$qId];
		}

		return $questions;
	}

	public function getAllUserQuestions() {
		$userQuestions = array();

		$userQuetionIds = $this->getUserQuestionsIds();

		$questions = $this->getQuestions();

		foreach ( $userQuetionIds as $id ) {
			$userQuestions[$id['question_id']] = $questions[$id['question_id']];
		}

		return $userQuestions;
	}

	public function getQuestions() {
		$db = JFactory::getDbo();
		$query  = $db->getQuery(true);

		$query->select('*')
			->from($db->quoteName('#__security_questions'));
		$db->setQuery($query);

		$questions = $db->loadObjectList('id');

		return $questions;
	}

	/**
	 * @param $post
	 *
	 * @throws Exception
	 */
	public function validateTypeLength( $diccionario, $post ) {
		$validator = IntFactory::getValidator();

		$validator->procesamiento( $post, $diccionario );

		if ( ! $validator->allPassed() ) {
			throw new Exception( JText::_( 'ERR_CODE_403' ), 403 );
		}
	}

	public function checkAnswers( $post, $postQuestions ) {
		$savedAnswers = $this->getUserSavedAnswers();

		$answers = array_combine($postQuestions, $post);

		foreach ( $answers as $questionId => $answer ) {
			if ($savedAnswers[$questionId]->answer != $answer) {
				throw new Exception( JText::_('ERR_FAILED_SECURITY_QUESTIONS') );
			}
		}
	}

	public function getUserSavedAnswers() {
		$db = JFactory::getDbo();

		$query = $db->getQuery( true );
		$query->select( array($db->quoteName( 'answer' ), $db->quoteName('question_id') ) )
		      ->from( $db->quoteName( '#__users_security_questions' ) )
		      ->where( 'user_id = '. $this->user->id);
		$db->setQuery($query);

		$encrypted =  $db->loadObjectList('question_id');

		foreach ( $encrypted as $ans ) {
			$ans->answer = $this->decryptAnswer($ans->answer);
		}

		return $encrypted;
	}

	/**
	 * @return array
	 */
	private function getUserQuestionsIds() {
		$userId = $this->getUserFromEmail();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('question_id')))
			->from( $db->quoteName('#__users_security_questions') )
			->where( $db->quoteName('user_id') .' = ' . $db->quote($userId) );
		$db->setQuery($query);

		$userQuetionIds = $db->loadAssocList();

		return $userQuetionIds;
	}

	public function countQuestionsDb() {
		return count($this->getQuestions());
	}

	public function saveUserQuestionsAndAnswers( $questions, $answers,JDatabaseDriver $db ) {
		$tmp = array_combine($questions, $answers);
		$userId = JFactory::getUser()->id;

		foreach ( $tmp as $key => $val ) {
			$record = new stdClass();
			$record->user_id = $userId;
			$record->question_id = $key;
			$record->answer = $this->encryptAnswer($val);
			$db->insertObject('#__users_security_questions', $record);
		}

	}

	public function encryptAnswer( $string ) {
		$key = JFactory::getConfig()->get('secret');

		$salted = $this->salt . $string;

		$encrypted = openssl_encrypt($salted, 'AES256', $key);

		return $encrypted;
	}

	public function decryptAnswer( $string ) {
		$key = JFactory::getConfig()->get('secret');

		$decrypted = openssl_decrypt($string, 'AES256', $key);

		$decrypted = str_replace( $this->salt, '', $decrypted);

		return $decrypted;
	}

	private function getUserFromEmail() {
		$sesion = JFactory::getSession();
		$email = $sesion->get('resetPassEmail');

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
		            ->select('id')
		            ->from($db->quoteName('#__users'))
		            ->where($db->quoteName('email') . ' = ' . $db->quote( $email ));
		$db->setQuery($query);

		return $db->loadResult();
	}

}