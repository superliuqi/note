<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Jobs\Order;
use App\Jobs\ProcessPodcast;
use App\Models\Test;
use App\Models\TestOrder;
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

		try {
			$orderList = 'order:temp:list';
//			$result = Redis::lpush($orderList,json_encode($order));
			$result = Order::dispatch($order)->onQueue('order')->delay(now()->addMinutes(mt_rand(1, 3)));
			$key    = $orderList . ':' . $order['order_id'];
			Redis::set($key, json_encode($order));
			Redis::expire($key, 300);
			if ($result) {
				echo '下单成功';
				exit;
			} else {
				echo '下单是吧';
				exit;
			}
		} catch (\Exception $e) {

		}
	}

	public function pay(Request $request)
	{
		$order_id = $request->order_id;
		if (!$order_id) {
			exit('fail');
		}
		$redis_key = 'order:temp:list';

		$exists = Redis::get($redis_key . ':' . $order_id);
		if (!$exists) {
			exit('已过期');
		}
		$ex = TestOrder::where('order_id', $order_id)->first();
		if (!$ex) {
			exit('fail_2');
		}
		TestOrder::where('order_id', $order_id)->update(['status' => 1]);
		echo 'success';
	}
}