<?php

namespace Harassmap\Incidents\Components;

use Carbon\Carbon;
use Cms\Classes\ComponentBase;
use DateTimeZone;
use Harassmap\Incidents\Classes\Analytics;
use Harassmap\Incidents\Models\Assistance;
use Harassmap\Incidents\Models\Category;
use Harassmap\Incidents\Models\Domain;
use Harassmap\Incidents\Models\Incident;
use Harassmap\Incidents\Models\Intervention;
use Harassmap\Incidents\Models\Location;
use Harassmap\Incidents\Models\Role;
use Illuminate\Support\MessageBag;
use RainLab\Translate\Models\Message;
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

        $this->page['categories'] = Category::whereHas('domains', function ($query) use ($domain) {
            $query->where('id', '=', $domain->id);
        })->get();

        $roles = Role::whereHas('domains', function ($query) use ($domain) {
            $query->where('id', '=', $domain->id);
        })->get()->lists('name', 'id');

        $this->page['roles'] = ['' => Message::get('My role was...')] + $roles;

        $this->page['assistance'] = Assistance::whereHas('domains', function ($query) use ($domain) {
            $query->where('id', '=', $domain->id);
        })->get()->lists('title', 'id');

        // timezones
        $zones = timezone_identifiers_list();
        $this->page['timezones'] = array_combine($zones, $zones);
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

        $location->city = $data['city'];
        $location->address = $data['address'];
        $location->lat = number_format($data['lat'], 5, '.', '');
        $location->lng = number_format($data['lng'], 5, '.', '');

        $incident->generatePublicId();
        $incident->domain_id = $domain->id;
        $incident->is_intervention = true;
        $incident->role_id = $data['role'];
        $incident->description = $data['description'];

        if (array_key_exists('categories', $data)) {
            $incident->categories = $data['categories'];
        }

        if (strtotime($data['date']) !== false && strtotime($data['time']) !== false) {

            // get the date and time from the form in the users timezone
            $date = new Carbon($data['date'], new DateTimeZone($data['timezone']));
            $time = new Carbon($data['time'], new DateTimeZone($data['timezone']));
            $date->setTime($time->format('h'), $time->format('i'), $time->format('s'));

            // get the server timezone and store it in that timezone
            $serverTimeZone = date_default_timezone_get();
            $date->setTimezone($serverTimeZone);

            // save the date to the incident
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

        Analytics::captureIntervention($incident, $user);

        return Redirect::to($this->pageUrl('report/thanks', ['id' => $incident->public_id]));
    }

}
