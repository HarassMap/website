<?php

namespace Harassmap\Incidents\Traits;

use BackendAuth;
use Harassmap\Incidents\Models\Domain;

trait DomainOptions
{

    public function getDomainIdOptions()
    {
        return Domain::getDomainIdOptions();
    }

}