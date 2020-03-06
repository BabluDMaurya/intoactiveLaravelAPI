<?php

namespace App\Http\Controllers;

use App\Model\AddVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class AddVideoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function videoStore(Request $request){
        
        if ($request->hasFile('video')) {      
            $request->file('video')->storeAs('public/profile_pic/', date('His').'bablu.mp4');
            return response()->json(['status' => 'success'], 200);
        }else{
            return response()->json(['status' => 'fail'], 500);
        }
        
//        $this->userData = Auth::user();
////         try{
//        if ($request->hasFile('file')) {
//            $this->field_name = $request->channel;
//            $this->fileName = 'intoactive';
//            $this->picture = date('His') . '-' . $this->fileName . '.jpg';
//            if ($this->field_name == 'profile_pic') {
//                $request->file('file')->storeAs('public/profile_pic/'.$this->userData->id, $this->picture);
//            } else if ($this->field_name == 'profile_background_image') {
//                $request->file('file')->storeAs('public/profile_background_image/'.$this->userData->id, $this->picture);
//            }
//            $this->userId = Bio::where('user_id', $this->userData->id )->first();
//            DB::beginTransaction();
//            $this->userId->update([$this->field_name => $this->picture]);
//            DB::commit();
//            return response()->json(['status' => 'success'], 200);
//        }
//       } catch (Exception $e){
//           return response()->json(['message' => $e->getMessage()], 500);
//       }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AddVideo  $addVideo
     * @return \Illuminate\Http\Response
     */
    public function show(AddVideo $addVideo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AddVideo  $addVideo
     * @return \Illuminate\Http\Response
     */
    public function edit(AddVideo $addVideo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AddVideo  $addVideo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AddVideo $addVideo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AddVideo  $addVideo
     * @return \Illuminate\Http\Response
     */
    public function destroy(AddVideo $addVideo)
    {
        //
    }
}
