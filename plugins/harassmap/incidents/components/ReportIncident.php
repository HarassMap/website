<?php

namespace Harassmap\Incidents\Components;

use Cms\Classes\ComponentBase;
use Faker\Provider\Uuid;
use Harassmap\Incidents\Models\Country;
use Harassmap\Incidents\Models\Domain;
use Harassmap\Incidents\Models\Incident;
use Harassmap\Incidents\Models\Location;
use ValidationException;
use Validator;

class ReportIncident extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Report Incident',
            'description' => 'Render a report incident form.'
        ];
    }

    public function onRender()
    {
        $this->page['countries'] = Country::all()->lists('name', 'id');
        $this->page['domain'] = Domain::getBestMatchingDomain();
    }

    public function onSubmit()
    {

        // get the domain
        $domain = Domain::getBestMatchingDomain();

        // this is the data that has been submitted
        $data = post();

        $location = new Location();
        $location->country_id = $data['country'];
        $location->city = $data['city'];
        $location->region = $data['region'];
        $location->address = $data['address'];
        $location->lat = $data['lat'];
        $location->lng = $data['lng'];
        $location->validate();

        $incident = new Incident();
        $incident->domain_id = $domain->id;
        $incident->public_id = Uuid::uuid();
        $incident->description = $data['description'];

        // create a datetime out of the date and time
        $date = new \DateTime();
        $date->setTimestamp(strtotime($data['date']));
        $time = new \DateTime();
        $time->setTimestamp(strtotime($data['time']));
        $date->setTime($time->format('h'), $time->format('i'), $time->format('s'));
        $incident->date = $date;

        $incident->validate();

        $location->save();

        $incident->location()->add($location);
        $incident->save();

        // validate the location
    }

}
