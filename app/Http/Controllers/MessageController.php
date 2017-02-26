<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Message;

class MessageController extends Controller
{
  public function __construct()//no access without being logged in
  {
    $this->middleware('auth');//->except(['index','show']);
  }

  public function show()
  {
    return view('/dashboard.messenger');
  }

  public function update()
  {

  }

  public function index()
  {
    $messages = Message::all();
    return view('/dashboard.get-messages',compact('messages'));
  }
}
