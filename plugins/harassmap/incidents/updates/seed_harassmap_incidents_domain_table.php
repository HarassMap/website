<?php

namespace Harassmap\Incidents\Updates;

use Harassmap\Incidents\Models\Domain;
use Seeder;

class SeedHarassmapDomainTable extends Seeder
{
    public function run()
    {
        Domain::create([
            'host' => '*',
            'name' => 'HarassMap',
            'about' => 'HarassMap is based on the idea that if more people start taking action when sexual harassment happens in their presence, we can end this epidemic.',
            'facebook' => 'http://facebook.com',
            'twitter' => 'http://twitter.com',
            'instagram' => 'http://instagram.com',
            'youtube' => 'http://youtube.com',
            'blogger' => 'http://blogger.com',
            'lat' => '30.044420',
            'lng' => '31.235712',
            'zoom' => '11',
        ]);
    }
}