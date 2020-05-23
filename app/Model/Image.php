<?php
declare(strict_types=1);
namespace App\Model;
use Hyperf\DbConnection\Model\Model;
use Hyperf\DbConnection\Db;
class Image extends Model
{
    protected $table = 'image';

    /**
     * @var bool 默认情况下，Hyperf 预期你的数据表中存在 created_at 和 updated_at 。
     * 如果你不想让 Hyperf 自动管理这两个列， 请将模型中的 $timestamps 属性设置为 false：
     */
    public $timestamps = false;

    /**
     * 如果你需要自定义存储时间戳的字段名，可以在模型中设置 CREATED_AT 和 UPDATED_AT 常量的值来实现
     */
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'last_update';

    /**
     * @var string 默认情况下，Hyperf 模型将使用你的应用程序配置的默认数据库连接 default。如果你想为模型指定一个不同的连接，
     * 设置 $connection 属性：当然，connection-name 作为 key，必须在 databases.php 配置文件中存在。
     */
    protected $connection = 'default';

    /**
     * @var array 如果要为模型的某些属性定义默认值，可以在模型上定义 $attributes 属性
     */
    protected $attributes = [
        'delayed' => false,
    ];

    public function index(){

    }
}

