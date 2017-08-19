<?php

namespace Harassmap\Comments\Components;

use ApplicationException;
use Carbon\Carbon;
use Cms\Classes\ComponentBase;
use Exception;
use Harassmap\Comments\Models\Comment;
use Harassmap\Comments\Models\Topic as TopicModel;
use Harassmap\Comments\Classes\Mailer;
use Harassmap\Incidents\Classes\Analytics;
use Harassmap\Incidents\Models\Notification;
use RainLab\User\Facades\Auth;

class Topic extends ComponentBase
{

    /**
     * @var TopicModel
     */
    public $topic;

    public function componentDetails()
    {
        return [
            'name' => 'Comment Topic',
            'description' => 'Add comments to a page'
        ];
    }

    public function defineProperties()
    {
        return [
            'id' => [
                'title' => 'The ID to use for the topics ID',
                'description' => '',
                'type' => 'string'
            ],
        ];
    }

    public function init()
    {
        $id = $this->property('id');

        if (!$id) {
            throw new Exception('No id specified for the comments topic component');
        }

        $topic = TopicModel::whereCode($id)->first();

        // if there is no topic then create it
        if (!$topic) {
            $topic = TopicModel::createWithCode($id);
        }

        // expose the topic
        $this->topic = $topic;
    }

    public function onRender()
    {
        $this->page['user'] = Auth::getUser();
        $this->page['mode'] = 'view';
        $this->page['comments'] = Comment
            ::orderBy('created_at', 'asc')
            ->where('topic_id', '=', $this->topic->id)
            ->withTrashed()
            ->paginate(10);
    }

    public function onComment()
    {
        // get the user that is commenting
        $user = Auth::getUser();
        $content = post('content');

        $comment = new Comment();
        $comment->content = $content;
        $comment->topic_id = $this->topic->id;
        $comment->user_id = $user->id;
        $comment->validate();

        $comment->save();

        $this->onRender();

        Notification::addComment($comment);
    }

    public function onEdit()
    {
        $comment = $this->getComment();

        $this->page['mode'] = 'edit';
        $this->page['comment'] = $comment;
    }

    public function onUpdate()
    {
        $comment = $this->getComment();

        $comment->content = post('content');
        $comment->approved = false;
        $comment->edited_at = new Carbon();
        $comment->save();

        $this->page['mode'] = 'view';
        $this->page['comment'] = $comment;
    }

    public function onCancel()
    {
        $comment = $this->getComment();

        $this->page['mode'] = 'view';
        $this->page['comment'] = $comment;
    }

    public function onDelete()
    {
        $comment = $this->getComment();
        $comment->user_deleted = true;
        $comment->save();

        $comment->delete();

        $this->page['mode'] = 'delete';
        $this->page['comment'] = $comment;
    }

    public function onFlag()
    {
        $comment = Comment::find(post('comment'));

        $comment->flags++;
        $comment->save();

        Mailer::commentReported($comment);

        Analytics::commentReported($comment);
    }

    /**
     * @return Comment
     * @throws ApplicationException
     */
    protected function getComment()
    {
        $data = post();

        $comment = Comment::find($data['comment']);

        if (!$comment || !$comment->canEdit()) {
            throw new ApplicationException('Permission denied.');
        }

        return $comment;
    }

}
