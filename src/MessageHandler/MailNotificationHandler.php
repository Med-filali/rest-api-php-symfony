<?php

namespace App\MessageHandler;

use App\Message\MailNotification;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

#[AsMessageHandler]
class MailNotificationHandler
{
    private $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    public function __invoke(MailNotification $message)
    {

        // generate a signed url and email it to the user
        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $message->getUser(),
            (new TemplatedEmail())
                ->from(new Address('el.filali.mohammed3@gmail.com', 'Acme Mail Bot'))
                ->to($message->getUser()->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );
    }
}
