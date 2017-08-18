<?php

namespace Harassmap\Incidents\Classes;

use Backend;
use Harassmap\Incidents\Models\Incident;
use Harassmap\Incidents\Models\Notification;
use Harassmap\Incidents\Models\Support;
use Mail;
use RainLab\User\Models\User;

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

    /**
     * Send email to everyone who has received support
     */
    public static function sendSupportMail()
    {
        $collection = Notification::all()->groupBy('user_id');

//        foreach ($collection as $user_id => $items) {
//            $user = User::whereId($user_id)->first();
//            $reports = [];
//
//            // only notify the user if they have notifications enabled
//            if ($user->notification_incident) {
//                foreach ($items as $item) {
//                    $incident = $item->incident;
//
//                    $reports[] = [
//                        'link' => $item->link,
//                        'incident' => $incident,
//                        'count' => $item->count,
//                        'since' => $item->created_at
//                    ];
//
//                }
//
//                $data = [
//                    'name' => $user->name,
//                    'reports' => $reports
//                ];
//
//                Mail::send('harassmap.incidents::mail.user.support', $data, function ($message) use ($user) {
//                    $message->to($user->email, $user->full_name);
//                });
//            }
//        }

        // delete all the supports
        Notification::getQuery()->delete();
    }

}