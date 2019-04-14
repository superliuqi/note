<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\GitLog;

class GitController extends Controller
{

    public function index() {
        $data = file_get_contents("php://input");
        $res = GitLog::create(['val'=>$data]);
    }

    public function test()
    {
        echo 'yes1';
    }
}