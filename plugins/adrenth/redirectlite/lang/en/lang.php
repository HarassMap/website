<?php

return [
    'plugin' => [
        'name' => 'Redirect Lite',
        'description' => 'Easily manage redirects (lite version of Redirect plugin).',
    ],
    'permission' => [
        'access_redirects' => [
            'label' => 'Redirects',
            'tab' => 'Redirects',
        ],
    ],
    'navigation' => [
        'menu_label' => 'Redirects',
        'menu_description' => 'Manage redirects',
    ],
    'redirect' => [
        'redirect' => 'Redirect',
        'from_url' => 'Source Path',
        'from_url_placeholder' => '/source/path',
        'from_url_comment' => 'The source path to match.',
        'to_url' => 'Target Path or URL',
        'to_url_placeholder' => '/absolute/path, relative/path or http://target.url', // changed since 2.0.6
        'to_url_comment' => 'The target path or URL to redirect to.',
        'status_code' => 'HTTP Status Code',
        'sort_order' => 'Sort Order',
        'permanent' => '301 - Permanent',
        'temporary' => '302 - Temporary',
        'see_other' => '303 - See Other',
        'not_found' => '404 - Not Found',
        'gone' => '410 - Gone',
        'none' => 'none',
        'priority' => 'Priority',
        'hits' => 'Hits',
        'return_to_redirects' => 'Return to redirects list',
        'delete_confirm' => 'Are you sure?',
        'created_at' => 'Created at',
        'modified_at' => 'Modified at',
        'type' => 'Type',
        'last_used_at' => 'Last Used At',
        'name' => 'Name',
        'date_time' => 'Date & Time',
        'date' => 'Date',
        'truncate_confirm' => 'Are you sure you want to delete ALL records?',
        'truncating' => 'Deleting...',
        'warning' => 'Warning',
        'general_confirm' => 'Are you sure you want to do this?',
    ],
    'list' => [
        'no_records' => 'There are no redirects in this view.',
        'switch_is_enabled' => 'Enabled',
    ],
    'title' => [
        'redirects' => 'Manage redirects',
        'create_redirect' => 'Create redirect',
        'edit_redirect' => 'Edit redirect',
    ],
    'buttons' => [
        'add' => 'Add',
        'new_redirect' => 'New redirect',
        'create_redirects' => 'Create redirects',
        'create_redirect' => 'Create redirect',
        'delete' => 'Delete',
        'reorder_redirects' => 'Reorder',
    ],
    'flash' => [
        'success_created_redirects' => 'Successfully created :count redirects',
        'truncate_success' => 'Successfully deleted all records',
        'delete_selected_success' => 'Successfully deleted selected records',
    ],
];
