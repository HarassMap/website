<?php

namespace Harassmap\Incidents\Updates;

use Harassmap\Incidents\Models\City;
use Harassmap\Incidents\Models\Country;
use Seeder;
use Symfony\Component\Yaml\Yaml;

class SeedHarassMapIncidentsCityTable extends Seeder
{
    public function run()
    {
        $filename = __DIR__ . '/data/cities.yml';
        $fixtures = Yaml::parse(file_get_contents($filename));

        $citiesData = $fixtures['cities'];

        foreach ($citiesData as $key => $cityData) {

            $country = Country::whereIso($cityData['country'])->first();

            City::create([
                'name' => $cityData['city'],
                'lat' => $cityData['city_lat'],
                'lng' => $cityData['city_lon'],
                'country_id' => $country->id
            ]);
        }
    }
}