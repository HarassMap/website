<?php return [
    'plugin' => [
        'name' => 'HarassMap Mail',
        'description' => ''
    ],
    'form' => [
        'layout' => [
            'twig_help' => 'The layout can use: {{ content|raw }} to output the email content, {{ css|raw }} to output the styles, {{ domain.logo|raw }} to output the domain logo and {{ domain.name }} to output the domain name.'
        ],
        'test_lang_help' => 'Select the language you want to send the test message in:'
    ]
];