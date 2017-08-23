<?php

namespace Harassmap\Incidents\Classes;

use Backend;
use Harassmap\Incidents\Models\Incident;
use Harassmap\Incidents\Models\Notification;
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
        $isCLI = (php_sapi_name() === 'cli');

        // end early if we are in the cli
        if ($isCLI) {
            return;
        }

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
     * Send email to everyone who has received notifications
     */
    public static function sendNotificationMail()
    {
        // get all the notifications grouped by user
        $collection = Notification
            ::where('read', '=', false)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('user_id');

        // loop through each user
        foreach ($collection as $user_id => $notifications) {
            $user = User::whereId($user_id)->first();
            $reports = [];

            // only notify the user if they have notifications enabled
            if ($user->notification_incident) {

                // loop through each notification
                foreach ($notifications as $notification) {
                    $content = $notification->content;

                    $reports[] = [
                        'link' => $content['link'],
                        'message' => $notification->getTitle()
                    ];
                }

                $data = [
                    'name' => $user->name,
                    'count' => $notifications->count(),
                    'notifications' => $reports
                ];

                Mail::send('harassmap.incidents::mail.user.notifications', $data, function ($message) use ($user) {
                    $message->to($user->email, $user->full_name);
                });
            }
        }
    }

}