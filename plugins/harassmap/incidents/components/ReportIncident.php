<?php

namespace Harassmap\Incidents\Components;

use Carbon\Carbon;
use Cms\Classes\ComponentBase;
use DateTimeZone;
use Harassmap\Incidents\Models\Assistance;
use Harassmap\Incidents\Models\Category;
use Harassmap\Incidents\Models\Domain;
use Harassmap\Incidents\Models\Incident;
use Harassmap\Incidents\Models\Intervention;
use Harassmap\Incidents\Models\Location;
use Harassmap\Incidents\Models\Role;
use Illuminate\Support\MessageBag;
use Lang;
use RainLab\Translate\Classes\Translator;
use RainLab\Translate\Models\Message;
use RainLab\User\Facades\Auth;
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

        $this->page['categories'] = Category::where('domain_id', '=', $domain->id)->get();
        $this->page['assistance'] = Assistance::where('domain_id', '=', $domain->id)->get()->lists('title', 'id');

        $roles = Role::where('domain_id', '=', $domain->id)
            ->get()->lists('name', 'id');
        $this->page['roles'] = ['' => Message::get('My role was...')] + $roles;

        // timezones
        $zones = timezone_identifiers_list();
        $locale = Translator::instance()->getLocale();
        $this->page['timezones'] = array_combine($zones, array_map(function ($zone) use ($locale) {
            return Lang::get('harassmap.translate::lang.timezones.' . strtolower($zone), array(), $locale);
        }, $zones));
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

        $incident->domain_id = $domain->id;
        $incident->is_intervention = false;
        $incident->role_id = $data['role'];
        $incident->description = $data['description'];

        // if the domain doesn't require descriptions to be approved then auto approve it
        if (!$domain->need_approval) {
            $incident->approved = true;
        }

        if (array_key_exists('categories', $data)) {
            $incident->categories = $data['categories'];
        }

        if (strtotime($data['date']) !== false && strtotime($data['time']) !== false) {

            // get the date and time from the form in the users timezone
            $date = new Carbon($data['date'], new DateTimeZone($data['timezone']));
            $time = new Carbon($data['time'], new DateTimeZone($data['timezone']));
            $date->setTime($time->format('H'), $time->format('i'), $time->format('s'));

            // get the server timezone and store it in that timezone
            $serverTimeZone = date_default_timezone_get();
            $date->setTimezone($serverTimeZone);

            // save the date to the incident
            $incident->date = $date;
        }

        // generate an errors array
        $errors = new MessageBag();

        // if there was an intervention but no type then throw an error
        if ($data['intervention']) {

            if (array_key_exists('assistance', $data)) {
                $intervention->assistance = $data['assistance'];
            }

            $intervention->validate();

            $errors->merge($intervention->errors());
        }

        $location->validate();
        $incident->validate();

        $errors->merge($location->errors());
        $errors->merge($incident->errors());

        // if we have errors
        if (!$errors->isEmpty()) {
            return Redirect::refresh()->withErrors($errors);
        }

        $incident->save();

        $location->incident()->add($incident);
        $location->save();

        if ($data['intervention']) {
            $intervention->incident()->add($incident);
            $intervention->save();
        }

        return Redirect::to($this->pageUrl('report/thanks', ['id' => $incident->public_id]));
    }

}
