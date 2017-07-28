<?php

namespace Harassmap\Incidents\Classes;

class Attributable
{
    public $key = null;
    public $apiVersion = "1-0";

    public $settings = [
        'default_to_current_datetime' => true,
        'default_to_current_ip' => true,
        'default_to_current_user_agent' => true,
        'timeout' => 60
    ];

    public $success = [];
    public $errors = [];
    public $warnings = [];

    public function capture($event, $occurred_on = null, $author = null, $tags = null, $is_error = null, $is_resolved = null, $execution_time_in_seconds = null, $comments = null)
    {

        // check for errors
        if (!$this->key) {
            $this->errors[] = "Missing API key";
            return false;
        }

        if (!$this->apiVersion) {
            $this->errors[] = "API version not specified";
            return false;
        }

        if (!$event) {
            $this->errors[] = "Event not specified";
            return false;
        }

        if ($occurred_on && !strtotime($occurred_on)) {
            $this->errors[] = "Badly formatted datetime";
            return false;
        }

        if ($author && !is_array($author)) {
            $this->errors[] = "Author should be an array";
            return false;
        }

        if ($tags && !is_array($tags)) {
            $this->errors[] = "Tags should be an array";
            return false;
        }

        // sanitize data
        if (!@$occurred_on && $this->settings['default_to_current_datetime']) {
            $occurred_on = date('Y-m-d H:i:s');
        }

        if (@$author['phone']) {
            $author['phone'] = preg_replace('/[^\d-]+/', '', $author['phone']);
        }

        if (!@$author['ip'] && $this->settings['default_to_current_ip']) {
            $author['ip'] = (@$_SERVER['REMOTE_ADDR'] ? $_SERVER['REMOTE_ADDR'] : @$_SERVER['REMOTE_HOST']);
        }

        if (!@$author['user_agent'] && $this->settings['default_to_current_user_agent']) {
            $author['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        }

        if (@$author['latitude']) $author['latitude'] = floatval($author['latitude']);

        if (@$author['longitude']) $author['longitude'] = floatval($author['longitude']);

        $is_error = floatval($is_error);

        $is_resolved = floatval($is_resolved);

        $execution_time_in_seconds = floatval($execution_time_in_seconds);

        // create data payload
        $payload = '{' . "\n";
        $payload .= '  "event" : "' . addSlashes($event) . '",' . "\n";
        $payload .= '  "occurred_on" : "' . date('Y-m-d H:i:s', strtotime($occurred_on)) . '",' . "\n";
        if ($author) {
            $payload .= '  "author" : {' . "\n";
            if (@$author['user_id']) $payload .= '    "user_id" : "' . $author['user_id'] . '",' . "\n";
            if (@$author['first_name']) $payload .= '    "first_name" : "' . $author['first_name'] . '",' . "\n";
            if (@$author['last_name']) $payload .= '    "last_name" : "' . $author['last_name'] . '",' . "\n";
            if (@$author['ip']) $payload .= '    "ip" : "' . $author['ip'] . '",' . "\n";
            if (@$author['latitude']) $payload .= '    "latitude" : "' . $author['latitude'] . '",' . "\n";
            if (@$author['longitude']) $payload .= '    "longitude" : "' . $author['longitude'] . '",' . "\n";
            if (@$author['user_agent']) $payload .= '    "user_agent" : "' . $author['user_agent'] . '",' . "\n";
            if (@$author['email']) $payload .= '    "email" : "' . $author['email'] . '",' . "\n";
            if (@$author['phone']) $payload .= '    "phone" : "' . $author['phone'] . '",' . "\n";
            $lists = ['is_blacklisted', 'is_greylisted', 'is_whitelisted'];
            foreach ($lists as $list) {
                if (array_key_exists($list, @$author)) $payload .= '    "' . $list . '" : "' . $author[$list] . '",' . "\n";
            }
            $payload = rtrim($payload, "\n\r, ") . "\n";
            $payload .= '  },' . "\n";
        }
        if ($tags) {
            $payload .= '  "tags" : {' . "\n";
            foreach ($tags as $key => $value) {
                $payload .= '    "' . $key . '" : "' . $value . '",' . "\n";
            }
            $payload = rtrim($payload, "\n\r, ") . "\n";
            $payload .= '  },' . "\n";
        }
        if ($is_error === 1 || $is_error === 0) $payload .= '  "is_error" : "' . $is_error . '",' . "\n";
        if ($is_resolved === 1 || $is_resolved === 0) $payload .= '  "is_resolved" : "' . $is_resolved . '",' . "\n";
        if ($execution_time_in_seconds) $payload .= '  "execution_time_in_seconds" : "' . $execution_time_in_seconds . '",' . "\n";
        if ($comments) $payload .= '  "comments" : "' . addSlashes($comments) . '",' . "\n";

        $payload = trim($payload, "\n\r, ") . "\n";
        $payload .= '}' . "\n";

        // connect with Attributable
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.attributables.com/" . $this->apiVersion . "/" . $this->key . "/capture",
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "application/json",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => $this->settings['timeout'],
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                "Content-Type:application/json",
                "cache-control: no-cache"
            ],
            CURLINFO_HEADER_OUT => true,
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $info = curl_getinfo($curl);

        curl_close($curl);

        // parse & return
        if ($err) {
            $this->errors[] = $err;
            return false;
        } else {

            // separate headers from content
            list($headers, $content) = explode("\r\n\r\n", $response, 2);
            // convert content from JSON to array if required
            $array = json_decode($content, true);
            // return
            return ['headers' => $headers, 'content' => (is_array($array) ? $array : $content)];

        }

    }

    public function measure($metric, $value, $occurred_on = null)
    {

        // check for errors
        if (!$this->key) {
            $this->errors[] = "Missing API key";
            return false;
        }

        if (!$this->apiVersion) {
            $this->errors[] = "API version not specified";
            return false;
        }

        if (!$metric) {
            $this->errors[] = "Metric not specified";
            return false;
        }

        if (!$value) {
            $this->errors[] = "Value not specified";
            return false;
        }

        // create data payload
        $payload = '{' . "\n";
        $payload .= '  "metric" : "' . addSlashes($metric) . '",' . "\n";
        $payload .= '  "value" : "' . $value . '",' . "\n";
        $payload .= '  "occurred_on" : "' . (!$occurred_on || !strtotime($occurred_on) ? date('Y-m-d H:i:s') : $occurred_on) . '",' . "\n";

        $payload = trim($payload, "\n\r, ") . "\n";
        $payload .= '}' . "\n";

        // connect with Attributable
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.attributables.com/" . $this->apiVersion . "/" . $this->key . "/measure",
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "application/json",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => $this->settings['timeout'],
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                "Content-Type:application/json",
                "cache-control: no-cache"
            ],
            CURLINFO_HEADER_OUT => true,
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $info = curl_getinfo($curl);

        curl_close($curl);

        // parse & return
        if ($err) {
            $this->errors[] = $err;
            return false;
        } else {

            // separate headers from content
            list($headers, $content) = explode("\r\n\r\n", $response, 2);
            // convert content from JSON to array if required
            $array = json_decode($content, true);
            // return
            return ['headers' => $headers, 'content' => (is_array($array) ? $array : $content)];

        }

    }

