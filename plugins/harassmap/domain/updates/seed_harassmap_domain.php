<?php

namespace Harassmap\Domain\Updates;

use Harassmap\Domain\Models\Domain;
use Seeder;

class SeedHarassMapDomainTable extends Seeder
{
    public function run()
    {
        Domain::create([
            'host' => '*',
            'about' => 'HarassMap is based on the idea that if more people start taking action when sexual harassment happens in their presence, we can end this epidemic.',
            'facebook' => 'http://facebook.com',
            'twitter' => 'http://twitter.com',
            'instagram' => 'http://instagram.com',
            'youtube' => 'http://youtube.com',
            'blogger' => 'http://blogger.com'
        ]);
    }
}
