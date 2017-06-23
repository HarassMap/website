<?php

namespace Harassmap\Incidents\Updates;

use Harassmap\Incidents\Models\Assistance;
use Seeder;
use Symfony\Component\Yaml\Yaml;

class SeedHarassmapIncidentsAssistanceTable extends Seeder
{
    public function run()
    {
        $filename = __DIR__ . '/data/assistance.yml';
        $fixtures = Yaml::parse(file_get_contents($filename));

        $assistanceData = $fixtures['types'];

        foreach ($assistanceData as $key => $typeData) {
            Assistance::create([
                'title' => $typeData['title']
            ]);
        }
    }
}