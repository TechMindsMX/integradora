<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  User.joomla
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Integralib\IntFactory;

defined('_JEXEC') or die;

/**
 * Joomla User plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  User.joomla
 * @since       1.5
 */
class PlgUserIntegrado extends JPlugin
{
	/**
	 * Application object
	 *
	 * @var    JApplicationCms
	 * @since  3.2
	 */
	protected $app;

	/**
	 * Database object
	 *
	 * @var    JDatabaseDriver
	 * @since  3.2
	 */
	protected $db;

	protected $autoloadLanguage = true;

	function getIntegradosCurrUser($instance)
	{
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('integradoId'))
			->from($this->db->quoteName('#__integrado_users'))
			->where($this->db->quoteName('user_id') . '=' . $this->db->quote($instance->id));
		$result = $this->db->setQuery($query)->loadObjectList();
		
		
		jimport('integradora.integrado');
		
		$usuario = new Integrado;
		$usuario->intergrado->ids = $result;
		
		return $result;
	}

	private function getSecurityQuestions( $instance ) {
		return IntFactory::getsUserSecurity($instance);
	}

	/**
	 * @param $instance
	 */
	public function onUserAfterLogin( ) {

		$user = JFactory::getUser();
        $this->extendUser($user);
		$redirectUrl = 'index.php';

        if ($this->app->getName() == 'site') {
            $changeUrl     ='index.php?option=com_integrado&view=integrado&layout=change&Itemid=207';
            $changeFullUrl = JUri::base().$changeUrl;
            $count = count($user->integrados);

            switch ( true ) {
				case ( $count === 0):
                    $this->app->enqueueMessage(JText::sprintf('NO_INTEGRADO', $changeFullUrl), 'warning');
                    break;
                case ( $count === 1):
                    try {
                        Integrado::setIntegradoInSession(new IntegradoSimple($user->integrados[0]->integradoId));
                    } catch (Exception $e) {
                        $this->app->enqueueMessage(JText::sprintf('NO_INTEGRADO', $changeFullUrl), 'warning');
                    }
                    break;
                case ( $count > 1):
                    $redirectUrl = $changeUrl;
                    break;
            }

            if ( count( $user->security ) == 0 ) {
                $this->app->enqueueMessage(JText::_( 'NO_SECURITY_QUESTIONS' ), 'warning' );
                $redirectUrl = 'index.php?option=com_usersinteg&view=usersinteg&layout=questions';
            }
		}

		$this->app->redirect(JRoute::_($redirectUrl));
	}

	public function onUserAfterLogout()
	{
		JFactory::getApplication()->redirect('index.php');
	}

    /**
     * @param $instance
     */
    public function extendUser(JUser $instance)
    {
        // extiende los datos de usuario con los de integrados
        $integrados = $this->getIntegradosCurrUser($instance);
        $instance->set('integrados', $integrados);

        // extiende los datos de usuario con las presuntas de seguridad
        $securityQuestions = $this->getSecurityQuestions($instance);
        $instance->set('security', $securityQuestions);

    }

}
