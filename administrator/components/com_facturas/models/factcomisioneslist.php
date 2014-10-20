<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.modellist');
jimport('integradora.integrado');
jimport('integradora.validator');
jimport('integradora.gettimone');

/**
 * Methods supporting a list of Facturas records.
 */
class FacturasModelFactcomisioneslist extends JModelList {

    public function __construct($config = array()) {

        parent::__construct($config);
    }

    public function getUserIntegrado(){
        $db     =JFactory::getDbo();
        $query  =$db->getQuery(true);
        $query
            ->select('intuser.integrado_id, user.id,user.name' )
            ->from('#__integrado_users as intuser')
            ->join('INNER', '#__users as user on  intuser.user_id = user.id')
            ->where('intuser.integrado_principal'.' <> 0 ');
        $db->setQuery($query);
        $result=$db->loadAssocList();
        return $result;
    }

    public function getFacturas(){
        $data = getFromTimOne::getFactComisiones();

        $usuarios = $this->getUserIntegrado();

        foreach($data as $value){
            foreach($usuarios as $usuario){
                if($value->receptor == $usuario['integrado_id']){
                    $value->userName = $usuario['name'];
                }
            }
        }


        return $data;
    }

    public  function getComision(){
        $data = getFromTimOne::getComisiones();
        return $data;
    }
}
