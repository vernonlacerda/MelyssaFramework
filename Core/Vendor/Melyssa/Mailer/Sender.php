<?php
namespace Melyssa\Mailer;

class Sender extends \PHPMailer
{
    public function __construct($from_mail, $from_name, $reply = null)
    {
        parent::__construct();
        //Usando envio de e-mails via SMTP
        $this->isSMTP();
        //Definindo envio de mensagens em formato HTML
        $this->isHTML(true);
        //Setando cliente de SMTP
        $this->Host = '';
        //Porta
        $this->Port = 000;
        //Habilitando autenticação SMTP
        $this->SMTPAuth = true;
        //Usuário de autenticação do sistema ( criptografado ):
        $this->Username = '';
        //Senha de autenticação do sistema ( criptografado ):
        $this->Password = '';
        if (null !== $reply) {
            // Replyto:
            $this->addReplyTo($reply);
        }
        //Definindo remetente do e-mail:
        $this->setFrom($from_mail, utf8_decode($from_name));
    }

    public function setDestiny($mail, $name)
    {
        $this->addAddress($mail, $name);
    }

    public function makeMessage($assunto, $conteudo)
    {
        $this->Subject = $assunto;
        $this->Body = utf8_decode($conteudo);
        $this->AltBody = strip_tags($conteudo);
    }

    public function sendMessage()
    {
        return $this->send();
    }
}
