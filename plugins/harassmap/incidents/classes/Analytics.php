<?php

namespace Harassmap\Incidents\Classes;

use Carbon\Carbon;
use Harassmap\Comments\Models\Comment;
use Harassmap\Incidents\Models\Domain;
use Harassmap\Incidents\Models\Incident;
use RainLab\User\Models\User;

class Analytics
{

    static $attributable = null;

    /**
     * @return Attributable
     */
    public static function getInstance()
    {
        if (is_null(self::$attributable)) {
            $domain = Domain::getBestMatchingDomain();

            self::$attributable = new Attributable();
            self::$attributable->key = $domain->attributable_key;
        }

        return self::$attributable;
    }

    /**
     * @param $event
     * @param Carbon $occurred_on
     * @param User $author
     * @param array $tags
     * @param bool $is_error
     * @param bool $is_resolved
     * @param null $execution_time_in_seconds
     * @param null $comments
     */
    public static function capture($event, $occurred_on = null, $author = null, $tags = null, $is_error = null, $is_resolved = null, $execution_time_in_seconds = null, $comments = null)
    {
        $attributable = self::getInstance();

        // if there is an author
        if ($author) {
            $user = $author;

            $author = [
                'user_id' => $user->id,
                'first_name' => $user->name,
                'last_name' => $user->surname,
                'email' => $user->email,
                "is_whitelisted" => true
            ];
        }

        $attributable->capture($event, $occurred_on, $author, $tags, $is_error, $is_resolved, $execution_time_in_seconds, $comments);
    }

    public static function measure($metric, $value, $occurred_on = null)
    {
        $attributable = self::getInstance();

        $attributable->measure($metric, $value, $occurred_on);
    }

    public static function getEventName($message, User $user = null)
    {
        $event = 'Anonymous ';

        if ($user) {
            $event = $user->name . ' ' . $user->surname . ' ';
        }

        return $event . $message;
    }

    public static function incidentCreated(Incident $incident, User $user = null)
    {
        $event = self::getEventName('created incident', $user);

        self::capture($event, $incident->created_at, $user, [
            'incident_id' => $incident->id
        ]);
    }

    public static function interventionCreated(Incident $incident, User $user = null)
    {
        $event = self::getEventName('created intervention', $user);

        self::capture($event, $incident->created_at, $user, [
            'intervention_id' => $incident->id,
            'incident_id' => $incident->id
        ]);
    }

    public static function comment(Comment $comment, $message, User $user = null)
    {
        $event = self::getEventName($message, $user);

        $incident = $comment->topic->incident;

        self::capture($event, $comment->created_at, $user, [
            'incident_id' => $incident->id,
            'comment_id' => $comment->id
        ]);
    }

    public static function commentCreated(Comment $comment, User $user = null)
    {
        self::comment($comment, $user, 'commented');
    }

    public static function commentEdited(Comment $comment, User $user = null)
    {
        self::comment($comment, $user, 'edited comment');
    }

    public static function commentDeleted(Comment $comment, User $user = null)
    {
        self::comment($comment, $user, 'deleted comment');
    }

    public static function commentReported(Comment $comment, User $user = null)
    {
        self::comment($comment, $user, 'reported comment');
    }

}