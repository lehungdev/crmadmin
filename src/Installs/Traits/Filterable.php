<?php

namespace App\Traits;

use DB;

trait Filterable
{
    public function scopeFilter($query, $request)
    {
        $param = $request->all();
        if (count($param)) {
            $table_id = DB::table('modules')->where('name_db', $this->table)->value('id');
            foreach ($param as $field => $value) {
                if ($value === '') {
                    continue;
                } else if($field != 'limit' and $field != 'page'  and $field != 'include' ) {
                    $module_field  = DB::table('module_fields')->where('colname', $field)->where('module', $table_id)->select(['colname', 'field_type'])->first();
                    if(!empty($module_field)){
                        $module_field->value = $value;
                        $query = $this->buildQueryFilter($query, $module_field);
                    }
                }
            }
        }
        return $query;
    }

    /*
     * Get get Search Filter (Duyệt điều kiện where Select query)
     * @param   $module_field
     * @param   $query
     * */

    public static function buildQueryFilter($query, $module_field)
    {
        switch ($module_field -> field_type ) {

            case 7:
                $value = explode(',', $module_field -> value);
                $query = $query->whereIn($module_field -> colname, $value );
                break;

            case 15:
                $value = explode(',', $module_field -> value);
                $colname = $module_field  -> colname;
                $query = $query->where(function($query) use ($value, $colname) {
                    foreach ($value as $key => $value) {
                        if ($key == 0) {
                            $query      = $query->where($key, 'LIKE', '%"' . $value . '"%');
                        } else  $query      = $query->orWhere($key, 'LIKE', '%"' . $value . '"%');
                    }
                });
                break;
            case 1:
            case 3:
            case 6:
            case 8:
            case 14:
            case 16:
            case 19:
            case 21:
            case 22:
            case 23:
            case 25:
            case 26:
                $value =  $module_field -> value;
                $query = $query->where($module_field -> colname, 'LIKE', '%' . $value . '%');
                break;
            default:
                if ($module_field -> colname != 'limit' and $module_field -> colname != 'page') {
                    $value =  $module_field -> value;
                    $query = $query->where($module_field -> colname, $value);
                }
                break;
        }
        return $query;
    }

}
