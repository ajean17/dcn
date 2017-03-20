<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use \Storage;

class ParseController extends Controller
{
  //***Find a way to place all parser content within this controller***//
  public function friend()
  {
    //dd(request('type'));
    return view('/phpParsers.friendSystem');
  }

  public function block()
  {
    //dd(request('type'));
    return view('/phpParsers.blockSystem');
  }

  public function message()
  {
    //dd(request('msg'));
    return view('phpParsers.messageSystem');
  }

  public function search()
  {
    return view('phpParsers.searchSystem');
  }

  public function password()
  {
    return view('phpParsers.passwordSystem');
  }

  public function photoHandle(User $User, Request $request)
  {
    //Pull the request object named avatar, and assign its original filename to a variable
    $file = $request->file('avatar');
    $fileName = $file->getClientOriginalName();
    $userName = $User->name;
    //Grab the allowed file types and max file size from the config.app file keys
    $allowedFileTypes = config('app.allowedFileTypes');
    $maxFileSize = config('app.maxFileSize');
    //Assign the validation rules and run the command
    $rules = [
      'avatar' => 'required|mimes:'.$allowedFileTypes.'|max:'.$maxFileSize
    ];
    $this->validate($request,$rules);
    //Grab the destination path variable from the config.app file key
    $destinationPath = config('app.fileDestinationPath').'/'.$userName.'/images'.'/'.$fileName;
    //Move the uploaded file from the temporary location to the folder of choice
    $moveResult = Storage::put($destinationPath, file_get_contents($file->getRealPath()));

    if($User->avatar != NULL)
    //If the user already has an avatar, delete it and replace with the uploaded file
    {
      //delete current photo in its place
      Storage::delete(config('app.fileDestinationPath').'/'.$userName.'/images'.'/'.$User->avatar);
    }
    User::where('name','=',$userName)->update(Array('avatar' => $fileName));
    //Back to the profile page
    return redirect()->to('/profile'.'/'.$userName);
  }
}
