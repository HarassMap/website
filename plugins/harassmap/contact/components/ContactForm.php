<?php

namespace Harassmap\Contact\Components;

use App;
use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use Harassmap\Incidents\Models\Domain;

class ContactForm extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Contact Form',
            'description' => 'Displays contact form'
        ];
    }

    public function onRun()
    {
        $domain = Domain::getBestMatchingDomain();

        if (!$domain->contact_form_enabled) {
            return $this->controller->run('404');
        }
    }

}
