<?php namespace Harassmap\Incidents\Models;

use Model;
use October\Rain\Database\Traits\Validation;

/**
 * Model
 *
 * @property int $id
 * @property string $city
 * @property string $address
 * @property string $lat
 * @property string $lng
 * @property int $incident_id
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Location whereAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Location whereCity($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Location whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Location whereLat($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Location whereLng($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Location whereRegion($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Location whereIncidentId($value)
 * @mixin \Eloquent
 */
class Location extends Model
{
    use Validation;

    public $table = 'harassmap_incidents_location';

    public $timestamps = false;

    public $throwOnValidation = false;

    public $rules = [
        'city' => 'required',
        'lat' => 'required',
        'lng' => 'required'
    ];

    public $belongsTo = [
        'incident' => Incident::class
    ];

    public $hidden = ['id', 'incident_id'];

    public function getFullAddress()
    {
        $address = $this->address;

        if(!empty($address)) {
            $address .= ', ';
        }

        return $address . $this->city;
    }
}