<?php namespace Harassmap\Incidents\Models;

use Cms\Classes\Page;
use Harassmap\Comments\Models\Comment;
use Model;
use October\Rain\Database\Traits\Validation;
use RainLab\Translate\Models\Message;
use RainLab\User\Models\User;

/**
 * Harassmap\Incidents\Models\Notification
 *
 * @property int $id
 * @property string $type
 * @property string $reference
 * @property int $user_id
 * @property string $content
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Notification whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Notification whereReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Notification whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Notification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Notification whereUserId($value)
 * @mixin \Eloquent
 * @property int $read
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Notification whereRead($value)
 */
class Notification extends Model
{
    use Validation;

    public $table = 'harassmap_incidents_notifications';

    public $belongsTo = [
        'user' => User::class
    ];

    public $rules = [
        'user' => 'required',
        'content' => 'required',
        'type' => 'required',
    ];

    protected $jsonable = ['content'];

    // types of notification
    const INCIDENT_SUPPORT = 'incident_support'; // someone supported your incident
    const INCIDENT_COMMENT = 'incident_comment'; // someone commented on your incident
    const COMMENT_REPLY = 'comment_reply'; // someone replied to a comment thread you are on

    /**
     * Add an incident support notification
     * @param Incident $incident
     */
    public static function addIncidentSupport(Incident $incident)
    {
        // only do this if there is a user
        if ($incident->user_id) {
            $notification = self::getNotification($incident->user_id, $incident->id, self::INCIDENT_SUPPORT);

            // get the content of the notification
            $content = $notification->content;

            // if the content is empty then create a new one
            if (!$content) {
                $content = [
                    'count' => 0,
                    'link' => Page::url('reports/view', ['id' => $incident->public_id])
                ];
            }

            // now increase the count
            $content['count']++;

            // save the notification
            $notification->content = $content;
            $notification->save();
        }
    }

    /**
     * A comment has been added to an incident
     * @param Comment $comment
     */
    public static function addComment(Comment $comment)
    {
        $topic = $comment->topic;
        $incident = $topic->incident;

        if ($incident->user_id && $incident->user_id !== (int) $comment->user_id) {
            $notification = self::getNotification($incident->user_id, $incident->id, self::INCIDENT_COMMENT);
            self::increaseCommentNotification($notification, $incident);
        }

        $notified = [];

        // loop through all the comments for this topic to notify them of a reply
        foreach ($topic->comments as $otherComment) {

            // notify the comment author that someone else has commented on the topic
            // don't notify more than once
            // don't notify if the user commenting is the incident user
            // don't notify the author of this comment
            if (
                !array_search($otherComment->user_id, $notified)
                && $otherComment->user_id !== $incident->user_id
                && $otherComment->user_id !== (int) $comment->user_id
            ) {
                $notified[] = $otherComment->user_id;

                $notification = self::getNotification($otherComment->user_id, $incident->id, self::COMMENT_REPLY);
                self::increaseCommentNotification($notification, $incident);
            }
        }
    }

    protected static function increaseCommentNotification(Notification $notification, Incident $incident)
    {
        // get the content of the notification
        $content = $notification->content;

        // if the content is empty then create a new one
        if (!$content) {
            $content = [
                'count' => 0,
                'link' => Page::url('reports/view', ['id' => $incident->public_id]) . '#comments'
            ];
        }

        // now increase the count
        $content['count']++;

        // save the notification
        $notification->content = $content;
        $notification->save();
    }

    public static function getNotification($user_id, $reference, $type)
    {
        $notification = self
            ::where('user_id', '=', $user_id)
            ->where('reference', '=', $reference)
            ->where('type', '=', $type)
            ->where('read', '=', false)
            ->first();

        if (!$notification) {
            $notification = new Notification();
            $notification->user_id = $user_id;
            $notification->reference = $reference;
            $notification->type = $type;
        }

        return $notification;
    }

    public function getTitle()
    {
        switch ($this->type) {
            case self::INCIDENT_COMMENT:
                $description = $this->content['count'] > 1 ? 'You have :count new comments on one of your reports.' : 'You have :count new comment on one of your reports.';
                break;
            case self::COMMENT_REPLY:
                $description = $this->content['count'] > 1 ? 'There are :count new comments on a report you have commented on.' : 'There is :count new comment on a report you have commented on.';
                break;
            default;
                $description = $this->content['count'] > 1 ? 'You have :count new expressions of support on one of your reports.' : 'You have :count new expression of support on one of your reports.';
        }

        return Message::trans($description, $this->content);
    }

    public function getLinkText()
    {
        switch ($this->type) {
            case self::INCIDENT_COMMENT:
            case self::COMMENT_REPLY:
                return 'View Comments';
            default;
                return 'View Incident';
        }
    }
}