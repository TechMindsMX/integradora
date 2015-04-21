<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 17-Apr-15
 * Time: 3:05 PM
 */

namespace Integralib;


class UserSecurity {

	public function getUserAnswers( \JUser $instance ) {
		$db = \JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select( array( $db->quoteName('question_id'), $db->quoteName('answer') ) )
			->from('#__users_security_questions')
			->where($db->quote('user_id'). ' = '. $instance->id);
		$db->setQuery($query);

		$results = $db->loadObjectList();

		return $results;
	}
}