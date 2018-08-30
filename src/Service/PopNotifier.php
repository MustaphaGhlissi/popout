<?php
/**
 * Created by PhpStorm.
 * User: Hp
 * Date: 22/03/2018
 * Time: 17:07
 */

namespace App\Service;
use App\Entity\Booking;
use App\Utils\Util;

class PopNotifier
{
    private $popMailer;
    private $popEmail;
    private $popUsername;

    public function __construct(PopOutMailer $popOutMailer, string $popEmail, string $popUsername)
    {
        $this->popMailer = $popOutMailer;
        $this->popEmail = $popEmail;
        $this->popUsername = $popUsername;
    }

    public function notify(Booking $booking)
    {
        $event = $booking->getEvent();

        /*
        ******************************************** Send email notification to classic user ******************************************
        */
        $classicUser = $booking->getClassicUser();
        $cFirstName = ucfirst($classicUser->getFirstName());
        $cLastName = strtoupper($classicUser->getLastName());
        $to = [$classicUser->getEmail() => $cFirstName.' '.$cLastName];
        $from = [$this->popEmail => $this->popUsername];
        $subject = "Frais de participation à l'évènement: ".$event->getName();
        $body = "Bonjour $cFirstName $cLastName,".Util::newLines(2);
        $body .= "Votre paiement de frais de participation à l'évènement: ".$event->getName()." a été effectué avec succès.".Util::newLines(3);
        $body .= "------------------------------------------------------------------------".Util::newLines(1);
        $body .= "----------------------------- Récapitulatif ----------------------------".Util::newLines(1);
        $body .= "------------------------------------------------------------------------".Util::newLines(2);
        $body .= "Nom de l'évènement: ".$event->getName().Util::newLines(1);
        $body .= "Organisé le: ".$event->getStartDate()->format('Y-m-d')." à ".$event->getStartTime()->format('H:i').Util::newLines(1);
        $body .= "Lieu: ".$event->getLocation().Util::newLines(1);
        $body .= "------------------------------------------------------------------------".Util::newLines(5);
        $body .= "Merci,".Util::newLines(1);
        $body .= "Equipe PopOut.";
        $this->popMailer->send($to, $from, $subject, $body);

        /*
        ******************************************** Send email notification to popout ***********************************************
        */
        $to = [$this->popEmail => $this->popUsername];
        $from = [$this->popEmail => $this->popUsername];
        $subject = "Frais de participation à l'évènement: ".$event->getName();
        $body = "Bonjour $this->popUsername,".Util::newLines(2);
        $body .= "Vous avez reçu le frais de participation de $cFirstName $cLastName à l'évènement: ".$event->getName().Util::newLines(3);
        $body .= "------------------------------------------------------------------------".Util::newLines(1);
        $body .= "----------------------------- Récapitulatif ----------------------------".Util::newLines(1);
        $body .= "------------------------------------------------------------------------".Util::newLines(2);
        $body .= "Nom & Prénom du participant: $cFirstName $cLastName".Util::newLines(1);
        $body .= "Nom de l'évènement: ".$event->getName().Util::newLines(1);
        $body .= "Organisé le: ".$event->getStartDate()->format('Y-m-d')." à ".$event->getStartTime()->format('H:i').Util::newLines(1);
        $body .= "Lieu: ".$event->getLocation().Util::newLines(1);
        $body .= "------------------------------------------------------------------------".Util::newLines(5);
        $body .= "Merci,".Util::newLines(1);
        $body .= "Equipe PopOut.";
        $this->popMailer->send($to,$from,$subject,$body);

        /*
        ******************************************** Send email notification to pro user *********************************************
        */
        $pro = $event->getProUser();
        $pFirstName = ucfirst($pro->getFirstName());
        $pLastName = strtoupper($pro->getLastName());
        $to = [$pro->getEmail() => $pFirstName.' '.$pLastName];
        $from = [$this->popEmail => $this->popUsername];
        $subject = "Frais de participation à votre évènement: ".$event->getName();
        $body = "Bonjour $pFirstName $pLastName,".Util::newLines(2);
        $body .= "Vous avez reçu le frais de participation à votre évènement: ".$event->getName().Util::newLines(3);
        $body .= "------------------------------------------------------------------------".Util::newLines(1);
        $body .= "---------------------------- Récapitulatif -----------------------------".Util::newLines(1);
        $body .= "------------------------------------------------------------------------".Util::newLines(2);
        $body .= "Nom & Prénom du participant: $cFirstName $cLastName".Util::newLines(1);
        $body .= "Nom de l'évènement: ".$event->getName().Util::newLines(1);
        $body .= "Organisé le: ".$event->getStartDate()->format('Y-m-d')." à ".$event->getStartTime()->format('H:i').Util::newLines(1);
        $body .= "Lieu: ".$event->getLocation().Util::newLines(1);
        $body .= "------------------------------------------------------------------------".Util::newLines(5);
        $body .= "Merci,".Util::newLines(1);
        $body .= "Equipe PopOut.";
        $this->popMailer->send($to, $from, $subject, $body);
    }
}