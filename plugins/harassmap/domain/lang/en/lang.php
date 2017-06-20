<?php return [
    'plugin' => [
        'name' => 'HarassMap Domains',
        'description' => 'Adds support for different content on different domains.',
        'permissions' => [
            'tab' => 'HarassMap',
            'manage_domains' => 'Manage Domains',
            'manage_content_blocks' => 'Manage Content Blocks',
            'manage_user_domains' => 'Manage User Domains',
        ],
        'menu' => [
            'main' => 'Domains',
            'domains' => 'Domains',
            'content' => 'Content Blocks',
            'tips' => 'Weekly Tips',
        ],
    ],
    'tip' => [
        'list' => [
            'page_name' => 'List Page',
            'page_help' => 'Page name to use for clicking on a browse.',
        ],
        'form' => [
            'domain_help' => 'Which domain is this tip for?',
        ],
    ],
    'content' => [
        'form' => [
            'domain_help' => 'Which domain is this content block for?',
            'content_id_help' => 'Where should this be placed on the site?',
        ],
    ],
    'form' => [
        'tab' => [
            'site' => 'Site',
            'social' => 'Social',
            'map' => 'Map',
            'content' => 'Content',
            'extra' => 'Extra',
        ],
        'domain' => 'Domain',
        'host' => 'Host',
        'host_help' => 'Pattern for matching the domain name.',
        'name' => 'Name',
        'about' => 'About',
        'about_help' => 'A short description of the site.',
        'logo' => [
            'header' => 'Header Logo',
            'footer' => 'Footer Logo',
        ],
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
        'country' => 'Country'
    ],
];