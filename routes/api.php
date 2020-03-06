<?php
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'prefix' => 'auth'
], function () {    
    Route::post('/home', 'HomeController@index');
    Route::post('register', 'AuthController@register');
    Route::get('checkEmailAvailability/{email}', 'AuthController@checkEmailAvaibility');
    Route::get('checkUnameAvailability/{uname}', 'AuthController@checkUnameAvaibility');    
    Route::post('checkOtp', 'AuthController@checkOtp');
    Route::post('userLogin', 'AuthController@userLogin')->name('login');    
    Route::post('resendOtp', 'AuthController@resendOtp');
    Route::post('forgotPassword', 'AuthController@forgotPassword');    
    Route::post('updatePassword', 'AuthController@updatePassword');
    Route::get('captchaHtml', 'AuthController@captchaHtml');
    Route::post('sendnotify','SendPushNotificationController@sendNotification');
    Route::post('pushNotificationSend','SendPushNotificationController@send'); 
    
    Route::post('dirPath','AddVideosController@createFolderForVideo');    
    Route::post('uploadPost','PostController@uploadPost');  
    Route::post('getUserData','PeopleViewController@getUserData'); 
    Route::post('uploadVideo','SettingsController@uploadVideo');
    //--------------This is the test router ---------------//
//    Route::get('email-test', function(){  
//	$details['email'] = 'bablu@yopmail.com';  
//        dispatch(new App\Jobs\SendEmailJob($details));                 
//    });  
    
//    Route::post('islogedin', 'SettingsController@islogedin');
    
    //--------------Test router list End---------------//
    
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::post('islogedin', function () {
            return  response()->json(TRUE, 200);
        });
        Route::get('follow','PostController@noti');
        Route::post('videoPost','AddVideosController@addVideo');
        Route::post('videoComplete','AddVideosController@updateVideo');
        Route::get('viewNotification','NotificationController@showNotification');
        Route::get('viewNotificationOfPeople','NotificationController@showNotificationOfFollower');
        
        Route::get('viewUnreadNotification','NotificationController@showUnreadNotification');
        Route::post('setReadNotification','NotificationController@setReadNotification');
        
        
        Route::post('addUserTokenFcm','UserFcmTokenControlle@store');        
        Route::post('resetPassword', 'SettingsController@resetPassword');
        Route::post('testt', 'AuthController@testt');        
        Route::post('contactAdmin', 'SettingsController@contactAdmin');
        Route::post('logOut', 'SettingsController@logOut');
        Route::post('deleteUser', 'SettingsController@deleteUser');        
        Route::post('currentTimeZone','SettingsController@currentTimeZone');
        Route::get('getProfileData','SettingsController@getProfileData');
        Route::post('editProfile','SettingsController@editProfile');
        Route::post('additionalInfo','SettingsController@additionalInfo');
        Route::post('uploadPic','SettingsController@uploadPic');
        Route::get('getCommonData','SettingsController@getCommonData');
        Route::post('getState','SettingsController@getState');
        Route::post('insertNutrition','NutritionController@insert');
        Route::post('getCity','SettingsController@getCity');
        Route::get('getMyProfileData','MyProfileController@getMyProfileData');
        Route::post('uploadPost','PostController@uploadPost');  
        Route::post('topTenPeople','SearchController@topTenPeople');
        Route::post('searchPeople','SearchController@searchPeople');
        Route::Post('searchRequest','SearchController@searchRequest');
        Route::get('loadMyPost', 'PostController@loadMyPost');
        Route::post('imageLike', 'PostController@imageLike');        
        Route::post('bookmark', 'PostController@postBookmark');    
        Route::post('getBookmarkPost', 'PostController@getBookmarkPost');

        Route::get('previewImg', 'PostControllerPostController@previewImg@previewImg');
        Route::post('addComment', 'PostController@addComment');
        Route::post('getComment', 'PostController@getComment');
        Route::post('deleteComment', 'PostController@deleteComment');
        Route::get('getProfileImage', 'MyProfileController@getProfileImage');
        Route::get('getFollowers', 'FollowUpsController@getFollowers');
        Route::get('getFollowings', 'FollowUpsController@getFollowings');
        Route::post('followUpPeople', 'FollowUpsController@followUpPeople');
        Route::post('getFolloFollowingResult', 'FollowUpsController@getFolloFollowingResult');
//        Route::post('newFollow', 'FollowUpsController@newFollow');
        Route::post('blockUser', 'PeopleViewController@blockUser');
        
         Route::post('getPostById', 'PostController@getPostById');
        
    });
});
