<?php

namespace Harassmap\Incidents\Models;

use Model;
use October\Rain\Database\Traits\Validation;

/**
 * Harassmap\Incidents\Models\Location
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