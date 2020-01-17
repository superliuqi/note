<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Console\Command;

class EsInit extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'es:init';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = '初始化es的模版';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$client = new Client();
		$url    = config('scout.elasticsearch.hosts')[0] . '/_template/tmp';
//		$client->delete($url);
		$param = [
			'json' => [
				'template' => config('scout.elasticsearch.index'),
				'mappings' => [
					'_default_' => [
						'dynamic_templates' => [
							[
								'string' => [
									'match_mapping_type' => 'string',
									'mapping'            => [
										'type'     => 'text',
										'analyzer' => 'ik_smart',
										'field'    => [
											'keyword' => [
												'type' => 'keyword'
											]
										]
									]
								]
							]
						]
					]
				]
			]
		];
		$client->put($url, $param);
		$this->info('-----模版创建成功-----');

		$url = config('scout.elasticsearch.hosts')[0] . '/' . config('scout.elasticsearch.index');
//		$client->delete($url);
		$param = [
			'json' => [
				'settings' => [
					'refresh_interval'   => '5s',
					'number_of_shards'   => 1,
					'number_of_replicas' => 0
				],
				'mappings' => [
					'_default_' => [
						'_all' => [
							'enabled' => false
						]
					]
				]
			]
		];
		$client->put($url, $param);
		$this->info('-----创建索引成功-----');
	}
}
