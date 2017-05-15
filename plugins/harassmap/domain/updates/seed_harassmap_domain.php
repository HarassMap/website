<?php

namespace Harassmap\Domain\Updates;

use Harassmap\Domain\Models\Domain;
use Seeder;

class SeedHarassmapDomainTable extends Seeder
{
    public function run()
    {
        $domain = Domain::create([
            'host' => '*'
        ]);
    }
}
