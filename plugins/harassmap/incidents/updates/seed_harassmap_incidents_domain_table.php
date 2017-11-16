<?php

namespace Harassmap\Incidents\Updates;

use Harassmap\Incidents\Models\Domain;
use Seeder;

class SeedHarassmapIncidentsDomainTable extends Seeder
{
    public function run()
    {
        Domain::create([
            'host' => '*',
            'name' => 'HarassMap',
            'lat' => '30.044420',
            'lng' => '31.235712',
            'zoom' => '11',
            'email' => 'no-reply@domain.com'
        ]);
    }
}
