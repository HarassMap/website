<?php

namespace Harassmap\Incidents\Classes;

use Backend;
use Harassmap\Incidents\Models\Incident;
use Mail;

class Mailer
{

    /**
     * Send an email to domain admins that the incident has been created
     * @param Incident $incident
     */
    public static function incidentCreated(Incident $incident)
    {

        // get the domain that the incident is part of
        $domain = $incident->domain;
        $users = $domain->users;

        foreach ($users as $user) {

            $data = [
                'name' => $user->first_name,
                'link' => Backend::url('harassmap/incidents/incidents/update', ['id' => $incident->id])
            ];

            Mail::send('harassmap.incidents::mail.admin.report', $data, function ($message) use ($user) {
                $message->to($user->email, $user->getFullNameAttribute());
            });
        }
    }

}