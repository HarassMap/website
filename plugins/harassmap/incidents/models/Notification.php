<?php namespace Harassmap\Incidents\Models;

use Cms\Classes\Page;
use Model;
use October\Rain\Database\Traits\Validation;
use RainLab\User\Models\User;

/**
 * Harassmap\Incidents\Models\Notification
 *
 * @property int $id
 * @property string $type
 * @property string $reference
 * @property int $user_id
 * @property string $content
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Notification whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Notification whereReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Notification whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Notification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Notification whereUserId($value)
 * @mixin \Eloquent
 */
class Notification extends Model
{
    use Validation;

    public $table = 'harassmap_incidents_notifications';

    public $belongsTo = [
        'user' => User::class
    ];

    public $rules = [
        'user' => 'required',
        'content' => 'required',
        'type' => 'required',
    ];

    protected $jsonable = ['content'];

    // types of notification
    const INCIDENT_SUPPORT = 'incident_support'; // someone supported your incident
    const INCIDENT_COMMENT = 'incident_comment'; // someone commented on your incident
    const COMMENT_REPLY = 'comment_reply'; // someone replied to a comment thread you are on

    /**
     * Add an incident support notification
     * @param Incident $incident
     */
    public static function addIncidentSupport(Incident $incident)
    {
        // only do this if there is a user
        if ($incident->user_id) {
            $notification = self
                ::where('user_id', '=', $incident->user_id)
                ->where('reference', '=', $incident->id)
                ->where('type', '=', self::INCIDENT_SUPPORT)
                ->where('read', '=', false)
                ->first();

            if (!$notification) {
                $notification = new Notification();
                $notification->user_id = $incident->user_id;
                $notification->reference = $incident->id;
                $notification->type = self::INCIDENT_SUPPORT;
            }

            // get the content of the notification
            $content = $notification->content;

            // if the content is empty then create a new one
            if (!$content) {
                $content = [
                    'count' => 0,
                    'link' => Page::url('reports/view', ['id' => $incident->public_id])
                ];
            }

            // now increase the count
            $content['count']++;

            // save the notification
            $notification->content = $content;
            $notification->save();
        }
    }
}