<?php

namespace Harassmap\Incidents\Updates;

use Harassmap\Incidents\Models\Category;
use Harassmap\Incidents\Models\Domain;
use Seeder;
use Symfony\Component\Yaml\Yaml;

class SeedHarassmapIncidentsCategoryTable extends Seeder
{
    public function run()
    {
        $domain = Domain::where('host', '=', '*')->first();

        $filename = __DIR__ . '/data/categories.yml';
        $fixtures = Yaml::parse(file_get_contents($filename));

        $categoriesData = $fixtures['categories'];

        foreach ($categoriesData as $key => $categoryData) {

            Category::create([
                'title' => $categoryData['category_title'],
                'description' => $categoryData['category_description'],
                'sort_order' => $categoryData['category_position'],
                'domain_id' => $domain->id,
            ]);
        }
    }
}