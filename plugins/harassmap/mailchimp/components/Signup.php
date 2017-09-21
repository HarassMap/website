<?php

namespace Harassmap\MailChimp\Components;

use ApplicationException;
use Cms\Classes\ComponentBase;
use Harassmap\Incidents\Models\Domain;
use ValidationException;
use Validator;

class Signup extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'Signup Form',
            'description' => 'Sign up a new person to a mailing list.'
        ];
    }

    public function onSignup()
    {
        // get the domain
        $domain = Domain::getBestMatchingDomain();

        if (!$domain->mailchimp_api) {
            throw new ApplicationException('MailChimp API key is not configured.');
        }

        /*
         * Validate input
         */
        $data = post();

        $rules = [
            'email' => 'required|email|min:2|max:64',
        ];

        $validation = Validator::make($data, $rules);

        if ($validation->fails()) {
            throw new ValidationException($validation);
        }

        /*
         * Sign up to Mailchimp via the API
         */
        require_once(plugins_path() . '/harassmap/mailchimp/vendor/MCAPI.class.php');

        $api = new \MCAPI($domain->mailchimp_api);

        $this->page['error'] = null;

        $mergeVars = '';
        if (isset($data['merge']) && is_array($data['merge']) && count($data['merge'])) {
            $mergeVars = $data['merge'];
        }

        if ($api->listSubscribe($domain->mailchimp_list, post('email'), $mergeVars) !== true) {
            $this->page['error'] = $api->errorMessage;
        }
    }
}
