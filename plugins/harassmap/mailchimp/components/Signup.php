<?php

namespace Harassmap\MailChimp\Components;

use ApplicationException;
use Cms\Classes\ComponentBase;
use Harassmap\Incidents\Models\Domain;
use Log;
use Mailchimp\Mailchimp;
use Mailchimp\MailchimpAPIException;
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
            'email' => 'required|email|min:2|max:64'
        ];

        $validation = Validator::make($data, $rules);

        if ($validation->fails()) {
            throw new ValidationException($validation);
        }

        /*
         * Sign up to Mailchimp via the API
         */
        $api = new Mailchimp($domain->mailchimp_api);

        $this->page['error'] = null;

        try {
            $api->request('POST', '/lists/{list_id}/members',
                ['list_id' => $domain->mailchimp_list],
                ['email_address' => post('email'), 'status' => 'subscribed']);
        } catch (MailchimpAPIException $e) {
            $message = $e->getMessage();

            if (str_contains($message, 'Member Exists')) {
                $this->page['error'] = "You are already a member of our mailing list.";
            } else {
                Log::error($e->getMessage());
                $this->page['error'] = "Something went wrong, please try again later";
            }

        }
    }
}
