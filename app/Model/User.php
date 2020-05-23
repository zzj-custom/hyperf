<?php

declare (strict_types=1);
namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 */
class User extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'name', 'head_url', 'ext_info', 'mobile'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    const CREATED_AT = 'create_date';

    const UPDATED_AT = 'update_date';

    public static function getUserInfoByMobile($mobile){
        $result = User::query()->where(['mobile' => $mobile])->first();
        if(empty($result)){
            return [];
        }else{
            return $result->toArray();
        }
    }

    public static function insertData($param){

        $model = new User($param);
        $model->save();
        return $model->id;
    }
}
