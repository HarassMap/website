<?php

namespace Harassmap\Incidents\Console;

use Harassmap\Incidents\Models\Assistance;
use Harassmap\Incidents\Models\Category;
use Harassmap\Incidents\Models\Domain;
use Harassmap\Incidents\Models\Role;
use Illuminate\Console\Command;
use Symfony\Component\Yaml\Yaml;

class UpCommand extends Command
{

    /**
     * @var string The console command name.
     */
    protected $name = 'harassmap:up';

    /**
     * @var string The console command description.
     */
    protected $description = 'Add the starting fixtures to make the site work.';

    /**
     * @var Domain
     */
    protected $domain = null;

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        $this->createDomain();
        $this->createCategories();
        $this->createRoles();
        $this->createAssistanceTypes();
    }

    protected function createDomain()
    {
        $this->domain = Domain::create([
            'host' => '*',
            'name' => 'HarassMap',
            'lat' => '30.044420',
            'lng' => '31.235712',
            'zoom' => '11',
            'email' => 'no-reply@domain.com'
        ]);
    }

    protected function createCategories()
    {
        $filename = __DIR__ . '/data/categories.yml';
        $fixtures = Yaml::parse(file_get_contents($filename));

        $categoriesData = $fixtures['categories'];

        foreach ($categoriesData as $key => $categoryData) {

            Category::create([
                'title' => $categoryData['category_title'],
                'description' => $categoryData['category_description'],
                'sort_order' => $categoryData['category_position'],
                'domain_id' => $this->domain->id,
            ]);
        }
    }

    protected function createRoles()
    {
        $filename = __DIR__ . '/data/roles.yml';
        $fixtures = Yaml::parse(file_get_contents($filename));

        $rolesData = $fixtures['roles'];

        foreach ($rolesData as $key => $roleData) {
            Role::create([
                'name' => $roleData['name'],
                'domain_id' => $this->domain->id,
            ]);
        }
    }

    protected function createAssistanceTypes()
    {
        $filename = __DIR__ . '/data/assistance.yml';
        $fixtures = Yaml::parse(file_get_contents($filename));

        $assistanceData = $fixtures['types'];

        foreach ($assistanceData as $key => $typeData) {
            Assistance::create([
                'title' => $typeData['title'],
                'domain_id' => $this->domain->id,
            ]);
        }
    }

}