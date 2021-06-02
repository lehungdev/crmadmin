<?php
/**
 * Model generated using IdeaGroup
 * Help: lehung.hut@gmail.com
 * CrmAdmin is open-sourced software licensed under the MIT license.
 * Developed by: Lehungdev IT Solutions
 * Developer Website: http://rellifetech.com
 */

namespace App;

// use Shanmuga\LaravelEntrust\EntrustPermission;
// use Illuminate\Database\Eloquent\SoftDeletes;

// class Permission extends EntrustPermission
// {
//     use SoftDeletes;

// 	protected $table = 'permissions';

// 	protected $hidden = [

//     ];

// 	protected $guarded = [];

// 	protected $dates = ['deleted_at'];
// }


use Shanmuga\LaravelEntrust\Models;

use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;
use Shanmuga\LaravelEntrust\Traits\LaravelEntrustPermissionTrait;
use Shanmuga\LaravelEntrust\Contracts\LaravelEntrustPermissionInterface;

class Permission extends Model implements LaravelEntrustPermissionInterface
{
    use LaravelEntrustPermissionTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table;

    /**
     * Creates a new instance of the model.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = Config::get('entrust.tables.permissions');
    }
}
