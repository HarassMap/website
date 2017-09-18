<?php

namespace Harassmap\Incidents\Updates;

use Harassmap\Incidents\Models\Assistance;
use Harassmap\Incidents\Models\Domain;
use Seeder;
use Symfony\Component\Yaml\Yaml;

class SeedHarassmapIncidentsAssistanceTable extends Seeder
{
    public function run()
    {
        $domain = Domain::where('host', '=', '*')->first();

        $filename = __DIR__ . '/data/assistance.yml';
        $fixtures = Yaml::parse(file_get_contents($filename));

        $assistanceData = $fixtures['types'];

        foreach ($assistanceData as $key => $typeData) {
            Assistance::create([
                'title' => $typeData['title'],
                'domain_id' => $domain->id,
            ]);
        }
    }
}