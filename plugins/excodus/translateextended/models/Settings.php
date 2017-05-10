<?php
namespace Excodus\TranslateExtended\Models;

use Model;
class Settings extends Model{

    public $implement = [
        'System.Behaviors.SettingsModel'
    ];

    public $settingsCode = 'excodus_translateextended_settings';

    public $settingsFields = 'fields.yaml';
} 