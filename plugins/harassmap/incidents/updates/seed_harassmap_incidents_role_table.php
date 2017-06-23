<?php

namespace Harassmap\Incidents\Updates;

use Harassmap\Incidents\Models\Role;
use Seeder;
use Symfony\Component\Yaml\Yaml;

class SeedHarassmapIncidentsRoleTable extends Seeder
{
    public function run()
    {
        $filename = __DIR__ . '/data/roles.yml';
        $fixtures = Yaml::parse(file_get_contents($filename));

        $rolesData = $fixtures['roles'];

        foreach ($rolesData as $key => $roleData) {
            Role::create([
                'name' => $roleData['name']
            ]);
        }
    }
}