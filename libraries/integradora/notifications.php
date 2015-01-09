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


    public function notification($data){

        $mailer = JFactory :: getMailer ();
        $Config = JFactory :: getConfig ();

        $remitente = array (
            $Config['mailfrom'],
            $Config['fromname']);

        $mailer->setSender($remitente);
        $user = JFactory::getUser();
        $recipient = $user->email;

        $mailer->addRecipient('aguilar_2001@hotmail.com');

        $body   = $data['body'];
        $mailer->isHTML(true);
        $mailer->Encoding = 'base64';
        $mailer->setBody($body);
        // Optionally add embedded image
        //$mailer->AddEmbeddedImage( JPATH_COMPONENT.'/assets/logo128.jpg', 'logo_id', 'logo.jpg', 'base64', 'image/jpeg' );

        $send = $mailer->Send();
        if ( $send !== true ) {
            return 'Error sending email: ' . $send->__toString();
        } else {
            return 'Mail sent';
        }
    }

}