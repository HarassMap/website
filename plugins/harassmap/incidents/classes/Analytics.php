<?php

namespace Harassmap\Incidents\Classes;

use BackendAuth;
use Carbon\Carbon;
use Harassmap\Comments\Models\Comment;
use Harassmap\Incidents\Models\Domain;
use Harassmap\Incidents\Models\Incident;
use RainLab\User\Facades\Auth;
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
     * @param array $tags
     * @param bool $is_error
     * @param bool $is_resolved
     * @param null $execution_time_in_seconds
     * @param null $comments
     */
    public static function capture($event, $occurred_on = null, $tags = null, $is_error = null, $is_resolved = null, $execution_time_in_seconds = null, $comments = null)
    {
        $isCLI = (php_sapi_name() === 'cli');

        // end early if we are in the cli
        if ($isCLI) {
            return;
        }

        $attributable = self::getInstance();
        $user = self::getUser();

        $author = null;

        // if there is a use then build the author array
        if ($user) {
            $author = [
                'user_id' => $user->id,
                'first_name' => self::getFirstName($user),
                'last_name' => self::getLastName($user),
                'email' => $user->email,
                "is_whitelisted" => true
            ];

        }

        $attributable->capture($event, $occurred_on->toDateTimeString(), $author, $tags, $is_error, $is_resolved, $execution_time_in_seconds, $comments);
    }

    public static function measure($metric, $value, $occurred_on = null)
    {
        $isCLI = (php_sapi_name() === 'cli');

        // end early if we are in the cli
        if ($isCLI) {
            return;
        }

        $attributable = self::getInstance();

        $attributable->measure($metric, $value, $occurred_on);
    }

    public static function getUser()
    {
        $user = BackendAuth::getUser();

        if (!$user) {
            $user = Auth::getUser();
        }

        return $user;
    }

    public static function getFirstName($user)
    {
        $name = $user->first_name;

        if ($user instanceof User) {
            $name = $user->name;
        }

        return $name;
    }

    public static function getLastName($user)
    {
        $name = $user->last_aname;

        if ($user instanceof User) {
            $name = $user->surname;
        }

        return $name;
    }

    public static function getEventName($message)
    {
        $user = self::getUser();
        $event = 'Anonymous ';

        if ($user) {
            $event = self::getFirstName($user) . ' ' . self::getLastName($user) . ' ';
        }

        return $event . $message;
    }

    public static function report(Incident $incident, $type = 'created')
    {
        $message = 'incident';
        $tags = [
            'incident_id' => $incident->id
        ];

        if ($incident->is_intervention) {
            $message = 'intervention';
            $tags['intervention_id'] = $incident->id;
        }

        $message = $type . ' ' . $message;

        $event = self::getEventName($message);

        self::capture($event, new Carbon(), $tags);
    }

    public static function reportCreated(Incident $incident)
    {
        self::report($incident, 'created');
    }

    public static function reportEdited(Incident $incident)
    {
        self::report($incident, 'edited');
    }

    public static function reportSupportAdded(Incident $incident)
    {
        self::report($incident, 'added support for an');
    }

    public static function reportDeleted(Incident $incident)
    {
        self::report($incident, 'deleted');
    }

    public static function comment(Comment $comment, $message)
    {
        $event = self::getEventName($message);

        $incident = $comment->topic->incident;

        self::capture($event, new Carbon(), [
            'incident_id' => $incident->id,
            'comment_id' => $comment->id
        ]);
    }

    public static function commentCreated(Comment $comment)
    {
        self::comment($comment, 'commented');
    }

    public static function commentEdited(Comment $comment)
    {
        self::comment($comment, 'edited comment');
    }

    public static function commentDeleted(Comment $comment)
    {
        self::comment($comment, 'deleted comment');
    }

    public static function commentReported(Comment $comment)
    {
        self::comment($comment, 'reported comment');
    }

    public static function domain(Domain $domain, $message)
    {
        $event = self::getEventName($message);

        self::capture($event, new Carbon(), [
            'domain_id' => $domain->id
        ]);
    }

    public static function domainCreated(Domain $domain)
    {
        self::domain($domain, 'created');
    }

    public static function domainEdited(Domain $domain)
    {
        self::domain($domain, 'edited');
    }

    public static function domainDeleted(Domain $domain)
    {
        self::domain($domain, 'deleted');
    }

    public static function user(User $user, $message)
    {
        $event = self::getEventName($message);

        self::capture($event, new Carbon(), [
            'user_id' => $user->id
        ]);
    }

    public static function userCreated(User $user)
    {
        self::user($user, 'created');
    }

    public static function userEdited(User $user)
    {
        self::user($user, 'edited');
    }

    public static function userDeleted(User $user)
    {
        self::user($user, 'edited');
    }

}