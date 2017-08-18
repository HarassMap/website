<?php

namespace Harassmap\Comments\Classes;

use Backend;
use Harassmap\Comments\Models\Comment;
use Harassmap\Incidents\Models\Incident;
use Harassmap\Incidents\Models\Support;
use Mail;
use RainLab\User\Models\User;

class Mailer
{

    public static function commentReported(Comment $comment)
    {
        $topic = $comment->topic;
        $incident = $topic->incident;
        $domain = $incident->domain;
        $users = $domain->users;

        // only send the email on the first flag
        if($comment->flags === 1) {
            foreach ($users as $user) {
                $data = [
                    'name' => $user->first_name,
                    'comment' => $comment->content,
                    'link' => Backend::url('harassmap/comments/comments/update', ['id' => $comment->id])
                ];

                Mail::send('harassmap.comments::mail.admin.flag', $data, function ($message) use ($user) {
                    $message->to($user->email, $user->getFullNameAttribute());
                });
            }
        }
    }

}