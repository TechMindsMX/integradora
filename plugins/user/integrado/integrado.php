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

    /**
     * Conditional redirects
     */
    public function onUserAfterLogin( ) {

        $user = IntFactory::getExtendedUser();
        $redirectUrl = 'index.php';

        if ($this->app->getName() == 'site') // Check if login occurs in site front
        {
            $redirectUrl     ='index.php';

            $count = count($user->integrados);
            switch ( true ) {
                case ( $count === 0):
                    $this->app->enqueueMessage(JText::sprintf('NO_INTEGRADO', JUri::base().$redirectUrl), 'warning');
                    $redirectUrl     ='index.php?option=com_integrado&view=solicitud&Itemid=207';
                    break;
                case ( $count === 1):
                    try {
                        Integrado::setIntegradoInSession(new IntegradoSimple($user->integrados[0]->integradoId));
                    } catch (Exception $e) {
                        $this->app->enqueueMessage(JText::sprintf('NO_INTEGRADO', JUri::base().$redirectUrl), 'warning');
                    }
                    break;
                case ( $count > 1):
                    $redirectUrl = 'index.php?option=com_integrado&view=integrado&layout=change&Itemid=207';
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

}
