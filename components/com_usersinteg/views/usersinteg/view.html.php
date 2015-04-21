<?php
defined('_JEXEC') or die('Restricted Access');

class UsersintegViewUsersinteg extends JViewLegacy {

	protected $challengeQuestions;
	protected $allQuestions;

	public function display($tpl = null) {

		$this->allQuestions = $this->get('Questions');

		if ( count($this->get('AllUserQuestions')) > 0 ) {
			$this->challengeQuestions = $this->get('RandomQuestionsFromUserQuestions');
		}

		parent::display($tpl);
	}

	/**
	 * @return mixed
	 */
	public function getChallengeQuestions() {
		return $this->challengeQuestions;
	}

	/**
	 * @return mixed
	 */
	public function getAllQuestions() {
		return $this->allQuestions;
	}
}