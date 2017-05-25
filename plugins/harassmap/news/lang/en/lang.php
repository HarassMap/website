<?php return [
    'plugin' => [
        'name' => 'HarassMap News',
        'description' => 'Plugin to allow domain specific News',
    ],
    'menu' => [
        'main' => 'News'
    ],
    'component' => [
        'post_list' => [
            'posts_per_page' => [
                'label' => 'Posts per Page',
                'help' => 'How many posts should we show per page?'
            ]
        ],
    ],
    'post' => [
        'page_name' => 'Post Page',
        'page_help' => 'The page that displays the individual posts',
    ],
    'post_list' => [
        'page_name' => 'Post List Page',
        'page_help' => 'The page that displays a list of all the posts',
    ],
    'form' => [
        'intro' => 'Intro',
        'content' => 'Content',
        'title' => 'Title',
        'slug' => 'Slug',
        'domain' => 'Domain',
        'image' => 'Image',
        'published' => 'Published',
    ],
];