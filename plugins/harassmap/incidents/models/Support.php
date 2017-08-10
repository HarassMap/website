<?php

namespace Harassmap\Incidents\Models;

use Cms\Classes\Page;
use Model;
use October\Rain\Database\Traits\Validation;
use RainLab\User\Models\User;

/**
 * Harassmap\Incidents\Models\Support
 *
 * @property int $id
 * @property int $user_id
 * @property int $incident_id
 * @property int $count
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string $link
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Support whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Support whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Support whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Support whereIncidentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Support whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Support whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Support whereUserId($value)
 * @mixin \Eloquent
 */
class Support extends Model
{
    use Validation;

    public $table = 'harassmap_incidents_support';

    public $rules = [
        'user_id' => 'required',
        'incident_id' => 'required',
        'count' => 'required',
        'link' => 'required',
    ];

    public $belongsTo = [
        'user' => User::class,
        'incident' => Incident::class
    ];

    public static function addIncidentSupport(Incident $incident)
    {
        $support = Support::where('incident_id', '=', $incident->id)->first();

        if (!$support) {
            $support = new Support();
            $support->incident_id = $incident->id;
            $support->user_id = $incident->user_id;
            $support->count = 0;
            $support->link = Page::url('reports/view', ['id' => $incident->public_id]);
        }

        $support->count++;
        $support->save();

    }

}