    public function events($eventID = null, $author = null, $startDate = null, $endDate = null, $tags = null, $is_error = null, $is_resolved = null, $page = null)
    {

        // check for errors
        if (!$this->key) {
            $this->errors[] = "Missing API key";
            return false;
        }

        if (!$this->apiVersion) {
            $this->errors[] = "API version not specified";
            return false;
        }

        if ($author && !is_array($author)) {
            $this->errors[] = "Author should be an array";
            return false;
        }

        if ($tags && !is_array($tags)) {
            $this->errors[] = "Tags should be an array";
            return false;
        }

        // create data payload
        $payload = '{' . "\n";
        if ($eventID) $payload .= '  "event_id" : "' . addSlashes($eventID) . '",' . "\n";
        if ($author) {
            $payload .= '  "author" : {' . "\n";
            if (@$author['author_id']) $payload .= '    "author_id" : "' . $author['author_id'] . '",' . "\n";
            if (@$author['user_id']) $payload .= '    "user_id" : "' . $author['user_id'] . '",' . "\n";
            if (@$author['ip']) $payload .= '    "ip" : "' . $author['ip'] . '",' . "\n";
            if (@$author['email']) $payload .= '    "email" : "' . $author['email'] . '",' . "\n";
            if (@$author['phone']) $payload .= '    "phone" : "' . $author['phone'] . '",' . "\n";
            if (array_key_exists('is_blacklisted', @$author)) $payload .= '    "is_blacklisted" : "' . $author['is_blacklisted'] . '",' . "\n";
            if (array_key_exists('is_greylisted', @$author)) $payload .= '    "is_greylisted" : "' . $author['is_greylisted'] . '",' . "\n";
            if (array_key_exists('is_whitelisted', @$author)) $payload .= '    "is_whitelisted" : "' . $author['is_whitelisted'] . '",' . "\n";
            $payload = rtrim($payload, "\n\r, ") . "\n";
            $payload .= '  },' . "\n";
        }
        if ($startDate) $payload .= '  "start_date" : "' . $startDate . '",' . "\n";
        if ($endDate) $payload .= '  "end_date" : "' . $endDate . '",' . "\n";
        if ($tags) {
            $payload .= '  "tags" : {' . "\n";
            foreach ($tags as $key => $value) {
                $payload .= '    "' . $key . '" : "' . $value . '",' . "\n";
            }
            $payload = rtrim($payload, "\n\r, ") . "\n";
            $payload .= '  },' . "\n";
        }
        if ($is_error === 1 || $is_error === 0) $payload .= '  "is_error" : "' . $is_error . '",' . "\n";
        if ($is_resolved === 1 || $is_resolved === 0) $payload .= '  "is_resolved" : "' . $is_resolved . '",' . "\n";
        if ($page) $payload .= '  "page" : "' . $page . '",' . "\n";

        $payload = trim($payload, "\n\r, ") . "\n";
        $payload .= '}' . "\n";

        // connect with Attributable
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.attributables.com/" . $this->apiVersion . "/" . $this->key . "/events",
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "application/json",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => $this->settings['timeout'],
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                "Content-Type:application/json",
                "cache-control: no-cache"
            ],
            CURLINFO_HEADER_OUT => true,
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $info = curl_getinfo($curl);

