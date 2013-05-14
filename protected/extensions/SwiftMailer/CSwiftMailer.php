<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bolu
 * Date: 13-5-14
 * Time: AM11:06
 * To change this template use File | Settings | File Templates.
 */
include Yii::getPathOfAlias('ext.SwiftMailer.lib') . DIRECTORY_SEPARATOR . 'swift_required.php';
class CSwiftMailer
{
    public $fromName;
    public $email;
    public $password;
    public $smtpServer;
    public $smtpPort;

    private $transport;
    private $mailer;

    public function init()
    {
        if (empty($this->smtpServer) || empty($this->smtpPort)) {
            throw new exception("You should set smtpServer and smptPort for SwiftMailer in main.php");
        }
        if (empty($this->fromName) || empty($this->email) || empty($this->password)) {
            throw new exception("fromName, email and passowrd should be set for SwiftMailer in main.php");
        }
        $this->transport = Swift_SmtpTransport::newInstance($this->smtpServer, $this->smtpPort)->setUsername($this->email)->setPassword($this->password);
        $this->mailer = Swift_Mailer::newInstance($this->transport);
    }

    public function send($to, $subject, $body, $fromName = null)
    {
        if (empty($fromName)) {
            $fromName = $this->fromName;
        }
        $failures = null;
        $message = Swift_Message::newInstance($subject)
            ->setFrom(array($this->email => $fromName))
            ->setTo(array($to))
            ->setBody($body,'text/html');
        $this->mailer->send($message, $failures);
        return $failures;
    }

    public function sendBatch($message_array)
    {
        $this->mailer->registerPlugin(new Swift_Plugins_AntiFloodPlugin(100));
        $failure_messages = array();
        foreach ($message_array as $msg) {
            $failures = null;
            $message = Swift_Message::newInstance($msg['subject'])
                ->setFrom($msg['from'])
                ->setTo($msg['to'])
                ->setBody($msg['body'],'text/html');
            $this->mailer->send($message, $failures);
            $failure_messages[] = array('to' => $failures, 'from' => $msg['from'], 'body' => $msg['body'], 'subject' => $msg['subject']);
        }
        return $failure_messages;
    }
}
