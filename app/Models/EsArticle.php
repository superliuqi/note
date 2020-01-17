<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/8
 * Time: 下午5:11
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;

/**
 * 文章
 * Class Article
 * @package App\Models
 */
class EsArticle extends Model
{
	use Searchable;
	//状态
	const STATUS_OFF = 0;
	const STATUS_ON = 1;

	const STATUS_DESC
		= [
			self::STATUS_ON  => '正常',
			self::STATUS_OFF => '待审'
		];

	protected $table = 'es_article';
	protected $guarded = ['id'];

	/**
	 * 获取详情
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function articleDesc()
	{
		return $this->hasOne('App\Models\ArticleDesc');
	}

	public function searchableAs()
	{
		return 'article';
	}

	public function toSearchableArray()
	{
		return [
			'title'   => $this->title,
			'content' => $this->content
		];
	}

}