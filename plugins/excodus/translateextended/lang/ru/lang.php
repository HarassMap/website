<?php

return [
    'strings'     => [
        'plugin_desc'               => 'Добавляет определение языка браузера и языковые префиксы в URL для плагина Rainlab\'s Translate.',
        'settings_label'            => 'Translate Extended',
        'settings_desc'             => 'Настройки Translate Extended.',
        'browser_language_detection'           => 'Определение языка браузера',
        'browser_language_detection_comment'   => 'Позволяет отображать сайт в языке, предпочитаемом браузером.',
        'route_prefixing'            => 'Языковые префиксы в URL',
        'route_prefixing_comment'    => 'Добавляет префикс языка в адреса страниц.',
        'prefer_user_session'           => 'Отдавать предпочтение сесси пользователя автоопределению языка браузера',
        'prefer_user_session_comment'   => 'Если включено, язык, установленный в сессии пользователя, будет иметь приоритет над языком браузера. Если выключено, язык будет определяться при каждом входе посетителя на сайт.',
        'homepage_redirect'             => 'Редирект главной страницы',
        'homepage_redirect_comment'     => 'Если включено, префикс языка будет добавлен в адрес главной страницы.'
    ],
    'permissions' => [
        'tab'      => 'Excodus',
        'settings' => 'Настройки Translate Extended',
    ],
];
