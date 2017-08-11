<?php

namespace Harassmap\Contact\Classes;

use JanVince\SmallContactForm\Models\Message;
use Mail;

class Mailer
{

    /**
     * Send an email to domain admins that someone has contacted them.
     * @param Message $message
     */
    public static function sendContactEmail(Message $message)
    {

        // get the domain that the incident is part of
        $domain = $message->domain;
        $users = $domain->users;

        foreach ($users as $user) {

            $fields = [];
            $formData = $message->form_data;

            foreach ($formData as $key => $value) {

                // skip helpers
                if (substr($key, 0, 1) == '_') {
                    continue;
                }

                $fields[$key] = $value;

            }

            Mail::send('janvince.smallcontactform::mail.notification', ['fields' => $fields], function ($message) use ($user) {
                $message->to($user->email, $user->getFullNameAttribute());
            });
        }
    }

}