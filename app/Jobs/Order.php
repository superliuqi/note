<?php

namespace App\Jobs;

use App\Models\Test;
use App\Models\TestOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class Order implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public $arr = [];

	public function __construct($arr)
	{
		$this->arr = $arr;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
//        Test::create(['param'=>'order id :'.$this->arr['id'] .' start at:'.date('Y-m-d H:i:s',$this->arr['start_at']).'work at:'.date('Y-m-d H:i:s')]);

		$order = [
			'order_id'   => $this->arr['order_id'],
			'm_id'       => $this->arr['m_id'],
			'created_at' => $this->arr['created_at'],
			'updated_at' => $this->arr['updated_at']
		];
		TestOrder::create($order);
	}
}
