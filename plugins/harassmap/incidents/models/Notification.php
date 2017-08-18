<?php namespace Harassmap\Incidents\Models;

use Model;
use October\Rain\Database\Traits\Validation;

/**
 * Model
 */
class Notification extends Model
{
    use Validation;

    public $table = 'harassmap_incidents_notifications';

    public $rules = [
    ];

    public static function addIncidentSupport(Incident $incident)
    {

    }
}