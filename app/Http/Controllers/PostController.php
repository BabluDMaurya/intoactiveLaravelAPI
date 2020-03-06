<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use App\Model\User;
use App\Model\Posts;
use App\Model\ImagePosts;
use App\Model\PostLikes;
use App\Model\PostBookmark;
use App\Model\PostComments;
use Illuminate\Support\Facades\Storage;
use App\Http\Traits\NotificationFollowing;
use App\Services\FcmTokenService;
use App\Services\PostService;

class PostController extends Controller
{
    use NotificationFollowing;
    private $postService;
    private $fcmTokenService;
    
    public function __construct(FcmTokenService $fcmTokenService, PostService $postService) {
        $this->fcmTokenService = $fcmTokenService;
        $this->postService = $postService;
    }

    public function uploadPost(Request $request) {
        try {
            $userData = Auth::User();
            $file = $request['file[]'];
            $description = $request['description'];
            
            if(count($file)>0 || $description ){
                $imgagePath = array();
                $imgPath = null;
                $thumbPathArr = array();
                $thPath = null;
                foreach ($file as $key => $f) {

                    $picture = 'image/user_'.$userData->id.'_'.date('YmdHis').'_'.$key .'.jpeg';
                   
                    $thumbPath = 'image/thumb/user_'.$userData->id.'_'.date('YmdHis').$key .'_'.'.jpeg';
                   // Storage::makeDirectory('public/image/user_'.$userData->id.'/thumbs/', 0755, true, true);
                    $imageParts = explode(";base64,", $f);
                    $imageTypeAux = explode("image/", $imageParts[0]);
                    $imageType = $imageTypeAux[1];
                    $imageBase64 = base64_decode($imageParts[1]);
                   // Storage::disk('local')->put($picture, $imageBase64);
                    file_put_contents($picture, $imageBase64);
                    $imageParts = null;
                    $imageTypeAux = null;
                    $imageType = null;
                    $imageBase64 = null;
                    $imgagePath[] = $picture;
                   // $this->compressImage(storage_path('app/'.$picture),storage_path('app/'.$thumbPath),55);
                    $this->compressImage($picture,$thumbPath,55);
                    $thumbPathArr [] = $thumbPath;
                }
                
                $imgPath = implode(',', $imgagePath);
                $thPath = implode(',' , $thumbPathArr);
                if(count($file)>0){
                    $postType = 1;
                }else{
                    $postType = 8;
                }
    //       
                DB::beginTransaction();
                $post = Posts::create([
                            'user_id' => $userData->id,
                            'post_type' => $postType,
                ]);

                ImagePosts::create([
                    'post_id' => $post->id,
                    'description' => $description,
                    'image_path' => $imgPath,
                     'thumb_path'=>$thPath
                ]);

                //--add post Notification to users--//
                
                $notificationData = [
                    'post_id'=>$post->id,
                    'user_id'=>$userData->id,
                    'post_type'=>$postType, 
                    'description'=>$description,
                    'following_uid'=>$this->followingId(),
                ];
                $this->saveNotification($notificationData);
                
                $notify = [
                    'user_ids'=> $this->followingId(),
                    'title'=> $userData->user_name.' '.config('constants.NOTIFICATION_POST_TITLE'),
                    'description'=>$description,
                    'moredata'=> 'more'
                ];
                $this->fcmTokenService->sendNotification($notify);

                //--add post Notification to users--//
                
                DB::commit();
                $status = 'successfully uploaded';
            }else{
                $status = 'Empty submission occur.';
            }
                
            return response()->json(['status' => $status ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    function compressImage($source, $destination, $quality) {

        $info = getimagesize($source);

        if ($info['mime'] == 'image/jpeg') {
          $image = imagecreatefromjpeg($source);
        }
      

        imagejpeg($image, $destination, $quality);

      }
      
      
    public function loadMyPost(Request $request) {
        try {
            if ($request['userId']) {
                $userData = User::findorfail($request['userId']);
            } else {
                $userData = Auth::User();
            }
          
            $post = $userData->posts(explode(',',$request['postType']))->paginate(10);
            $imgPost = $post->map(function ($ip) {
                $ip->postLikes;
                $ip->totalComments->count();
                $ip->postBookmarks;
                return $ip->imagePost;
            });
            return response()->json(['posts' => $post]);
        } catch (Exception $ex) {
            return response()->json(['message' => $ex->getMessage()], 500);
        }
    }
    
    public function getPostById(Request $request)
    {
        try{
             $postData = $this->postService->getPostById($request);
            return response()->json(['postData'=>$postData], 200);
        } catch(Exception $ex)
        {
            return response()->json(['message' => $ec->getMessage()], 500);            
        }
        
    }

    public function imageLike(Request $request) {
        try {
            $userData = Auth::User();

            $liked = $request['liked'];
            if ($liked) {
                $status = PostLikes::where([
                            ['user_id', '=', $userData->id],
                            ['post_id', '=', $request['postId']]
                                ]
                        )->delete();
                $status = "Unliked";
                // use to unlike post entry 
            } else {
                // use to like post entry 
                $status = PostLikes::updateOrCreate(
                                [
                            'user_id' => $userData->id,
                            'post_id' => $request['postId']
                                ], [
                            'user_id' => $userData->id,
                            'post_id' => $request['postId'],
                            'is_unliked' => '0'
                                ]
                );
                $status = "Liked";
            }

            return response()->json(['status' => $status]);
        } catch (Exception $ex) {
            return response()->json(['message' => $ex->getMessage()], 500);
        }
    }

    public function postBookmark(Request $request) {
        try {
                $userData = Auth::User();
                
                $bookmark = $request['bookmark'];
                if ($bookmark) {
                      // use to unbookmark post entry 
                    $status = PostBookmark::where([
                                ['user_id', '=', $userData->id],
                                ['post_id', '=', $request['postId']]
                                    ]
                            )->delete();
                    $status = "Removed from List";
              
            } else {
                // use to bookmark post entry 
                $status = PostBookmark::updateOrCreate(
                                [
                            'user_id' => $userData->id,
                            'post_id' => $request['postId']
                                ], [
                            'user_id' => $userData->id,
                            'post_id' => $request['postId'],
                            'is_delete' => '0'
                                ]
                );
                $status = "Added to bookmark";
            }

            return response()->json(['status' => $status]);
        } catch (Exception $ex) {
            return response()->json(['message' => $ex->getMessage()], 500);
        }
    }
    
     public function getBookmarkPost(Request $request) {
        try {
      
                $userData = Auth::User();
            

            $post = $userData->postBookmark; // from bookmark tbl
            
            $imgPost = $post->map(function ($ip) {
                // $ip->postUser;
                // $ip->postBookmarks;
                // $ip->posts
                 $abc = $ip->posts->postLikes;  
                  $ip->posts->postBookmarks;  
                    $ip->posts->totalComments->count();
                 $ab = $ip->posts->imagePost;
// from posts tbl
             return $ip->posts->postUser->bios; // from users and bios tbl
            });
            return response()->json(['posts' => $post]);
        } catch (Exception $ex) {
            return response()->json(['message' => $ex->getMessage()], 500);
        }
    }
    
    public function previewImg(Request $request) {
        if ($request['userId']) {
            $userData = User::findorfail($request['userId']);
        } else {
            $userData = Auth::User();
        }
        $imagePath = 3;

        while ($imagePath < 5) {
            echo $imagePath;
            $post = $userData->posts()->paginate(1);
            $imgPost = $post->map(function ($ip) {
                return $ip->imagePost;
            });
            print_r($imgPost['0']->id);
            $imagePath++;
        }
    }

    public function addComment(Request $request) {
        $userData = Auth::User();
        try {
            if (isset($request['replyToComment']) && $request['replyToComment'] != '') {
                $replyId = $request['replyToComment'];
            } else {
                $replyId = null;
            }
            DB::beginTransaction();
            $post = PostComments::create([
                        'post_id' => $request['postId'],
                        'user_id' => $userData->id,
                        'comment' => $request['comment'],
                        'reply_to' => $replyId,
            ]);
            
            //--add post Notification to users--//
                $cPost = Posts::where('id',$request['postId'])->first();
                if($userData->id != $cPost->user_id){
                
                $notificationData = [
                    'post_id'=>$request['postId'],
                    'user_id'=>$userData->id,
                    'post_type'=>9, 
                    'description'=>$request['comment'],
                    'following_uid'=>$cPost->user_id,
                ];
                $this->saveNotification($notificationData);
                
                $notify = [
                    'user_ids'=> $cPost->user_id,
                        'title'=> $userData->user_name.' '.config('constants.NOTIFICATION_COMMENT_TITLE'),
                    'description'=>$request['comment'],
                    'moredata'=> 'more'
                ];
                $this->fcmTokenService->sendNotification($notify);
                
                }
                //--add post Notification to users--//
            
            DB::commit();
            return response()->json(['status' => true]);
        } catch (Exception $ex) {
            return response()->json(['message' => $ex->getMessage()], 500);
        }
    }

    public function getComment(Request $request) {

        $postDetails = Posts::findOrFail($request['postId']);
        
        $comments = $postDetails->postComments;
        $totalComments = $postDetails->totalComments->count();
//        $comments = $postDetails->postComments->where('reply_to',$comments);
        $commentPost = $comments->map(function ($ip) {
            $ip->user;
            $ip->user->bios->first();
            $reply = $ip->commentReply;
             $reply->map(function ($reply) {
                $reply->user;
                return $reply->user->bios;
            });
        });


        return response()->json(['status' => $comments,'count'=>$totalComments]);
    }

    public function deleteComment(Request $request) {
        try {
            DB::beginTransaction();
            PostComments::where('id',$request['commentId'])->update(['comment_delete' => '1']);
            PostComments::where('reply_to',$request['commentId'])->update(['comment_delete' => '1']);
//            PostComments::where([
//                ['id', '=', $request['commentId']],
//                ['reply_to', '=', $request['commentId']]
//                    ]
//            )->update(['comment_delete' => '1']);
            DB::commit();
            return response()->json(['status' => true]);
        } catch (Exception $ex) {
            return response()->json(['message' => $ex->getMessage()], 500);
        }
    }
}
