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

    protected $data;
    protected $recipients;

    function __construct( $customEmails = null) {
        if ( isset( $customEmails ) && !is_array($customEmails) ) {
            $this->customEmail = array($customEmails) ;
        }
        elseif ( is_array( $customEmails ) ) {
            $this->customEmail = $customEmails;
        }
        else {
            $this->customEmail = array();
        }
    }
    /**
     * @param $notificationNumber
     * @param $data                 array  Un arreglo de datos indexado que contiene la informacion a sustituir en el contenido
     *
     * @return mixed
     */
    public function sendNotifications($notificationNumber, $data, $editTitle = null) {

        $title  = 'TITULO_'.$notificationNumber;
        $titulo = JText::_($title);

        if(isset($editTitle)){
            $titulo = vsprintf($titulo, $editTitle);
        }

        $text   = 'NOTIFICACIONES_'.$notificationNumber;

        $conten = JText::_($text);
        $contenido = vsprintf($conten, $data);

        $dato['titulo']         = $titulo;
        $dato['body']           = $contenido;
        $this->data = (object)$dato;

        $info = $this->envia();

        return $info;
    }

    public function notificationErrors($error, $servicio){
        $mailer = JFactory :: getMailer ();
        $Config = JFactory :: getConfig ();

        $remitente = array (
            $Config['mailfrom'],
            $Config['fromname']);
        $mailer->setSender($remitente);

        $correos = array('luis.magana@techminds.com.mx', 'joseluis.delacruz@techminds.com.mx');
        if(isset($error->code)){
            switch($error->code){
                case 400:
                    $correos = array('luis.magana@techminds.com.mx');
                    break;
                case 503:
                    $correos = array('luis.magana@techminds.com.mx', 'joseluis.delacruz@techminds.com.mx');
                    break;
                case 0:
                    $correos = array('luis.magana@techminds.com.mx', 'joseluis.delacruz@techminds.com.mx');
                    break;
                default:
                    $correos = array('luis.magana@techminds.com.mx', 'joseluis.delacruz@techminds.com.mx');
                    break;

            }
        }

        $mailer->addRecipient( $correos ) ;
        $body   = 'Se presento el siguiente error en la plataforma TIMONE llamando al servicio: '.@$servicio.'<br /> Código: '.@$error->code.'<br /> Mensaje: '.$error->message;
        $title  = 'Error de comunicacion con servicios TimOne';
        $mailer->isHTML(true);
        $mailer->Encoding = 'base64';
        $mailer->setSubject($title);
        $mailer->setBody($body);
        $send = $mailer->Send();
    }

    private function envia()
    {
        $mailer = JFactory :: getMailer ();
        $Config = JFactory :: getConfig ();

        $remitente = array (
            $Config['mailfrom'],
            $Config['fromname']);
        $mailer->setSender($remitente);

        $this->setFinalRecipients();
        $mailer->addRecipient( $this->recipients ) ;
        $body   = $this->data->body;
        $title  = $this->data->titulo;
        $mailer->isHTML(true);
        $mailer->Encoding = 'base64';
        $mailer->setSubject($title);
        $mailer->setBody($body);
        $send = $mailer->Send();

        $this->logEvent( $mailer, $send );

        return $send;
    }

    private function logEvent( $info, $dato ) {
        $logdata = implode( ' | ', array (
            JFactory::getUser()->id,
            JFactory::getSession()->get('integradoId', null, 'integrado'),
            json_encode( array ( $info, $dato  ) )
        ) );
        JLog::add( $logdata, JLog::DEBUG, 'bitacora' );

    }

    public function setIntegradoEmailsArray(IntegradoSimple $getCurrInteg) {

        $emailsInteg = array();

        foreach ($getCurrInteg->usuarios as $key => $val) {
            if(isset($val->permission_level)) {
                if ($val->permission_level >= '3' || $val->id == JFactory::getUser()->id) {
                    $emailsInteg[] = $val->email;
                }
            }
        }
        array_push($emailsInteg, JFactory::getUser()->email);
        $this->recipients = $emailsInteg;
    }

    public function setAdminEmails() {
        $this->recipients = array(JFactory::getUser(93)->email);
    }

    private function setFinalRecipients() {
        if ( isset($this->customEmail) ) {
            $this->recipients = array_unique( array_merge($this->recipients, $this->customEmail) );
        }
    }

}