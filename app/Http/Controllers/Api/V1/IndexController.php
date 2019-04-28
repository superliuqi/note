<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

class IndexController extends Controller
{

    public function index() {
        $result = [
            'code'=>0,
            'data'=>[
                'result'=>[
                    'location'=>[
                        'lng'=>'120',
                        'lat'=>'30'
                    ]
                ]
            ]
        ];

        echo json_encode($result);
    }
}