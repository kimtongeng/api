<?php

namespace App\Http\Controllers\Mobile\Modules\SocietySecurity;

use App\Models\Contact;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Helpers\StringHelper;
use App\Models\BusinessComment;
use App\Enums\Types\CommentType;
use App\Helpers\Utils\ImagePath;
use App\Http\Controllers\Controller;
use App\Enums\Types\ContactNotificationType;
use App\Enums\Types\SenderType;
use App\Models\News;

class NewsCommentCrudController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    public function addComment(Request $request)
    {
        $this->validate($request, [
            'news_id' => 'required|exists:news,id',
            'contact_id' => 'required|exists:contact,id',
            'type' => 'required|numeric|min:1|max:3',
            'comment' => 'required',
            'sender' => 'required|numeric|min:1|max:2',
        ]);

        $comment = new BusinessComment();

        $request->merge([
            BusinessComment::BUSINESS_ID => $request->input('news_id'),
        ]);

        $comment->setData($request);

        if ($comment->save()) {
            if (!empty($request->input('type'))) {
                if ($request->input('type') == CommentType::getText()) {
                    $comment->{BusinessComment::COMMENT} = $request->input('comment');
                    $comment->save();
                } else if ($request->input('type') == CommentType::getImage()) {
                    $image = StringHelper::uploadImage($request->input('comment'), ImagePath::newsComment);
                    $comment->{BusinessComment::COMMENT} = $image;
                    $comment->save();
                } else if ($request->input('type') == CommentType::getAudio()) {
                    $audio = StringHelper::uploadAudio($request->input('comment'), ImagePath::newsComment);
                    $comment->{BusinessComment::COMMENT} = $audio;
                    $comment->save();
                }
            }

            $contactNotiType = '';
            if (SenderType::getParticipantSender() == $request->input('sender')) {
                $contactNotiType = ContactNotificationType::getParticipantComment();
            } else if (SenderType::getPosterSender() == $request->input('sender')) {
                $contactNotiType = ContactNotificationType::getPosterComment();
            }

            $data = [
                'image' => News::find($comment->{BusinessComment::BUSINESS_ID})->image,
            ];

            $sendResponse = Notification::newsNotification(
                $contactNotiType,
                env('TOPIC_NEWS_COMMENT') . $request->input('news_id'),
                $comment->{BusinessComment::BUSINESS_ID},
                $data
            );
            info('Mobile Notification News Comment: ' . $sendResponse);

            $response = [
                'id' => $comment->id,
                'news_id' => $comment->business_id,
                'contact_id' => $comment->contact_id,
                'contact_name' => Contact::find($comment->contact_id)->fullname,
                'contact_image' => Contact::find($comment->contact_id)->profile_image,
                'type' => $comment->type,
                'comment' => $comment->comment,
                'created_at' => $comment->created_at,
            ];

            return $this->responseWithData($response);
        }
    }

    public function getCommentList(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.news_id' => 'required|exists:news,id',
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');

        $filter = $request->input('filter');

        $data = BusinessComment::listComment($filter)->paginate($tableSize);

        return $this->responseWithPagination($data);
    }
}
