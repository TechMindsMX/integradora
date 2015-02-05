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
    /*
     * Esta funcion espera recibir tres parametros
     * $notificaciones       = el numero de la notificacion que se llamara
     * $data                 = Un arreglo de datos que contiene la informacion a cambiar en el texto
     * $emailUserPrincipal   = Es el correo del usuario principal del integrado
     * Retorna boolean true  = email enviado
     *                 falce = error en envio y su descripcion;
     */
    public function sendNotifications($notification, $data, $emailUserPrincipal) {

        $title  = 'TITLE_'.$notification;
        $text   = 'NOTIFICACIONES_'.$notification;

        $emails = array($emailUserPrincipal, JFactory::getUser()->email, 'aguilar_2001@hotmail.com');
        $emails = array_unique($emails);

        $titulo = JText::_($title);

        $conten = JText::_($text);
        $contenido = vsprintf($conten, $data);

        $dato['titulo']         = $titulo;
        $dato['body']           = $contenido;
        $dato['email']          = $emails;
        $send                   = new Send_email();
        $info = $send->notification($dato);
        return $info;
    }

    public function notification($data){

        $this->data = (object)$data;

        $currentIntegradoId= JFactory::getSession()->get('integradoId', null, 'integrado');

        $int = new IntegradoSimple($currentIntegradoId);

        array_push($int->usuarios, JFactory::getUser(93));

        foreach ($int->usuarios as $key => $val) {
            if(isset($val->permission_level)) {
                if ($val->permission_level >= '3' || $val->id == JFactory::getUser()->id || $val->authorise('core.admin')) {
                    return  $this->envia();
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
        var_dump($send);exit;
        $this->logEvent( $mailer, $send );

        return $send;
    }

    private function logEvent( $info, $dato ) {
        $logdata = $logdata = implode( ', ', array (
            JFactory::getUser()->id,
            JFactory::getSession()->get('integradoId', null, 'integrado'),
            json_encode( array ( $info, $dato  ) )
        ) );
        JLog::add( $logdata, JLog::DEBUG, 'bitacora' );

    }


}