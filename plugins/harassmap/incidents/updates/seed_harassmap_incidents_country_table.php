<?php

namespace Harassmap\Incidents\Updates;

use Harassmap\Incidents\Models\Country;
use Seeder;
use Symfony\Component\Yaml\Yaml;

class SeedHarassmapBaseCountryTable extends Seeder
{
    public function run()
    {
        $filename = __DIR__ . '/data/countries.yml';
        $fixtures = Yaml::parse(file_get_contents($filename));

        $countriesData = $fixtures['countries'];

        foreach ($countriesData as $key => $countryData) {
            Country::create([
                'iso' => $countryData['iso'],
                'name' => $countryData['country']
            ]);
        }
    }
}