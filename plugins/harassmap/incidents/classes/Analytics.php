<?php

namespace Harassmap\Incidents\Classes;

use Carbon\Carbon;
use Harassmap\Incidents\Models\Domain;
use RainLab\User\Models\User;

class Analytics
{

    static $attributable = null;

    const INCIDENT_CREATED = 'Created Incident';
    const INTERVENTION_CREATED = 'Created Intervention';

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


}