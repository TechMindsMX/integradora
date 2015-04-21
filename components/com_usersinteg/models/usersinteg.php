<?php
use Integralib\IntFactory;

defined('_JEXEC') or die('Restricted Access');

class UsersintegModelUsersinteg extends JModelLegacy {

	protected $user;
	private $salt;

	function __construct() {
		$this->user = JFactory::getUser();
		$this->salt = 'u-d-6wBa6/E?wTwqmm$}K_EQC0Dh,|y&W*+Gzx?4HV?_XaP>;q%nthuN}d+sZs54';

		parent::__construct();
	}

	public function getRandomQuestionsFromUserQuestions() {
		$allUserQuestions = $this->getAllUserQuestions();

		foreach ( array_rand($allUserQuestions, 3) as $qId ) {
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
	public function validateTypeLength( $post ) {
		$validator = IntFactory::getValidator();

		$diccionario = array (
			'answer_1'  => array ( 'alphaNum'   => true,    'required' => true, 'minlenght' => 5),
			'answer_2'  => array ( 'alphaNum'   => true,    'required' => true, 'minlenght' => 5),
			'answer_3'  => array ( 'alphaNum'   => true,    'required' => true, 'minlenght' => 5),
			'q1'        => array ( 'number'     => true,    'required' => true, 'min' => 1,     'max' => $this->countQuestionsDb() ),
			'q2'        => array ( 'number'     => true,    'required' => true, 'min' => 1,     'max' => $this->countQuestionsDb() ),
			'q3'        => array ( 'number'     => true,    'required' => true, 'min' => 1,     'max' => $this->countQuestionsDb() )
		);

		$validator->procesamiento( $post, $diccionario );

		if ( ! $validator->allPassed() ) {
			throw new Exception( JText::_( 'ERR_CODE_403' ), 403 );
		}
	}

	public function checkAnswers( $post, $postQuestions ) {
		$savedAnswers = $this->getUserSavedAnswers();

		$answers = array_combine($post, $postQuestions);

		foreach ( $answers as $questionId => $answer ) {
			if ($savedAnswers[$questionId] != $answer) {
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
		$userId = JFactory::getUser()->id;

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('question_id')))
			->from( $db->quoteName('#__users_security_questions') )
			->where( $db->quoteName('user_id') .' = ' . $db->quote($userId) );
		$db->setQuery($query);

		$userQuetionIds = $db->loadAssocList();

		return $userQuetionIds;
	}

	private function countQuestionsDb() {
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

}