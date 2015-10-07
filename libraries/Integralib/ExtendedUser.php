<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 02-Sep-15
 * Time: 11:04 AM
 */

namespace Integralib;


use JUser;

class ExtendedUser
{
    protected $user;

    /**
     * ExtendedUser constructor.
     *
     * @param null $id
     */
    public function __construct($id)
    {
        $this->user = \JFactory::getUser($id);
        $this->extendUser($this->user);
    }

    /**
     * @param JUser $instance
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

    /**
     * @param $instance
     *
     * @return mixed
     */
    public function getIntegradosCurrUser($instance)
    {
        $db    = \JFactory::getDbo();
        $query = $db->getQuery(true)
                          ->select($db->quoteName('integradoId'))
                          ->from($db->quoteName('#__integrado_users'))
                          ->where($db->quoteName('user_id') . '=' . $db->quote($instance->id));
        $result = $db->setQuery($query)->loadObjectList();


        jimport('integradora.integrado');

        $usuario = new Integrado;
        $usuario->intergrado->ids = $result;

        return $result;
    }

    /**
     * @param $instance
     *
     * @return mixed
     */
    private function getSecurityQuestions( $instance ) {
        return IntFactory::getsUserSecurity($instance);
    }

    /**
     * @return JUser
     */
    public function getUser()
    {
        return $this->user;
    }

}