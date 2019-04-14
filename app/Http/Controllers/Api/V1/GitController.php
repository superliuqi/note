<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

class GitController extends Controller
{

    public function index() {
        file_put_contents('./git.txt', file_get_contents("php://input"));
    }

    public function test()
    {
        echo 'yes';
    }
}