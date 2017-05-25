<?php

namespace Harassmap\Domain\Updates;

use Harassmap\Domain\Models\Content;
use Harassmap\Domain\Models\Domain;
use Seeder;

class SeedHarassMapContentTable extends Seeder
{
    public function run()
    {
        $domain = Domain::where('host', '=', '*')->first();

        Content::create([
            'domain' => $domain,
            'content_id' => 'homepage.basics',
            'content' => '<h4>Learn the basics</h4><p>Quick guide to sexual harassment and how to take action.</p>'
        ]);

        Content::create([
            'domain' => $domain,
            'content_id' => 'homepage.share',
            'content' => '<h4>Share your story</h4><p>Reporting is anonymous. This is how it works.</p>'
        ]);

        Content::create([
            'domain' => $domain,
            'content_id' => 'homepage.active',
            'content' => '<h4>Get active</h4><p>There is a lot more you can do. Check these practical tips.</p>'
        ]);

        Content::create([
            'domain' => $domain,
            'content_id' => 'homepage.droplet',
            'content' => '<h2>How we can stop sexual harassment</h2><p>When enough people take action, harassers will find it harder and harder to harass. They will face consequences on the street, in their workplace, from friends and family and coworkers, and eventually they will choose to not harass at all. This is when we reach our goal - zero-tolerance will be the norm and sexual harassment will decrease in Egypt.</p>'
        ]);
    }
}
