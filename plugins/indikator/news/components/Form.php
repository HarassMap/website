<?php namespace Indikator\News\Components;

use Cms\Classes\ComponentBase;
use Indikator\News\Models\Subscribers;
use Validator;
use ValidationException;

class Form extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'indikator.news::lang.component.form',
            'description' => ''
        ];
    }

    public function onSubscription()
    {
        $data = post();

        if (Subscribers::where('email', $data['email'])->count() == 1) {
            return;
        }

        $rules = [
            'name'  => 'required|between:2,64',
            'email' => 'required|email|between:8,64'
        ];

        $validation = Validator::make($data, $rules);
        if ($validation->fails()) {
            throw new ValidationException($validation);
        }

        Subscribers::insertGetId([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'common'     => '',
            'created'    => 2,
            'statistics' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}
