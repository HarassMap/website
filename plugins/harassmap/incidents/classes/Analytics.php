<?php

namespace Harassmap\Incidents\Classes;

use BackendAuth;
use Carbon\Carbon;
use Exception;
use Harassmap\Comments\Models\Comment;
use Harassmap\Incidents\Models\Domain;
use Harassmap\Incidents\Models\Incident;
use Harassmap\Incidents\Models\Settings;
use Log;
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
            $api_key = Settings::get('attributable_api_key');

            self::$attributable = new Attributable();
            self::$attributable->key = $api_key;
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

        $domain = Domain::getBestMatchingDomain();

        $tags['domain_id'] = $domain->id;

        try {
            $attributable->capture($event . ' on ' . $domain->name, $occurred_on->toDateTimeString(), $author, $tags, $is_error, $is_resolved, $execution_time_in_seconds, $comments);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    public static function measure($metric, $value, $occurred_on = null)
    {
        $isCLI = (php_sapi_name() === 'cli');

        // end early if we are in the cli
        if ($isCLI) {
            return;
        }

        $domain = Domain::getBestMatchingDomain();

        $attributable = self::getInstance();

        try {
            $attributable->measure($domain->name . ' ' . $metric, $value, $occurred_on);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
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
        $name = $user->last_name;

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

    public static function report(Incident $incident, $type = 'Created')
    {
        $message = 'Incident';
        $tags = [
            'incident_id' => $incident->id
        ];

        if ($incident->is_intervention) {
            $message = 'Intervention';
            $tags['intervention_id'] = $incident->id;
        }

        $message = $type . ' ' . $message;

        $event = self::getEventName($message);

        self::capture($event, new Carbon(), $tags);
    }

    public static function reportCreated(Incident $incident)
    {
        self::report($incident, 'created');
        self::measureReports($incident);
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
        self::measureReports($incident);
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
        self::measureComments($comment);
    }

    public static function commentEdited(Comment $comment)
    {
        self::comment($comment, 'edited a comment');
    }

    public static function commentDeleted(Comment $comment)
    {
        self::comment($comment, 'deleted a comment');
        self::measureComments($comment);
    }

    public static function commentReported(Comment $comment)
    {
        self::comment($comment, 'reported a comment');
    }

    public static function domain(Domain $domain, $message)
    {
        $event = self::getEventName($message);

        self::capture($event . ' a Domain', new Carbon(), [
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

        self::capture($event . ' a User', new Carbon(), [
            'user_id' => $user->id
        ]);
    }

    public static function userCreated(User $user)
    {
        self::user($user, 'created');
        self::measureUsers($user);
    }

    public static function userEdited(User $user)
    {
        self::user($user, 'edited');
    }

    public static function userDeleted(User $user)
    {
        self::user($user, 'deleted');
        self::measureUsers($user);
    }

    public static function measureReports(Incident $incident)
    {
        $domain = Domain::getBestMatchingDomain();

        $metric = 'Incidents';
        $query = Incident::where('domain_id', '=', $domain->id);

        if ($incident->is_intervention) {
            $metric = 'Interventions';
            $query->where('is_intervention', '=', true);
        } else {
            $query->where('is_intervention', '=', false);
        }

        $count = $query->count();

        self::measure($metric, $count);
    }

    public static function measureComments(Comment $comment)
    {
        $topic = $comment->topic;

        $metric = 'Report Comments (' . $topic->code . ')';
        $count = Comment::where('topic_id', '=', $topic->id)->count();

        self::measure($metric, $count);
    }

    public static function measureUsers(User $user)
    {
        $domain = Domain::getBestMatchingDomain();

        $metric = 'Users';
        $count = User::where('domain_id', '=', $domain->id)->count();

        self::measure($metric, $count);
    }

}