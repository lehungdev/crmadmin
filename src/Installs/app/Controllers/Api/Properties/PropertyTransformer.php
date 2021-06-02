<?php
/**
 * Model generated using IdeaGroup
 * Help: lehung.hut@gmail.com
 * CrmAdmin is open-sourced software licensed under the MIT license.
 * Developed by: Lehungdev IT Solutions
 * Developer Website: http://rellifetech.com
 */

namespace App\Http\Controllers\Api\Properties;

use League\Fractal\TransformerAbstract;
use Lehungdev\Crmadmin\Models\Module;
use App\Http\Controllers\Api\Users\UserTransformer;

use App\Http\Controllers\Api\Categories\CategoryTransformer;

use App\Models\Property;


class PropertyTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [];
    protected $availableIncludes = ['user', 'category'];
    protected $fields = ['id', 'name', 'description', 'value', 'unit', 'type_data', 'filter', 'show_colum', 'user_id', 'updated_at'];

    public function transform($results)
    {
        $module = Module::get('Properties');
        $myNewArray = array_combine($this -> fields, array_map(function($field) use ($results, $module) {
            if(json_decode($results[$field], true)){
                $value = json_decode($results[$field], true);
                if(!empty($module->fields[$field])){
                    if(!empty($module->fields[$field]['lang_active'])){
                        $value = $value[config('app.locale_id')];
                    }
                }
            }
            else $value = $results[$field];
            return  $value;
        }, $this -> fields));
        return $myNewArray;
    }

    /* Model belongsTo */

	public function includeUser(Property $properties)
	{
		if (!is_null($properties->user)) {
			return $this->item($properties->user, new UserTransformer);
		}

	}


    /* Model belongsTo children*/

	public function includeCategory(Property $properties)
	{
		if (!is_null($properties->category)){
			return $this->collection($properties->category, new CategoryTransformer);
		}

	}
    //Add_belongsTo_parent
}
