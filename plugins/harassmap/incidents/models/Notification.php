<?php namespace Harassmap\Incidents\Models;

use Cms\Classes\Page;
use Model;
use October\Rain\Database\Traits\Validation;
use RainLab\User\Models\User;

/**
 * Harassmap\Incidents\Models\Notification
 *
 * @property int $id
 * @property int $user_id
 * @property string $content
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Notification whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Notification whereId($value)
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
        'content' => 'required'
    ];

    protected $jsonable = ['content'];

    public static function addIncidentSupport(Incident $incident)
    {
        // only do this if there is a user
        if ($incident->user_id) {
            $notification = self::where('user_id', '=', $incident->user_id)->first();

            if (!$notification) {
                $notification = new Notification();
                $notification->user_id = $incident->user_id;
            }

            $content = $notification->content;

            // default the content to an array
            if (!$content) {
                $content = [];
            }

            // create the support array if it doesnt exist
            if (!array_key_exists('support', $content)) {
                $content['support'] = [];
            }

            if (!array_key_exists($incident->id, $content['support'])) {
                $content['support'][$incident->id] = [
                    'count' => 0,
                    'link' => Page::url('reports/view', ['id' => $incident->public_id])
                ];
            }

            $content['support'][$incident->id]['count']++;

            $notification->content = $content;
            $notification->save();
        }
    }
}