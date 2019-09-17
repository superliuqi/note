<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Jobs\Order;
use App\Jobs\ProcessPodcast;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class IndexController extends Controller
{

    public function index(Request $request)
    {
//        $cityName = $request->cityName;
//        $result = file_get_contents('http://api.map.baidu.com/geocoder?address=' . $cityName . '&output=json&key=37492c0ee6f924cb5e934fa08c6b1676&city=%E5%8C%97%E4%BA%AC%E5%B8%82');
//        return $result;
		Redis::set('lq:name','liuqi',60);
    }


    public function delay()
    {

        $id = mt_rand(1, 100);
//        ProcessPodcast::dispatch(time())->delay(now()->addMinutes(mt_rand(1,3)));
//        Order::dispatch(['start_at'=>time(),'id'=>$id])->onQueue('order')->delay(now()->addMinutes(mt_rand(1,3)));
//        echo $id.'<br/>';
//        echo time();

        for ($i = 0; $i < 10; $i++) {
            Order::dispatch(['start_at' => time(), 'id' => $i])->onQueue('order')->delay(now()->addMinutes(1));
            sleep(1);
        }
    }


    public function tt()
    {
        echo today();
    }

    public function t1(Request $request)
    {
        DB::beginTransaction();
        $res = Test::where('id',121)->first();
        $update = Test::where(['id'=>121,'version'=>$res->version])->update(['kc'=>$res->kc - 1,'version'=>$res->version + 1,'param'=>$res->param .$request->url()]);
        DB::commit();
    }

    public function t2(Request $request)
    {
        DB::beginTransaction();
        $res = Test::where('id',121)->first();
        $update = Test::where(['id'=>121,'version'=>$res->version])->update(['kc'=>$res->kc - 1,'version'=>$res->version + 1,'param'=>$res->param .$request->url()]);
        DB::commit();
    }

}