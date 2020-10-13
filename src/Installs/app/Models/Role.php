<?php
/**
 * Model generated using IdeaGroup
 * Help: lehung.hut@gmail.com
 * CrmAdmin is open-sourced software licensed under the MIT license.
 * Developed by: Lehungdev IT Solutions
 * Developer Website: http://rellifetech.com
 */

namespace App;

// use Shanmuga\LaravelEntrust\EntrustRole;
// use Illuminate\Database\Eloquent\SoftDeletes;

// class Role extends EntrustRole
// {
//     use SoftDeletes;

// 	protected $table = 'roles';

// 	protected $hidden = [

//     ];

// 	protected $guarded = [];

// 	protected $dates = ['deleted_at'];
// }

use Shanmuga\LaravelEntrust\Models;

use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;
use Shanmuga\LaravelEntrust\Traits\LaravelEntrustRoleTrait;
use Shanmuga\LaravelEntrust\Contracts\LaravelEntrustRoleInterface;

class Role extends Model
{
    use LaravelEntrustRoleTrait;

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
        $this->table = Config::get('entrust.tables.roles');
    }
}
