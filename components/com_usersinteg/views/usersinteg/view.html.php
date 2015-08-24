<?php
defined('_JEXEC') or die('Restricted Access');

class UsersintegViewUsersinteg extends JViewLegacy {

	protected $challengeQuestions = array();
	protected $allQuestions;

	/**
	 * @param null $tpl
	 *
	 * @return mixed|void
	 * @throws Exception
	 */
	public function display($tpl = null) {

		$this->allQuestions = $this->get('Questions');

		try {
			$this->challengeQuestions = $this->get('UserQuestions');
		} catch (\Exception $e) {
            JLog::add($e->getMessage(), JLog::DEBUG);
            $app = JFactory::getApplication();

			if (JFactory::getUser()->guest) {
				$app->redirect(JRoute::_('index.php?option=com_usersinteg&view=error'));
			}
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