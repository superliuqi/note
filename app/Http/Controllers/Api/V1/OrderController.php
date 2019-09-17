<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Jobs\Order;
use App\Jobs\ProcessPodcast;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class OrderController extends Controller
{
	public function index()
	{
	}

	public function addOrder()
	{
		$order = [
			'order_id'   => 'HD' . time() . rand(100000, 999999),
			'm_id'       => rand(1, 100),
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		];

		try{
			$orderList = 'order:temp:list';
			$result = Redis::lpush($orderList,json_encode($order));
			if($result){
				echo '下单成功';exit;
			}else{
				echo '下单是吧';exit;
			}
		}catch (\Exception $e){

		}
	}
}