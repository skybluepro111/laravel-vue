<?php

namespace App\Http\Controllers\Api;

use App\Channel;
use App\Comment;
use App\Discussion;
use App\Http\Controllers\ApiController;
use App\Http\Requests;
use App\Transformers\ChannelTransformer;
use App\Transformers\CommentTransformer;
use App\Transformers\DiscussionTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use JWTAuth;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class DiscussionController extends ApiController
{
    /**
     * Get all discussion with filter
     * 
     * @param  Request $request
     * @return json
     */
    public function index(Request $request)
    {
    	$per_page = $request->input('per_page') ?: 20;
        $filter_by = $request->input('filter_by') ?: 'all';
        $user = $this->getAuthUser($request);
        if ($filter_by == 'contributed_to' || $filter_by == 'me')
        {
            if(!isset($user->id))
            {
                return $this->respondUnauthorized('No authenticated user found.');
            }
        }
        $discuss = $this->filterDiscussion($filter_by, $per_page, $user);

        $fractal = fractal()
            ->collection($discuss)
            ->includeUser()
            ->includeChannel()
            ->transformWith(new DiscussionTransformer)
            ->paginateWith(new IlluminatePaginatorAdapter($discuss))
            ->toArray();

        return $this->respondSuccessWithData([
            'discuss' => $fractal['data'],
            'pagination' => $fractal['meta']['pagination'],
        ]);
    }

    /**
     * Get all channel list
     * 
     * @param  Channel $channel
     * @return json
     */
    public function channelList(Channel $channel)
    {
        return fractal()
            ->collection($channel->get())
            ->transformWith(new ChannelTransformer)
            ->toArray();
    }

    /**
     * Get discussion by channel slug
     * 
     * @param  string  $slug
     * @param  Request $request
     * @return json
     */
    public function discussChannel($slug, Request $request)
    {
        $per_page = $request->input('per_page') ?: 20;
        $channel = Channel::where('slug', $slug)->first();

        if (!$channel) {
            return $this->respondNotFound("Channel does not found.");
        }

        $discuss = Discussion::where('channel_id', $channel->id)
                            ->with('user', 'channel')
                            ->orderBy('id', 'desc')
                            ->paginate($per_page);

        $fractal = fractal()
            ->collection($discuss)
            ->includeUser()
            ->includeChannel()
            ->transformWith(new DiscussionTransformer)
            ->paginateWith(new IlluminatePaginatorAdapter($discuss))
            ->toArray();

        return $this->respondSuccessWithData([
            'discuss' => $fractal['data'],
            'pagination' => $fractal['meta']['pagination'],
        ]);
    }

    /**
     * Get single discussion by discuss slug
     * 
     * @param  string  $slug
     * @param  Request $request
     * @return json
     */
    public function show($slug, Request $request)
    {
        $discuss        = Discussion::where('slug', $slug)->with('user', 'channel')->first();
        // $best_comment   = Comment::where('discussion_id', $discuss->id)
        //                         ->where('id', $discuss->best_comment_id)
        //                         ->with('user')->first();

        $discuss = fractal()
            ->item($discuss)
            ->includeUser()
            ->includeChannel()
            ->includeComments()
            ->transformWith(new DiscussionTransformer)
            ->toArray();

        return $this->respondSuccessWithData($discuss['data']);
    }
    
    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'title' => 'required|max:255|min:5',
            'body' => 'required|min:15',
            'channel' => 'required',
        ]);

        $user = JWTAuth::parseToken()->authenticate();
        $discussion = Discussion::where('title', $request->input('title'))->first();
        $channel = Channel::where('id', $request->input('channel'))->first();

        $validator->after(function($validator) use ($discussion, $user, $channel) {
            if ($discussion) {
                $validator->errors()->add('title', 'This topic is already discussed in the forum.');
            }
            if (!isset($user->id)) {
                $validator->errors()->add('auth', 'Please log in first to post your discussion on forum.');
            }
            if (!isset($channel->id)) {
                $validator->errors()->add('channel', 'Channel does not exist.');
            }
        });

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $discuss = Discussion::create([
            'title' => $request->input('title'),
            'body' => $request->input('body'),
            'slug' => str_slug($request->input('title'), "-"),
            'user_id' => $user->id,
            'channel_id' => $request->input('channel'),
            'is_approved' => true,
        ]);

        $discuss = fractal()
            ->item($discuss)
            ->includeUser()
            ->includeChannel()
            ->transformWith(new DiscussionTransformer)
            ->toArray();

        return $this->setStatusCode(201)
                    ->respondSuccessWithData($discuss['data']);
    }

    public function storeComment(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'comment' => 'required|min:10',
            'discuss_id' => 'required',
        ]);

        $user = JWTAuth::parseToken()->authenticate();

        $validator->after(function($validator) use ($user) {
            if (!isset($user->id)) {
                $validator->errors()->add('auth', 'Please log in first to post your comment on forum.');
            }
        });

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $comment = Comment::create([
            'discussion_id' => $request->input('discuss_id'),
            'content' => $request->input('comment'),
            'user_id' => $user->id,
            'approved' => true,
        ]);

        $discuss = Discussion::where('id', $request->input('discuss_id'))->first();
        $comments_count = (int) $discuss->comments_count;
        $discuss->comments_count = $comments_count + 1;
        $discuss->save();

        $fractal = fractal()
                    ->item($comment)
                    ->transformWith(new CommentTransformer)
                    ->toArray();

        return $this->setStatusCode(201)
                    ->respondSuccessWithData($fractal['data']);

    }

    /**
     * Filter discussion
     * 
     * @param  string $filter_by
     * @param  int $per_page
     * @return Illuminate\Http\Response
     */
    protected function filterDiscussion($filter_by, $per_page, $user)
    {
        $discuss = Discussion::with('user', 'channel')
                   ->orderBy('id', 'desc')
                   ->paginate($per_page);

        if ($filter_by == 'me')
        {
            $discuss = $this->myQuestions($per_page, $user);
        }

        if ($filter_by == 'contributed_to')
        {
            $discuss = $this->myParticipation($per_page, $user);
        }

        if ($filter_by == 'popular_this_week')
        {
            $discuss = $this->popularThisWeek($per_page);
        }

        if ($filter_by == 'popular_all_time')
        {
            $discuss = $this->popularAllTime($per_page);
        }

        if ($filter_by == 'answered')
        {
            $discuss = $this->answeredQuestions($per_page);
        }

        if ($filter_by == 'unanswered')
        {
            $discuss = $this->unansweredQuestions($per_page);
        }
        return $discuss;
    }

    /**
     * Get auth user questions
     * 
     * @param  int $per_page
     * @return Illuminate\Http\Response
     */
    protected function myQuestions($per_page, $user)
    {
        $discuss = Discussion::where('user_id', $user->id)
                   ->with('user', 'channel')
                   ->orderBy('id', 'desc')
                   ->paginate($per_page);
        return $discuss;
    }

    /**
     * Get questions by my perticipation
     * 
     * @param  int $per_page
     * @return Illuminate\Http\Response
     */
    protected function myParticipation($per_page, $user)
    {
        $comments = Comment::where('user_id', $user->id)
                            ->lists('discussion_id')
                            ->toArray();
        $discuss = Discussion::whereIn('id', $comments)
                                ->with('user', 'channel')
                                ->orderBy('id', 'desc')
                                ->paginate($per_page);
        return $discuss;
    }


    /**
     * Get popular questions of this week
     * 
     * @param  int $per_page
     * @return Illuminate\Http\Response
     */
    protected function popularThisWeek($per_page)
    {
        $discuss = Discussion::where('created_at', '>=', Carbon::now()->subWeeks(1))
                                ->with('user', 'channel')
                                ->orderBy('comments_count', 'desc')
                                ->paginate($per_page);

        return $discuss;
    }

    /**
     * Get popular questions of all time
     * 
     * @param  int $per_page
     * @return Illuminate\Http\Response
     */
    protected function popularAllTime($per_page)
    {
        $discuss = Discussion::with('user', 'channel')
                                ->orderBy('comments_count', 'desc')
                                ->paginate($per_page);
        return $discuss;
    }

    /**
     * Get answered questions
     * 
     * @param  int $per_page
     * @return Illuminate\Http\Response
     */
    protected function answeredQuestions($per_page)
    {
        $discuss = Discussion::where('comments_count', '>=', 1)
                                ->with('user', 'channel')
                                ->orderBy('id', 'desc')
                                ->paginate($per_page);
        return $discuss;
    }

    /**
     * Get unanswered questions
     * 
     * @param  int $per_page
     * @return Illuminate\Http\Response
     */
    protected function unansweredQuestions($per_page)
    {
        $discuss = Discussion::where('comments_count', 0)
                                ->with('user', 'channel')
                                ->orderBy('id', 'desc')
                                ->paginate($per_page);
        return $discuss;
    }

    protected function getAuthUser($request)
    {
        try {

            if (! $user = JWTAuth::parseToken()->authenticate())
            {
                return $this->respondNotFound("User not found");
            }

        } catch (TokenExpiredException $e)
        {
            return $this->setStatusCode($e->getStatusCode())
                        ->respondWithError("Token has been expired");

        } catch (TokenInvalidException $e)
        {
            return $this->setStatusCode($e->getStatusCode())
                        ->respondWithError("Token is invalid");

        } catch (JWTException $e)
        {
            return $this->setStatusCode($e->getStatusCode())
                        ->respondWithError("Token is absent");
        }

        // the token is valid and we have found the user via the sub claim
        return $user;
    }
}
