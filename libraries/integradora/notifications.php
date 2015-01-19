<?php
/**
 * Created by PhpStorm.
 * User: Nestor
 * Date: 08/01/2015
 * Time: 10:51 AM
 */
jimport('integradora.integrado');
jimport('joomla.factory');

class Send_email{

    public $responses;
    protected $recipient;

    public function notification($data){

        $this->data = (object)$data;

        $currentIntegradoId= JFactory::getSession()->get('integradoId', null, 'integrado');


        $int = new IntegradoSimple($currentIntegradoId);

        array_push($int->usuarios, JFactory::getUser(93));

        foreach ($int->usuarios as $key => $val) {
            if(isset($val->permission_level)) {
                if ($val->permission_level >= '3' || $val->id == JFactory::getUser()->id || $val->authorise('core.admin')) {
                    $this->envia();
                }
            }
        }
    }

    private function envia()
    {

        $mailer = JFactory :: getMailer ();
        $Config = JFactory :: getConfig ();

        $remitente = array (
            $Config['mailfrom'],
            $Config['fromname']);
        $mailer->setSender($remitente);

        $mailer->addRecipient($this->data->email);
        $body   = $this->data->body;
        $title  = $this->data->titulo;
        $mailer->isHTML(true);
        $mailer->Encoding = 'base64';
        $mailer->setSubject($title);
        $mailer->setBody($body);
        $send = $mailer->Send();
        return $send;

    }


}