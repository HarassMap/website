<?php

namespace Harassmap\Domain\Updates;

use Harassmap\Domain\Models\Domain;
use Seeder;

class SeedHarassMapDomainTable extends Seeder
{
    public function run()
    {
        Domain::create([
            'host' => '*'
        ]);
    }
}
