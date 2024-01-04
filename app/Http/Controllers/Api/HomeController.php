<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class HomeController extends Controller
{
    public function getDataHome(){
        $files = DB::table('files')->orderBy('id', 'desc')->get();
        return [
            'files' => $files
        ];
    }
}
