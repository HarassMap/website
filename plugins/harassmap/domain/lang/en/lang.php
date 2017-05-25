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
            'main' => 'Domain',
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
            'domain_help' => 'Which domain is this tip for?'
        ]
    ],
    'content' => [
        'form' => [
            'domain_help' => 'Which domain is this content block for?',
            'content_id_help' => 'Where should this be placed on the site?'
        ]
    ],
    'form' => [
        'domain' => 'Domain',
        'host' => 'Host',
        'logo' => [
            'header' => 'Header Logo',
            'footer' => 'Footer Logo'
        ],
        'about' => 'About',
        'about_help' => 'A short description of the site.',
        'facebook' => 'Facebook',
        'twitter' => 'Twitter',
        'instagram' => 'Instagram',
        'youtube' => 'YouTube',
        'blogger' => 'Blogger',
        'tip' => 'Tip',
        'featured_from' => 'Featured From',
        'content' => 'Content',
        'content_id' => 'Content ID',
    ],
];