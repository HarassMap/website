<?php

namespace Harassmap\Incidents\Console;

use Carbon\Carbon;
use DateTimeZone;
use Harassmap\Incidents\Models\Assistance;
use Harassmap\Incidents\Models\Category;
use Harassmap\Incidents\Models\Domain;
use Harassmap\Incidents\Models\Incident;
use Harassmap\Incidents\Models\Intervention;
use Harassmap\Incidents\Models\Location;
use Harassmap\Incidents\Models\Role;
use Illuminate\Console\Command;
use parseCSV;

class MigrateCommand extends Command
{

    /**
     * @var string The console command name.
     */
    protected $name = 'harassmap:migrate';

    /**
     * @var string The console command description.
     */
    protected $description = 'Migrate the old data into the database';

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        $csv = new parseCSV(__DIR__ . '/data/final_data.csv');

        $assistance = Assistance::whereTitle('other')->first();
        $domain = Domain::whereHost('*')->first();

        $witness = Role::whereName('I witnessed the incident')->first()->id;
        $harassed = Role::whereName('I was harassed')->first()->id;

        $emptyCategory = Category::whereTitle('BLANK')->first()->id;

        foreach ($csv->data as $data) {
            // get the default host
            $latlng = explode(',', $data['location']);

            if (count($latlng) === 2) {
                $lat = $latlng[0];
                $lng = $latlng[1];
            } else {
                $lat = $latlng[0] . '.' . $latlng[1];
                $lng = $latlng[2] . '.' . $latlng[3];
            }

            $location = new Location();
            $incident = new Incident();
            $intervention = new Intervention();

            $address = !empty($data['addess/description']) ? $data['addess/description'] : 'No Address';
            $town = $data['city/town'];
            $governate = $data['governorate'];

            $city = trim($town . ', ' . $governate, " \t\n\r \v,");

            if (empty($city)) {
                $city = 'No City/Country';
            }

            $location->address = $address;
            $location->city = $city;
            $location->lat = number_format(floatval($lat), 5, '.', '');
            $location->lng = number_format(floatval($lng), 5, '.', '');

            $incident->domain_id = $domain->id;
            $incident->description = $data['description'];
            $incident->approved = true;

            $incident->role_id = $data['reporter_state'] === 'Witness' ? $witness : $harassed;

            // get the date and time from the form in the users timezone
            $date = new Carbon($data['incident_date'], new DateTimeZone($domain->timezone));

            // get the server timezone and store it in that timezone
            $serverTimeZone = date_default_timezone_get();
            $date->setTimezone($serverTimeZone);

            // save the date to the incident
            $incident->date = $date;
            $incident->created_at = new Carbon($data['report_date_time']);

            $categoriesData = explode(',', $data['categories']);
            $categories = [];

            foreach ($categoriesData as $title) {
                $title = trim($title);

                if (!empty($title)) {

                    $category = Category
                        ::where('title','=',$title)
                        ->where('domain_id', '=', $domain->id)
                        ->first();

                    if (!$category) {
                        $category = Category::create([
                            'title' => $title,
                            'description' => $title,
                            'domain_id' => $domain->id
                        ]);
                    }

                    array_push($categories, $category->id);
                }
            }

            if (empty($categories)) {
                $categories = [$emptyCategory];
            }

            $incident->categories = $categories;

            if ($data['intervention'] === 'Yes') {
                $incident->is_intervention = true;
            }

            $incident->save();

            if (empty($incident->id)) {
                print_r($incident->errors());
            }

            $location->incident()->add($incident);
            $location->save();

            if ($data['intervention'] === 'Yes') {
                $intervention->assistance = [$assistance];
                $intervention->incident()->add($incident);
                $intervention->save();
            }
        }
    }

}