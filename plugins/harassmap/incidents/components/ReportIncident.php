<?php

namespace Harassmap\Incidents\Components;

use Cms\Classes\ComponentBase;
use Faker\Provider\Uuid;
use Harassmap\Incidents\Models\Category;
use Harassmap\Incidents\Models\Country;
use Harassmap\Incidents\Models\Domain;
use Harassmap\Incidents\Models\Incident;
use Harassmap\Incidents\Models\Location;
use Harassmap\Incidents\Models\Role;
use Redirect;

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
        $domain = Domain::getBestMatchingDomain();

        $this->page['countries'] = Country::all()->lists('name', 'id');
        $this->page['domain'] = $domain;
        $this->page['categories'] = Category::whereHas('domains', function ($query) use ($domain) {
            $query->where('id', '=', $domain->id);
        })->get();
        $this->page['roles'] = Role::all()->lists('name', 'id');
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
        $incident->role_id = $data['role'];
        $incident->description = $data['description'];

        if (array_key_exists('categories', $data)) {
            $incident->categories = $data['categories'];
        }

        $dateTime = strtotime($data['date']);
        $timeTime = strtotime($data['time']);

        if ($dateTime !== false && $timeTime !== false) {
            $date = new \DateTime();
            $date->setTimestamp($dateTime);
            $time = new \DateTime();
            $time->setTimestamp($timeTime);
            $date->setTime($time->format('h'), $time->format('i'), $time->format('s'));
            $incident->date = $date;
        }

        $incident->validate();
        $location->save();

        $incident->location()->add($location);
        $incident->save();

        return Redirect::refresh();
    }

}