        curl_close($curl);

        // parse & return
        if ($err) {
            $this->errors[] = $err;
            return false;
        } else {

            // separate headers from content
            list($headers, $content) = explode("\r\n\r\n", $response, 2);
            // convert content from JSON to array if required
            $array = json_decode($content, true);
            // return
            return ['headers' => $headers, 'content' => (is_array($array) ? $array : $content)];

        }

    }

    public function user($author, $page = null)
    {

        // check for errors
        if (!$this->key) {
            $this->errors[] = "Missing API key";
            return false;
        }

        if (!$this->apiVersion) {
            $this->errors[] = "API version not specified";
            return false;
        }

        if (!$author) {
            $this->errors[] = "Author not specified";
            return false;
        } elseif (!is_array($author)) {
            $this->errors[] = "Author should be an array";
            return false;
        }

        // create data payload
        $payload = '{' . "\n";
        if (@$author['author_id']) $payload .= '  "author_id" : "' . $author['author_id'] . '",' . "\n";
        if (@$author['user_id']) $payload .= '  "user_id" : "' . $author['user_id'] . '",' . "\n";
        if (@$author['ip']) $payload .= '  "ip" : "' . $author['ip'] . '",' . "\n";
        if (@$author['email']) $payload .= '  "email" : "' . $author['email'] . '",' . "\n";
        if (@$author['phone']) $payload .= '  "phone" : "' . $author['phone'] . '",' . "\n";
        if (array_key_exists('is_blacklisted', @$author)) $payload .= '  "is_blacklisted" : "' . $author['is_blacklisted'] . '",' . "\n";
        if (array_key_exists('is_greylisted', @$author)) $payload .= '  "is_greylisted" : "' . $author['is_greylisted'] . '",' . "\n";
        if (array_key_exists('is_whitelisted', @$author)) $payload .= '  "is_whitelisted" : "' . $author['is_whitelisted'] . '",' . "\n";
        if ($page) $payload .= '  "page" : "' . $page . '",' . "\n";

        $payload = trim($payload, "\n\r, ") . "\n";
        $payload .= '}' . "\n";

        // connect with Attributable
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.attributables.com/" . $this->apiVersion . "/" . $this->key . "/user",
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "application/json",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => $this->settings['timeout'],
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                "Content-Type:application/json",
                "cache-control: no-cache"
            ],
            CURLINFO_HEADER_OUT => true,
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $info = curl_getinfo($curl);

        curl_close($curl);

        // parse & return
        if ($err) {
            $this->errors[] = $err;
            return false;
        } else {

            // separate headers from content
            list($headers, $content) = explode("\r\n\r\n", $response, 2);
            // convert content from JSON to array if required
            $array = json_decode($content, true);
            // return
            return ['headers' => $headers, 'content' => (is_array($array) ? $array : $content)];

        }

    }

}