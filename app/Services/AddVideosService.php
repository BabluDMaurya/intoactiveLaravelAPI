<?php

namespace App\Services;

use DB;
use Mail;
use Auth;
use File;
use Illuminate\Support\Facades\Storage;
use App\Model\AddVideo;
use App\Model\Posts;

class AddVideosService {

    private $ffmpag;
    private $uploadPath;
    private $thumb;
    private $audio;
    private $video;

    public function __construct() {
        $this->ffmpeg = config('constants.FFMPAGE_PATH');
    }

    public function videoUpdate($request) {
        DB::beginTransaction();
        try {
            AddVideo::where('id', $request->add_videos_id)->update([
                'video_type' => $request->video_type,
                'thumb_path' => $request->thumb_path,
                'users_id' => $request->users_id,
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function videoStore($request) {

        DB::beginTransaction();
        $userData = Auth::User();
        try {
            if ($request->hasFile('video')) {
                $videoFrame = $this->ffmpageVideoThumbnail($request);
                $videoFile = $this->ffmpageVideoAndAudioMap($request);
                $post = Posts::create([
                            'user_id' => $userData->id,
                            'post_type' => '2',
                ]);
                AddVideo::create([
                    'posts_id' => $post->id,
                    'title' => $request->title,
                    'description' => $request->description,
                    'video_path' => $videoFile,
                    'thumb_path' => $videoFrame['vFrame'],
                    'duration' => $videoFrame['duration']
                ]);
                DB::commit();
                return response()->json(['status' => 'success'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function createFolderForVideo() {
        try {
            $user = Auth::user();
            $this->uploadPath = config('constants.VIDEO_UPLOAD_PATH') . '/user_' . $user->id;
            if (!File::exists($this->uploadPath)) {
                Storage::makeDirectory($this->uploadPath, 0755, true, true);
            }
            if (!File::exists($this->uploadPath . '/thumb')) {
                Storage::makeDirectory($this->uploadPath . '/thumb', 0755, true, true);
                $this->thumb = storage_path('app/' . $this->uploadPath . '/thumb/');
            }
            if (!File::exists($this->uploadPath . '/audio')) {
                Storage::makeDirectory($this->uploadPath . '/audio', 0755, true, true);
                $this->audio = storage_path('app/' . $this->uploadPath . '/audio/');
            }
            if (!File::exists($this->uploadPath . '/video')) {
                Storage::makeDirectory($this->uploadPath . '/video', 0755, true, true);
                $this->video = storage_path('app/' . $this->uploadPath . '/video/');
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function ffmpageVideoAndAudioMap($request) {
        try {
            // Create the folder
            $this->createFolderForVideo();

            $file = $request->file('video');

            // Recived file name without extension
            $withoutExtensionFileName = substr($file->getClientOriginalName(), 0, strrpos($file->getClientOriginalName(), "."));

            // create the new music file path
            $videoMusic = $this->audio . $withoutExtensionFileName . '.mp3';

            // create the new mearge audio path
            $mergeAudio = $this->audio . "merge.mp3";

            //create the new video path without the audio of uploaded video 
            $finalVideoWithoutAudio = $this->video . "videofinalwithoutaudio.mp4";

            //create the final video path
            $finalVideo = $this->video . "into_Bob_video_" . date('YmdHis') . "_final.mp4";

            // get the music file from music library
            $musicFile = storage_path('app/public/') . 'audio1.mp3';

            // create the new audio file of uploaded video music
            shell_exec("$this->ffmpeg -i " . $file . "  $videoMusic");

            //create the new video without the audio
            shell_exec("$this->ffmpeg -i $file -an $finalVideoWithoutAudio");

            if ($request->volume == 50) {

                //create the new music file merging of videomusic and selected music file audio
                shell_exec("$this->ffmpeg -i  $videoMusic -i $musicFile -filter_complex amix=inputs=2:duration=longest $mergeAudio");

                //create the new video after the merging the merge audio file
                shell_exec("$this->ffmpeg -i $finalVideoWithoutAudio -i $mergeAudio $finalVideo");
            } else {
                //create the new video after the merging the merge audio file
                shell_exec("$this->ffmpeg -i $finalVideoWithoutAudio -i $musicFile $finalVideo");
            }
            File::delete($mergeAudio, $videoMusic, $finalVideoWithoutAudio);
            return $finalVideo;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function ffmpageVideoThumbnail($request) {
        try {
            // Create the folder
            $this->createFolderForVideo();

            $size = "300X200";
            $file = $request->file('video');

            // Get the number of interval
            $output = $this->numberOfSecondGetTheVideoThumbnil($file);
            $interval = $output['interval'];
            $array_thumb = [];
            for ($num = 1; $num <= 5; $num++) {
                $imagefile = "into_Bob_img_" . $num . "_" . date('YmdHis') . ".jpg";
                $targetimagpath = $this->thumb . $imagefile;
                shell_exec("$this->ffmpeg -i $file -an -ss $interval -s $size $targetimagpath");
                array_push($array_thumb, $imagefile);
            }
            return $result = [
                'vFrame' => implode(',', $array_thumb),
                'duration' => $output['duration']
            ];
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function numberOfSecondGetTheVideoThumbnil($file) {
        try {
            // get the video duration
            $fileDuration = shell_exec("$this->ffmpeg -i $file 2>&1 | grep Duration | cut -d ' ' -f 4 | sed s/,//");
            preg_match("/(.{2}):(.{2}):(.{2})/", $fileDuration, $duration);
            if (!isset($duration[1])) {
                return false;
            }
            $hours = $duration[1];
            $minutes = $duration[2];
            $seconds = $duration[3];
            //----Get the thumbnil after the number of second from video--//
            $interval = round(($seconds + ($minutes * 60) + ($hours * 60 * 60)) / 5);
            $duration = ($seconds + ($minutes * 60) + ($hours * 60 * 60));
            return $result = [
                'interval' => $interval,
                'duration' => $duration
            ];
        } catch (\Exception $e) {
            throw $e;
        }
    }

}
