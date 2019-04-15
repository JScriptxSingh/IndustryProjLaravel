<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataController extends Controller {
    public function open() {
        $data = "This data is open and can be accessed without "
                ."the client being authenticated";
        return response()->json(compact('data'),200);
    }

    public function openUsers() {
        $users = DB::table('users')->get();
        $otherdata = 'hello';
        return response()->json(array('users'=>$users, 
                                      'otherdata'=>$otherdata));
    }

    public function closed() {
        $data = "Success. As a logged in user you are authorized this";
        return response()->json(compact('data'),200);
    }
}  
