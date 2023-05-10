<?php

namespace App\Modules\Comment\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\User\Models\User;
use App\Modules\Damage\Models\Damage;
use App\Modules\Comment\Models\Photo;
use App\Modules\Comment\Models\Comment;
use Illuminate\Support\Facades\Validator;

use App\Libs\UploadTrait;

class CommentController extends Controller
{
    use UploadTrait;

    public function index($id){
        $damage=Damage::find($id);
        if(!$damage){
            return [
                "payload"=>"damage is not exist !",
                "status"=>"damage_404",
            ];
        }
        $comments=Comment::with('user')
        ->with('photos')
        ->where("damage_id",$id)
        ->get();

        return [
            "payload" => $comments,
            "status" => "200"
        ];
    }

    public function get($id){
        $comment=Comment::find($id);
        if(!$comment){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_1"
            ];
        }
        else {
            $comment->damage=$comment->damage;
            $comment->photos=$comment->photos;
            $comment->user=$comment->user;
            return [
                "payload" => $comment,
                "status" => "200_1"
            ];
        }
    }

    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            "comment" => "required",
            "damage_id" => "required",
            "user_id" => "required",
        ]);
        if ($validator->fails()) {
            return [
                "payload" => $validator->errors(),
                "status" => "406_2"
            ];
        }
        $damage=Damage::find($request->damage_id);
        if(!$damage){
            return [
                "payload"=>"damage is not exist !",
                "status"=>"damage_404",
            ];
        }
        $user=User::find($request->user_id);
        if(!$user){
            return [
                "payload"=>"user is not exist !",
                "status"=>"user_404",
            ];
        }
        $comment=Comment::make($request->all());
        $comment->save();
        $comment->damage=$comment->damage;
        $comment->user=$comment->user;
        if($request->file()) {
            for ($i=0;$i<count($request->photos);$i++){
                $file=$request->photos[$i];
                $filename=time()."_".$file->getClientOriginalName();
                $this->uploadOne($file, config('cdn.damagePhotos.path'),$filename);
                $photo=new Photo();
                $photo->filename=$filename;
                $photo->comment_id=$comment->id;
                $photo->save();
            }
        }
        $comment->photos=$comment->photos;
        return [
            "payload" => $comment,
            "status" => "200"
        ];
    }

    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            "comment" => "required",
            "damage_id" => "required",
            "user_id" => "required",
        ]);
        if ($validator->fails()) {
            return [
                "payload" => $validator->errors(),
                "status" => "406_2"
            ];
        }
        $damage=Damage::find($request->damage_id);
        if(!$damage){
            return [
                "payload"=>"damage is not exist !",
                "status"=>"damage_404",
            ];
        }
        $user=User::find($request->user_id);
        if(!$user){
            return [
                "payload"=>"user is not exist !",
                "status"=>"user_404",
            ];
        }
        $comment=Comment::find($request->id);
        if(!$comment){
            return [
                "payload"=>"comment is not exist !",
                "status"=>"user_404",
            ];
        }
        $comment->status=$comment->status;
        $comment->comment=$request->comment;
        $comment->save();
        $comment->damage=$comment->damage;
        $comment->user=$comment->user;
        $comment->photos=$comment->photos;
        return [
            "payload" => $comment,
            "status" => "200"
        ];
    }
    
    public function delete(Request $request){
        $validator = Validator::make($request->all(), [
            "id" => "required",
        ]);
        if ($validator->fails()) {
            return [
                "payload" => $validator->errors(),
                "status" => "406_2"
            ];
        }
        $comment=Comment::find($request->id);
        if(!$comment){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_4"
            ];
        }
        else {
            $comment->delete();
            return [
                "payload" => "Deleted successfully",
                "status" => "200_4"
            ];
        }
    }

    public function addPhotos(Request $request){
        $validator = Validator::make($request->all(), [
            "comment_id" => "required",
        ]);
        if ($validator->fails()) {
            return [
                "payload" => $validator->errors(),
                "status" => "406_2"
            ];
        }
        $comment=Comment::find($request->comment_id);
        if(!$comment){
            return [
                "payload"=>"comment is not exist !",
                "status"=>"user_404",
            ];
        }
        if($request->file()) {
            for ($i=0;$i<count($request->photos);$i++){
                $file=$request->photos[$i];
                $filename=time()."_".$file->getClientOriginalName();
                $this->uploadOne($file, config('cdn.damagePhotos.path'),$filename);
                $photo=new Photo();
                $photo->filename=$filename;
                $photo->comment_id=$comment->id;
                $photo->save();
            }
        }
        $comment->photos=$comment->photos;
        return [
            "payload"=>$comment->photos,
            "status"=>"200",
        ];
    }

    public function deletePhoto(Request $request){
        $validator = Validator::make($request->all(), [
            "id" => "required",
        ]);
        if ($validator->fails()) {
            return [
                "payload" => $validator->errors(),
                "status" => "406_2"
            ];
        }
        $photo=Photo::find($request->id);
        if(!$photo){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_4"
            ];
        }
        else {
            
            $this->deleteOne(config('cdn.damagePhotos.path'),$photo->filename);
            $photo->delete();

            $comment=Comment::find($request->comment_id);


            return [
                "payload" => $comment->photos,
                "status Delete" => "Deleted successfully",
                "status" => "200_4"
            ];
        }
    }
    public function sendDamagePhotosStoragePath(){
        return [
            "payload" => asset("/storage/cdn/damagePhotos/"),
            "status" => "200_1"
        ];
    }
}
