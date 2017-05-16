<?php

namespace Harassmap\Domain\Updates;

use Harassmap\Domain\Models\Content;
use Harassmap\Domain\Models\Domain;
use Seeder;

class SeedHarassMapContentTable extends Seeder
{
    public function run()
    {
        $domain = Domain::where('default', '=', true)->first();

        Content::create([
            'domain' => $domain,
            'content_id' => 'homepage.basics',
            'content' => '<p>Learn the basics</p>'
        ]);

        Content::create([
            'domain' => $domain,
            'content_id' => 'homepage.share',
            'content' => '<p>Share your story</p>'
        ]);

        Content::create([
            'domain' => $domain,
            'content_id' => 'homepage.active',
            'content' => '<p>Get active</p>'
        ]);
    }
}
