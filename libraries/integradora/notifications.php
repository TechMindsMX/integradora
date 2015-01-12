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

        foreach ($int->usuarios as $key => $val) {
            if ($val->permission_level>=3 || $val->id==JFactory::getUser()->id){
                $this->recipient = $val->email;

                $this->responses[$val->id.'-'.$key]=$this->envia();

            }
        }
        return $this;
    }

    private function envia()
    {

        $mailer = JFactory :: getMailer ();
        $Config = JFactory :: getConfig ();

        $remitente = array (
            $Config['mailfrom'],
            $Config['fromname']);

        $mailer->setSender($remitente);

        $mailer->addRecipient($this->recipient);

        $body   = $this->data->body;
        $mailer->isHTML(true);
        $mailer->Encoding = 'base64';
        $mailer->setBody($body);
        // Optionally add embedded image
        //$mailer->AddEmbeddedImage( JPATH_COMPONENT.'/assets/logo128.jpg', 'logo_id', 'logo.jpg', 'base64', 'image/jpeg' );

        $send = $mailer->Send();

    }


}