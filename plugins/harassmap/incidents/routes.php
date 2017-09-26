<?php

Route::get('api/incidents', [
    'as' => 'incidents',
    'uses' => 'Harassmap\Incidents\Classes\Api\IncidentsController@index'
]);