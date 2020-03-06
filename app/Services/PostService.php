<?php

namespace App\Services;

use DB;
use Auth;
use App\Model\User;
use App\Model\Posts;
use App\Model\ImagePosts;
use App\Model\PostLikes;
use App\Model\PostBookmark;
use App\Model\PostComments;
use Illuminate\Support\Facades\Storage;

class PostService {

    public function getPostById($request) {
        try {
            $posts = Posts::where('id', $request['postId'])->first();
            $posts->postLikes;
            $posts->postBookmarks;

            $posts->totalComments->count();
            $posts->imagePost;
            $posts->postUser->bios;
            return $posts;
        } catch (\Exception $e) {
            return $e;
        }
    }

}

?>