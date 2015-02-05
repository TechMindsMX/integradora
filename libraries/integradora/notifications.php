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
    protected $data;

    function __construct( $customEmails = null) {
        if ( isset( $customEmails ) && !is_array($customEmails) ) {
            $this->data->customEmail = array($customEmails) ;
        }
        elseif ( is_array( $customEmails ) ) {
            $this->data->customEmail = $customEmails;
        }
        else {
            $this->data->customEmail = array();
        }
    }
    /*
     * Esta funcion espera recibir tres parametros
     * $notificaciones       = el numero de la notificacion que se llamara
     * $data                 = Un arreglo de datos que contiene la informacion a cambiar en el texto
     * $emailUserPrincipal   = Es el correo del usuario principal del integrado
     * Retorna boolean true  = email enviado
     *                 falce = error en envio y su descripcion;
     */
	/**
     * @param $notificationNumber
     * @param $data                 array  Un arreglo de datos indexado que contiene la informacion a sustituir en el contenido
     *
     * @return mixed
     */
    public function sendNotifications($notificationNumber, $data) {

        $title  = 'TITLE_'.$notificationNumber;
        $text   = 'NOTIFICACIONES_'.$notificationNumber;

        $titulo = JText::_($title);

        $conten = JText::_($text);
        $contenido = vsprintf($conten, $data);

        $dato['titulo']         = $titulo;
        $dato['body']           = $contenido;
        $this->data = (object)$dato;

        $info = $this->envia();

        return $info;
    }

    private function envia()
    {

        $mailer = JFactory :: getMailer ();
        $Config = JFactory :: getConfig ();

        $remitente = array (
            $Config['mailfrom'],
            $Config['fromname']);
        $mailer->setSender($remitente);

        $mailer->addRecipient( array_unique(array_merge($this->data->email, $this->data->customEmail)) ) ;
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
        $logdata = $logdata = implode( ', ', array (
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

        $this->data->email = array_push($emailsInteg, JFactory::getUser()->email);
    }

    public function setAdminEmails() {
        $this->data->email = JFactory::getUser(93)->email;
    }


}