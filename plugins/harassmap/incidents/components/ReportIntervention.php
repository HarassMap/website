<?php

namespace Harassmap\Incidents\Components;

use Carbon\Carbon;
use Cms\Classes\ComponentBase;
use Faker\Provider\Uuid;
use Harassmap\Incidents\Models\Assistance;
use Harassmap\Incidents\Models\Category;
use Harassmap\Incidents\Models\City;
use Harassmap\Incidents\Models\Country;
use Harassmap\Incidents\Models\Domain;
use Harassmap\Incidents\Models\Incident;
use Harassmap\Incidents\Models\Intervention;
use Harassmap\Incidents\Models\Location;
use Harassmap\Incidents\Models\Role;
use Illuminate\Support\MessageBag;
use RainLab\User\Facades\Auth;
use Redirect;

class ReportIntervention extends ComponentBase
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
        })->get()->lists('title', 'id');

        $this->page['roles'] = Role::whereHas('domains', function ($query) use ($domain) {
            $query->where('id', '=', $domain->id);
        })->get()->lists('name', 'id');

        $this->page['assistance'] = Assistance::whereHas('domains', function ($query) use ($domain) {
            $query->where('id', '=', $domain->id);
        })->get()->lists('title', 'id');
    }

    public function onCountrySelect()
    {
        $data = post();

        return City::whereCountryId($data['country'])->get();
    }

    public function onSubmit()
    {
        // get the domain
        $domain = Domain::getBestMatchingDomain();

        // get the current user
        $user = Auth::getUser();

        // this is the data that has been submitted
        $data = post();

        $location = new Location();
        $incident = new Incident();
        $intervention = new Intervention();

        if ($user) {
            $incident->user_id = $user->id;
        }

        $location->country_id = $data['country'];
        $location->city = $data['city'];
        $location->region = $data['region'];
        $location->address = $data['address'];
        $location->lat = number_format($data['lat'], 5, '.', '');
        $location->lng = number_format($data['lng'], 5, '.', '');

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
            $date = new Carbon();
            $date->setTimestamp($dateTime);
            $time = new Carbon();
            $time->setTimestamp($timeTime);
            $date->setTime($time->format('h'), $time->format('i'), $time->format('s'));
            $incident->date = $date;
        }

        // generate an errors array
        $errors = new MessageBag();

        // add the assistance types to the intervention
        if (array_key_exists('assistance', $data)) {
            $intervention->assistance = $data['assistance'];
        }

        $intervention->validate();
        $location->validate();
        $incident->validate();

        $errors->merge($intervention->errors());
        $errors->merge($location->errors());
        $errors->merge($incident->errors());

        // if we have errors
        if (!$errors->isEmpty()) {
            return Redirect::refresh()->withErrors($errors);
        }

        $incident->save();

        $location->incident()->add($incident);
        $location->save();

        $intervention->incident()->add($incident);
        $intervention->save();

        return Redirect::to($this->pageUrl('report/thanks', ['id' => $incident->public_id]));
    }

}
