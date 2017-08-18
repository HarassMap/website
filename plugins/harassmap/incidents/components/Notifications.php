<?php

namespace Harassmap\Incidents\Components;

use App;
use Cms\Classes\ComponentBase;
use Harassmap\Incidents\Models\Incident;
use Harassmap\Incidents\Models\Notification;
use RainLab\User\Facades\Auth;

class Notifications extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Notifications',
            'description' => 'Shows a users notifications'
        ];
    }

    public function onRun()
    {
        $user = Auth::getUser();

        // find the incident with the public id
        $notifications = Notification
            ::where('user_id', '=', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $this->page['notifications'] = $notifications;

        // after the page is sent mark all the notifications as read
        $this->controller->bindEvent('page.postprocess', function() use ($notifications) {
            foreach ($notifications as $notification) {
                $notification->read = true;
                $notification->save();
            }
        });
    }

    public function onDelete()
    {
        $notification = Notification::find(post('notification'));

        if($notification) {
            $notification->delete();
        }
    }

}
