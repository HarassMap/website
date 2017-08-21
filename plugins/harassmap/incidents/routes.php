<?php

use Harassmap\Incidents\Classes\Mailer;

Route::get('api/incidents', [
    'as' => 'incidents',
    'uses' => 'Harassmap\Incidents\Classes\Api\IncidentsController@index'
]);

Route::get('mail/test', function () {
    Mailer::sendNotificationMail();
});