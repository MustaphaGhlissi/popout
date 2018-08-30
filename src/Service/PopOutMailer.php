<?php
/**
 * Created by PhpStorm.
 * User: Hp
 * Date: 22/03/2018
 * Time: 14:55
 */

namespace App\Service;


class PopOutMailer
{
    private $mailer;
    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function send($to, $from, $subject, $body)
    {
        $message = new \Swift_Message();
        $message->setSubject($subject)
            ->setBody($body)
            ->setTo($to)
            ->setFrom($from);
        $this->mailer->send($message);
    }
}