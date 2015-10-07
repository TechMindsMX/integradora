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

    /**
     * @param $error
     * @param $servicio
     * @param $traceId
     */
    public function notificationErrors($error, $servicio, $traceId = null){
        $mailer = JFactory :: getMailer ();
        $Config = JFactory :: getConfig ();

        $remitente = array (
            $Config['mailfrom'],
            $Config['fromname']);
        $mailer->setSender($remitente);

        $correos = $this->getErrorToEmails($error);

        $mailer->addRecipient( $correos ) ;
        list( $body, $title ) = $this->getErrorEmailBodyAndTitle($error, $servicio, $traceId);
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

    /**
     * @param $error
     *
     * @return array
     */
    public function getErrorToEmails($error)
    {
        if (isset( $error->code )) {
            switch (true) {
                case ( $error->code >= 400 && $error->code < 500 ):
                    $integradora    = new \Integralib\Integrado;
                    $admin          = $integradora->getIntegradoraUserData();
                    $user           = JFactory::getUser();
                    $correos        = array ($admin->email, $user->email);
                    break;
                default:
                    $correos = array (
                        'luis.magana@techminds.com.mx',
                        'joseluis.delacruz@techminds.com.mx',
                        'nestor.aguilar@techminds.com.mx',
                        'ricardo.lyon@techminds.com.mx'
                    );
                    break;
            }
        }

        return $correos;
    }

    /**
     * @param $error
     * @param $servicio
     *
     * @param $traceId
     *
     * @return array
     */
    public function getErrorEmailBodyAndTitle($error, $servicio, $traceId)
    {
        if (isset( $error->code )) {
            switch (true) {
                case ( $error->code > 400 && $error->code < 500 ):
                    $title = JText::_('ERR_TIMONE_ADMIN_EMAIL_TITLE');
                    $body  = JText::sprintf('ERR_TIMONE_ADMiN_EMAIL_BODY', $traceId);
                    break;
                default:
                    $title = JText::_('ERR_TIMONE_DEVELOP_EMAIL_TITLE');
                    $body  = JText::sprintf('ERR_TIMONE_DEVELOP_EMAIL_BODY', @$servicio, @$error->code, $error->message, $traceId);
                    break;
            }

            return array ($body, $title);
        }
    }

}