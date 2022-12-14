<?php return [
    'plugin' => [
        'name' => 'HarassMap Incidents',
        'description' => '',
        'permissions' => [
            'tab' => 'HarassMap',
            'manage_domains' => 'Manage Domains',
            'access_domain' => 'Access Domains',
            'access_categories' => 'Manage Categories',
            'manage_content_blocks' => 'Manage Content Blocks',
            'manage_user_domains' => 'Manage User Domains',
            'access_settings' => 'Access Settings',
        ],
    ],
    'menu' => [
        'incidents' => 'Incidents',
        'categories' => 'Categories',
        'domain' => 'Domain',
        'content' => 'Content',
        'tips' => 'Tips',
        'countries' => 'Countries',
        'cities' => 'Cities',
    ],
    'model' => [
        'domain' => [
            'host' => 'Host',
            'host_comment' => 'Pattern for matching the domain name.',
            'url' => 'Url',
            'url_comment' => 'This URL is used by the console to properly generate URLs',
            'name' => 'Name',
            'name_comment' => 'Used in logo alt text and emails.',
            'default_language' => 'Default Language',
            'default_language_comment' => 'The default language for this domain',
            'languages' => 'Languages',
            'languages_comment' => 'Which languages are enabled on this domain?',
            'timezone' => 'Timezone',
        ],
        'incident' => [
            'id' => 'ID',
            'public_id' => 'Public ID',
            'date' => 'Date Of Incident',
            'created_at' => 'Date Reported',
            'is_hidden' => 'Hidden',
            'verified' => 'Verified',
            'approved' => 'Approved',
            'is_intervention' => 'Is Intervention',
            'support' => 'Support',
            'domain' => 'Domain',
            'description' => 'Description',
            'user' => 'User',
        ],
        'logo' => [
            'domain' => 'Domain',
            'language' => 'Language',
            'image' => 'Logo Image',
            'position' => 'Logo Position',
        ],
        'content' => [
            'domain' => 'Domain',
            'content_id' => 'Content ID',
            'content' => 'Content',
            'image' => 'Image',
            'link' => 'Link',
        ],
        'common' => [
            'updated_at' => 'Updated',
            'created_at' => 'Created',
        ],
    ],
    'form' => [
        'tab' => [
            'site' => 'Site',
            'logo' => 'Logo',
            'social' => 'Social',
            'map' => 'Map',
            'content' => 'Content',
            'extra' => 'Extra',
            'incident' => 'Incident',
            'intervention' => 'Intervention',
            'location' => 'Location',
            'categories' => 'Categories',
            'analytics' => 'Analytics',
            'tip' => 'Tip',
            'read_more' => 'Read More Button',
            'meta' => 'Meta'
        ],
        'title' => 'Title',
        'description' => 'Description',
        'description_approved' => 'Description Approved',
        'color' => 'Color',
        'domain' => 'Domain',
        'domains' => 'Domains',
        'name' => 'Name',
        'about' => 'About',
        'about_help' => 'A short description of the site.',
        'logo' => [
            'header' => 'Header Logo',
            'footer' => 'Footer Logo',
        ],
        'facebook_app_id' => 'Facebook App Id',
        'facebook' => 'Facebook',
        'twitter' => 'Twitter',
        'instagram' => 'Instagram',
        'youtube' => 'YouTube',
        'blogger' => 'Blogger',
        'tip' => 'Tip',
        'featured_from' => 'Featured From',
        'content' => 'Content',
        'content_id' => 'Content ID',
        'image' => 'Image',
        'link' => 'Link',
        'country' => 'Country',
        'lat' => 'Latitude',
        'lng' => 'Longitude',
        'map_pin_color' => 'Map Pin Color',
        'zoom' => 'Zoom',
        'iso' => 'ISO Code',
        'updated_at' => 'Updated',
        'created_at' => 'Created',
        'date' => 'Date',
        'date_report' => 'Date Of Incident',
        'date_reported' => 'Date Reported',
        'intervention' => 'Intervention',
        'verified' => 'Verified',
        'user' => 'User',
        'id' => 'ID',
        'public_id' => 'Public ID',
        'support' => 'Support',
        'ga_key' => 'Google Analytics Key',
        'twitter_message' => 'Twitter Message',
        'read_more_label' => 'Label',
        'read_more_link' => 'Link',
        'read_more_help' => 'You can leave this blank for "Browse Tips" and link to tip page.',
        'email' => 'From Email'
    ]
];
