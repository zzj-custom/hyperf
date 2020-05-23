<?php

declare (strict_types=1);
namespace App\Model;

use Hyperf\DbConnection\Model\Model;
/**
 * @property int $id
 * @property int $title_id
 * @property string $information
 * @property int $agree
 * @property int $content
 * @property string $title
 * @property string $user_name
 * @property string $avatar
 * @property string $article_img
 * @property string $detail
 * @property string $type
 * @property string $create_dt
 * @property string $update_dt
 */
class Article extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'article';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'title_id' => 'integer', 'agree' => 'integer', 'content' => 'integer'];

    public static function getArticle($page, $page_size){
        return self::query()->skip(($page - 1) * $page_size)->limit($page_size)->get()->toArray();
    }
}
