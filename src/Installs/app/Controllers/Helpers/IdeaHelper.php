<?php

namespace App\Http\Controllers\Helpers;

use DB;
use Log;
use Dwij\Laraadmin\Models\Module;
use Dwij\Laraadmin\Models\ModuleFields;
use App\Models\Category;
use App\Models\Categories_article;
use App\Models\Upload;
use App\Models\Language;
// use Stichoza\GoogleTranslate\TranslateClient;
use Stichoza\GoogleTranslate\GoogleTranslate;
class IdeaHelper
{

    // IdeaHelper::print_cat_menu_editor($menu)
    /**
     * @param $menu
     * @param string $url
     * @param array $language
     * @param array $cat_all
     * @param array $module_tables
     * @param null $module_table_id
     * @return mixed
     */
    public static function print_menu_editor($menu, $url = 'categories', $cat_all = array(), $module_tables = array(), $module_table_id = null) {

        $listing_cols = ModuleFields::getModuleFields('Categories');

        //Data langgue
        $menu_lang = array_filter($cat_all, function($cat_item) use ($menu,  $module_table_id) {
            if($cat_item['local_parent'] == $menu['id'] or $cat_item['id'] ==  $menu['id']){
                return $cat_item;
            }
        });

        //Lọc dữ liệu cần theo ModuleFields
        foreach ($menu_lang as $key => $value){
            $value_true = array();
            foreach ($listing_cols as $key_fields => $module_fields_item){
                //Xử lý lấy ảnh khi type = image
                if(!empty($value[$key_fields]) and !empty($module_fields_item) and $module_fields_item->field_type == 12){
                    $value_true[$key_fields] = $value[$key_fields];
                    $value_true[$key_fields.'_img'] = IdeaHelper::pathImage($value[$key_fields], '50x50');
                } else if(!empty($module_fields_item))
                    $value_true[$key_fields] = $value[$key_fields];
            }
            $value_true['id'] = $value['id'];
            if(count($menu_lang) == 1)
                $value_true['local_parent'] = $value['id'];
                $value_true['slug'] = null;
            $menu_lang_locale[$value_true['locale']] = $value_true;
            unset($cat_all[$key]);
        }
        //Lấy danh sách nhóm con
        $childrens = array_filter($cat_all, function($cat_item) use ($menu, $module_table_id) {
            if($cat_item['parent'] == $menu['id'] and !empty($module_table_id) and $cat_item['module_table_id'] ==  $module_table_id){
                return $cat_item;
            } else if($cat_item['parent'] == $menu['id']){
                return $cat_item;
            }
        });

        //Xử lý url cat table
        if(!empty($module_tables)){
            if(  $module_tables[$menu['module_table_id']] == 'real_estates'){
                $module_table_url  = $module_tables[$menu['module_table_id']].'/' . $menu['id'];
            }
            else {
                if(count($childrens) > 0 ||  $menu['module_table_id'] == null) {
                    $module_table_url  = $module_tables[$menu['module_table_id']];
                }
                else $module_table_url  = $module_tables[$menu['module_table_id']].'/'. $menu['id'];

            }
        } else {
            if($menu['module_table_id'] == 1 )
                $module_table_url  = $url.'/' . $menu['id'];
            else {
                if($url == 'categories')
                    $module_table_url  = $url;
                else $module_table_url  = $url.'/' . $menu['id'];
            }
        }

        //////////////////////////////////////
        $name_cat = !empty($menu_lang_locale[config('app.locale_id')])? $menu_lang_locale[config('app.locale_id')]['name']: $menu['name'];
        if(count($childrens) > 0)
            $editing1 = '<a id="display_sub'.$menu['id'].'" class="display_sub btn-success btn pull-right" style="display: block;"><i class="fa fa-minus"></i></a>';
        else $editing1 = '';


        $editing = \Collective\Html\FormFacade::open(['route' => [config('laraadmin.adminRoute').'.categories.destroy', $menu['id']], 'method' => 'delete', 'style'=>'display:inline']);
        $editing .= $editing1;
        $editing .= '<button class="btn btn-xs btn-danger pull-right"><i class="fa fa-times"></i></button>';
        $editing .= '<a menu_id="'.$menu['id'].'" status="2" class="addModuleMenu btn btn-danger pull-right"><i class="fa fa-minus"></i></a>';
        $editing .= \Collective\Html\FormFacade::close();

        $editing .= '<a class="editMenuBtn btn btn-xs btn-success pull-right" menu_id="'.$menu['id'].'" info=\''.json_encode($menu_lang_locale, JSON_UNESCAPED_UNICODE ).'\'><i class="fa fa-edit"></i></a>';



        $str = '<li class="dd-item dd3-item" data-id="'.$menu['id'].'">
			        <div class="dd-handle dd3-handle"></div>
			        <div class="dd3-content"><a href="' . url(config('laraadmin.adminRoute') . '/'.$module_table_url) . '" ><i class="fa '.$menu['icon'].'"></i> '.$name_cat.' '.$editing.'</a></div>'; //url(config('ideaadmin.adminRoute') . '/categories/' . $menu['id'])

            if(count($childrens) > 0) {
                $str .= '<ol class="dd-list" style="display: block;">';
                    foreach($childrens as $children) {
                        if($children['locale'] == config('app.locale_id') ){
                             $menu_show1 = IdeaHelper::print_menu_editor($children, 'categories', $cat_all, $module_tables);
                            $str .= $menu_show1['string'];
                        }
                    }
                $str .= '</ol>';
            }
        $str .= '</li>';

        $menu_show['string'] = $str;
        $menu_show['catAll'] = $cat_all;
        return $menu_show;
    }

    // Get value address, latitude, longitude

    public static function getValueAddress($address_array, $load_map = false){

        if (empty($address_array['address'])) return false;

        $address        = $address_array['address'];
//        dd($address);
        $address        =  str_replace(' - ', ', ', $address);
        $address        =  str_replace(' -', ', ', $address);
        $address        =  str_replace('-', ', ', $address);
        $address        =  str_replace(', ', ',', $address);
        $address        =  str_replace(',', ', ', ucwords($address));
        $address        =  str_replace(' Phố ', '+', $address);
        if(!empty($address_array['country_id']))
            $country_id     = $address_array['country_id'];
        if(!empty($address_array['province_id']))
            $province_id    = $address_array['province_id'];
        if(!empty($address_array['district_id']))
            $district_id    = $address_array['district_id'];
        if(!empty($address_array['ward_id']))
            $ward_id        = $address_array['ward_id'];

        $data = array();

        $data['country_name']   = '';
        $country_name           = '';
        $data['province_name']  = '';
        $province_name          = '';
        $data['district_name']  = '';
        $district_name          = '';
        $data['ward_name']      = '';
        $ward_name              = '';

        if(!empty($country_id)) {
            $data['country_name'] = \App\Models\Country::find($country_id)->name;
        }

        if(!empty($province_id)) {
            $data['province_name'] = \App\Models\Province::find($province_id)->name;
        }

        if(!empty($district_id)) {
            $data['district_name'] = \App\Models\District::find($district_id)->name;
        }



        if(!empty($ward_id))
        {
            $data['ward_name']      = \App\Models\Ward::find($ward_id)->name;

            if(str_contains($address, ucwords($data['ward_name']))){
                $array_address  = explode(ucwords($data['ward_name']), $address);
                $string_address = $array_address[0];
                $array_address  = explode(', ', $string_address);
                $string_address = '';
                if(count($array_address) > 2)
                    for($i = 1; $i < count($array_address) - 1; $i++ ){
                        $string_address .= $array_address[$i].', ';
                    }
                else $string_address .= $array_address[count($array_address) - 2].', ';

//                for($i = 0; $i < count($array_address) - 1; $i++ ){
                $string_address .= $array_address[count($array_address) - 2].', ';
//                }
                $string_address     .= $data['ward_name'];

                if(!empty($data['district_name'])) {
                    $string_address .= ', '.$data['district_name'];
                }

                if(!empty($data['province_name'])) {
                    $string_address .= ', '.$data['province_name'];
                }

                if(!empty($data['country_name'])) {
                    $string_address .= ', '.$data['country_name'];
                }

            }

            else {
                $string_address      = $address.', '.$data['ward_name'];

                if(!empty($data['district_name'])) {
                    $string_address .= ', '.$data['district_name'];
                }

                if(!empty($data['province_name'])) {
                    $string_address .= ', '.$data['province_name'];
                }

                if(!empty($data['country_name'])) {
                    $string_address .= ', '.$data['country_name'];
                }
            }
        }
        else {
            $string_address = $address;


            if(!empty($data['country_name'])) {
                if(str_contains($address, ucwords($data['country_name']))){
                    if(!empty($data['province_name'])) {
                        if(str_contains($address, ucwords($data['province_name']))){
                            if(!empty($district_id)){
                                if(str_contains($address, ucwords($data['district_name']))){
                                    if(!empty($ward_id)) {
                                        if(str_contains($address, ucwords($data['ward_name']))){
                                            $string_address = $string_address;
                                        }
                                        else $string_address .= ', '.$data['ward_name'];
                                    }

                                }
                                else $string_address .= ', '.$data['district_name'];
                            }

                        }
                        else $string_address .= ', '.$data['province_name'];
                    }
                }
                else $string_address .= ', '.$data['country_name'];

            }

        }

        $data['address_full']       = $string_address;
        if($load_map == true) {
            //Load value Latitude and Longitude
            $address_map = IdeaHelper::get_infor_from_address( ucwords($data['address_full']) );

            if(!empty($address_map->results[0])) {
                $data['latitude']      = $address_map->results[0]->geometry->location->lat;

                $data['longitude']     = $address_map->results[0]->geometry->location->lng;
            }
            else {
                $data['latitude']      = '';

                $data['longitude']     = '';
            }
        }


        return $data;

    }

    public static function get_infor_from_address($address = null) {
        $prepAddr = str_replace(' ', '+', ucwords($address));

        $url = 'http://maps.google.com/maps/api/geocode/json?address='.$prepAddr.'&sensor=true';
//        dd($url);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        $response_a = json_decode($response);
        return $response_a;
    }

    public static function  stripUnicode($str){

        if (!$str) return false;

        $unicode = array(
            'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd'=>'đ',
            'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i'=>'í|ì|ỉ|ĩ|ị',
            'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y'=>'ý|ỳ|ỷ|ỹ|ỵ',
            'A'=>'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D'=>'Đ',
            'E'=>'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I'=>'Í|Ì|Ỉ|Ĩ|Ị',
            'O'=>'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U'=>'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y'=>'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        );
        foreach($unicode as $nonUnicode=>$uni){
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        return $str;
    }


    public static function nameModuleFiledType($colname, $table_id) {

        $module_fields_array = array();

        $module_fields  = DB::table('module_fields')->where('colname', $colname)->where('module', $table_id)->first();

        if(isset($module_fields -> popup_vals) && starts_with($module_fields -> popup_vals, "@"))
            $module_fields_array['foreign_table_name'] = str_replace("@", "", $module_fields -> popup_vals);

//        dd($module_fields -> field_type);
        if(isset($module_fields -> field_type)) {
            $module_fields_array['module_field_types_name'] = DB::table('module_field_types')->where('id', $module_fields->field_type)->value('name');
//            dd($module_fields_array['module_field_types_name']);
        }


        return $module_fields_array;

    }


    /*
     * Get value column table database (Lấy tên giá trị của bảng join)
     * @param array  $module_fields => ['module_field_types_name'=> value, 'foreign_table_name'=> value ]
     * @param $value column
     * @return $values column join
     * */

    /*
     * Get value column table database (Lấy tên giá trị của bảng join)
     * @param array  $module_fields => ['module_field_types_name'=> value, 'foreign_table_name'=> value ]
     * @param $value column
     * @return $values column join
     * */
    public  static function getValueColumn($field, $value, $result_lang_default = null){
        $external_table_name    = substr($field->popup_vals, 1);
        $field_type_str         = $field->field_type_str;
        $colname = $field->colname;
        if($field->lang_active == 0 and !empty($result_lang_default)){
            $value = $result_lang_default->$colname;
        }
        if(!empty($field)) {
            switch($field_type_str) {
                case 'Image':
                    // $image = \App\Models\Upload::find($value);
                    $values = IdeaHelper::pathImageNoSize($value);
                    // if(isset($image->name))
                    //     $values = $image->path();
                    // else $values = '';

                    // if(empty($image['path_name'])) {
                    //     $path_array = explode('/',$image['path']);
                    //     $values = 'uploads/'.$path_array[count($path_array) - 1];
                    // } else $values = $image['path_name'];
                    break;
                case 'File':
                    $image = \App\Models\Upload::find($value);
                    if(isset($image->name))
                        $values = $image->path();
                    else $values = ''; // dd($values);
                    break;
                case 'Files':
                    $files = json_decode($value);
                    $file_array = array();
                    foreach($files as $value){
                        $image = \App\Models\Upload::find($value);
                        //   if(isset($image->name))
                        //       $file_array[] = $image->path();
                        // else $file_array[] = '';
                        if(isset($image->name)){
                            if(empty($image['path_name'])) {
                                $path_array = explode('/',$image['path']);
                                $file_array[] = 'uploads/'.$path_array[count($path_array) - 1];
                            } else $file_array[] = $image['path_name'];
                        }
                    }
                    $values = $file_array;
                    break;
                case 'Dropdown':
                    $external_module = DB::table('modules')->where('name_db', $external_table_name)->first();
                    $values = DB::table($external_table_name)->where('id', $value)->value($external_module->view_col);
                    if($field->colname == "categories_id"){
                        $slug = DB::table($external_table_name)->where('id', $value)->value('slug');
                        $values = ['id'=>$value, $external_module->view_col=>$values, 'slug' => $slug];
                    }
                    else $values = ['id'=>$value, $external_module->view_col=>$values];


                    break;
                case 'Multiselect':
                    if(json_decode($value)) {
                        $external_module = DB::table('modules')->where('name_db', $external_table_name)->first();
                        $values = json_decode($value);
                        $values_list = array();
                        foreach($values as $value){
                            $values_list[$value] = DB::table($external_table_name)->where('id', $value)->value($external_module->view_col);
                        }

                        $values = $values_list;
                    }
                    else  $values = $value;


                    break;
                default:
                    $values = $value;
                    break;

            }
        }
        else    $values = $value;

        return $values;
    }
    /*
     * Get get Search Filter (Duyệt điều kiện where Select query)
     * @param   $filter_array
     * @param   $query
     * @param   $module_field_types_name
     * @return $query
     * */

    public  static function getSearchFilter($filter_array, $query, $module_field_types_name ){

        foreach($filter_array as $key => $value){

            if($value){

                $value = preg_replace('/[^0-9,]/','',$value);
                if($value >= 0 and $key != 'limit' and $key != 'page' ) {

                    if(!empty($module_field_types_name[$key])){

                        switch ($module_field_types_name[$key]['module_field_types_name']) {

                            case 'Dropdown':
                                $value = explode(',', $value);
                                $query = $query->whereIn($key, $value);
                                break;

                            case 'Multiselect':
                                $value_array = explode(',', $value);
                                $query = $query->where(function($query) use ($value_array,$key) {
                                    foreach ($value_array as $key1 => $value1) {
                                        if ($key1 == 0) {
                                            $query      = $query->where($key, 'LIKE', '%"' . $value1 . '"%');
                                        } else  $query      = $query->orWhere($key, 'LIKE', '%"' . $value1 . '"%');
                                    }
                                });
                                break;

                            default:
                                if ($value >= 0 and $key != 'limit' and $key != 'page') {
                                    $query = $query->where($key, $value);
                                }
                                break;
                        }
                    }
                }
            }
        }

        return $query;
    }


    /*
     * Get File path
     * @param   $id image
     * @param   $size image
     * */

    public static function pathImage($id, $size){
        $img = Upload::find($id);
        if(!empty($img)){
            $path = explode("/",$img->path);
            $img_name = $path[count($path) - 1];
            $date_append = substr($img_name, 2, 15 );
            return url("/s".$size."/$img->caption/$date_append/$img->name");
        } else return '';

    }

    /*
     * Get File path
     * @param   $id image
     * @param   $size image
     *
     */

    public static function pathImageNoSize($id){
        $img = Upload::find($id);
        if(!empty($img)){
            $path = explode("/",$img->path);
            $img_name = $path[count($path) - 1];
            $date_append = substr($img_name, 2, 15 );
            return '/'.$img->caption.'/'.$date_append.'/'.$img->name;
        } else return '';

    }

    /*
     * Lấy dự liệu ngôn ngữ qua tab view
     * @param   $locale id ngôn ngữ
     * @param   $parent_id id tiếng việt
     * */

    public static function getDataLaguge($locale = null, $parent_id = null, $module = null){
        if(!empty($module)){
            $name_db_lage = 'App\Models\\'.$module->name.'_lang';
            $list_data_lang     = $name_db_lage::where([['locale', '=' , $locale], ['parent_id', '=' , $parent_id]])->first();
            $module_lang    = Module::get($module->name_db.'_langs');
            $module_lang->row = $list_data_lang;
            $module_lang->fields['locale']['defaultvalue'] = $locale;
            $module_lang->fields['parent_id']['defaultvalue'] = $parent_id;
            $result['list_data_lang'] = $list_data_lang;
            $result['module_lang']      = $module_lang;
            return $result;
        }

    }



    /**
     * lấy dữ liệu Categories theo cấp con
     * @param $categories data categories
     * @param $parent_id id nhóm cha
     * @param $char ký tự cấp con
     * @param $resultết quả trả về
     * @param $stt Cấp
     * @param $idchar id cha
     **/
    public static function  showCategories($categories, $count, $parent_id = 0, $char = '' , $result = array(), $level = 0, $stt1 = 0, $idchar = '', $hierarchy_string = '', $hierarchy = '',  $dem = 0, $children = array()) //, $cate_child = array()
    {
        foreach ($categories as $key => $item)
        {
            if($level == 0){
                $idchar = $parent_id;
                $hierarchy_string = $parent_id;
            }

            if ($item->parent == $parent_id)
            {
                $dem = $dem + 1;
                $idchar_array = explode(".", $idchar);
                $hierarchy_string_array = explode(",", $hierarchy_string);
                $hierarchy1 = '';

                for ($i=0; $i<= $level; $i++){
                    $hierarchy_item = ($hierarchy_string_array[$i] < 10) ? '0'.$hierarchy_string_array[$i] : $hierarchy_string_array[$i];
                    if($i==0){
                        $idchar         = $idchar_array[0];
                        $hierarchy1   = $hierarchy_item;
                        $hierarchy    = $hierarchy_string_array[0];
                    }
                    else {
                        $idchar         = $idchar.'.'.$idchar_array[$i];
                        $hierarchy1   = $hierarchy1.','.$hierarchy_item;
                        $hierarchy    = $hierarchy.','.$hierarchy_string_array[$i];
                    }
                }


                $idchar = $idchar.'.'.$item->id;
                $hierarchy1 = ($item->hierarchy < 10) ? $hierarchy1.',0'.$item->hierarchy : $hierarchy1.','.$item->hierarchy ;
                $hierarchy = $hierarchy.','.$item->hierarchy;

                $item->id_parent    = $idchar;
                $item->name_cat      = $char.$item->name;
                $item->stt          = $hierarchy1;
                $item->level        = $level;

                // Xóa chuyên mục đã lặp
                unset($categories[$key]);

            }
            if(!empty($item->id_parent)){
                $result[$item->id] = $item;
//                $result[$idchar] = $item;
                $stt1 = $stt1+1;
            }
//            else

            if ($item->parent == $parent_id)
            {
                IdeaHelper::showCategories($categories, $count, $item->id, $char, $result, $level + 1, $stt1, $idchar, $hierarchy, $hierarchy,   $dem, $children); // . '&nbsp; &nbsp; &nbsp; &nbsp; '
            }
        }
        if( $level == 0){
            $result_sort = array();
            foreach ($result as $key => $row)
            {
                $result_sort[$key] = $row->stt;
            }
            array_multisort($result_sort, SORT_ASC, $result);

            //return $result;
            $result_name = array();
            $result_name[] = 'Chọn nhóm';
            foreach ($result as $key=>$value) {
                $result_name[$value->id] = $value->name_cat;
            }
//            foreach ($result as $ke=>$val) {
//                echo  $val->name_cat.'-'.$val->stt.'<br/>';
//            }
//            return $result_name;
            return $result;
        }


    }
    /**
     * Hàm xử lý dữ liệu đầu vào khi cập nhật item đa ngôn ngữ
     * @param $request data
     * @param $module
     * @param $language
     **/

    public static function data_insert_item_muti_langauges($request, $module_name){
        $module = Module::get($module_name);
        $language =  Language::select('id','locale','name','image')->get();
        if(!empty($language)){
            $request_lang = array();
            $fields = $module->fields;

            foreach ($language as $lang_item){

                //Call class trang
                if($lang_item->id != config('app.locale_id'))
                    $tr[$lang_item->id] = new GoogleTranslate(config('app.locale_name'), $lang_item['locale']);

                foreach ($request->all()  as $key =>$value){
                    if(is_array($value)) {
                        if ($key != 'locale' and $key != 'id' and $key != 'translate_auto') {
                            if (!empty($value[$lang_item->id])){
                                if(!empty($lang_item->id))
                                    $request_lang[$lang_item->id][$key] = $value[$lang_item->id];
                                else  $request_lang[$lang_item->id][$key] = null;
                            }

                            else if (!empty($value[config('app.locale_id')])) {

                                if(isset($request['translate_auto'][config('app.locale_id')])  and $lang_item->id != config('app.locale_id')  and !empty($fields[$key]['field_type']) and  !in_array($fields[$key]['field_type'], [2, 4, 5, 7, 8, 9, 12, 13, 14, 15, 17] )){
                                    $request_lang[$lang_item->id][$key] = $tr[$lang_item->id]->translate($value[config('app.locale_id')]);

                                } else {
                                    if(isset($request['translate_auto'][$lang_item->id]) and   !in_array($fields[$key]['field_type'], [2, 4, 5, 7, 8, 9, 12, 13, 14, 15, 17] )) {
                                        $request_lang[$lang_item->id][$key] = $tr[$lang_item->id]->translate($value[config('app.locale_id')]);
                                    }
                                    else
                                        $request_lang[$lang_item->id][$key] = $value[config('app.locale_id')];
                                }

                            }
                        } else if ($key == 'locale') {
                            $request_lang[$lang_item->id][$key] = $lang_item->id;
                        } else if ($key == 'id' and isset($value[$lang_item->id])) {
                            $request_lang[$lang_item->id][$key] = $value[$lang_item->id];
                        }
                    }
                    else if(strpos($key, '_lang_') !== false )
                    {
                        $key = explode("_lang_",$key);
                        $request_lang[$key[1]][$key[0]] = $value;
                    }

                }
            }

            if(!empty($request_lang)) {
                //Insert value langguge default

                //Xử lý thêm bảng ghi
                $i = 0;
                foreach ($request_lang as $key => $request) {
                    $i++;
                    if ($i == 1) {
                        $insert_id = Module::insert($module_name, $request);
                        if (isset($insert_id)) {
                            $request['local_parent'] = $insert_id;
                            $local_parent = $insert_id;
                            $insert_id = Module::updateRow($module_name, $request, $insert_id);
                        }
                    } else {
                        $request['local_parent'] = $local_parent;
                        $insert_id = Module::insert($module_name, $request);
                    }
                }
                return $insert_id;
            }

            else return false;
        }
        else return false;
    }

    /**
     * Hàm xử lý dữ liệu đầu vào khi cập nhật item đa ngôn ngữ
     * @param $request data
     * @param $module
     * @param $language
     **/

    public static function update_item_muti_langauges($request, $id, $module_name){
        $module = Module::get($module_name);
        $language =  Language::select('id','locale','name','image')->get()->toArray();

        $request_all = $request->all();
        //Gán lại local_parent thành locale defaull  có id mặc định
        // if(!empty($request->id[config('app.locale_id')]) and $id != $request->id[config('app.locale_id')]){

        //         // $id = $request->id[config('app.locale_id')];
        //         $array_local_parent = array();
        //         foreach ($request->local_parent as  $key_local_paren => $local_parent_item){
        //             $array_local_parent[$key_local_paren] = $request->id[config('app.locale_id')];
        //         }

        //         $request_all['local_parent'] = $array_local_parent;
        // }

        if(!empty($language)) {
            $request_lang = array();
            $fields = $module->fields;
            foreach ($language as $key_lang => $lang_item){
                //Call class trang
                //Khởi tạo GoogleTranslate cấu hình
                if($lang_item['id'] != config('app.locale_id')){
                    if(isset($request[$module->view_col][config('app.locale_id')])){

                        $tr[$lang_item['id']] = new GoogleTranslate($lang_item['locale'], config('app.locale_name'));
                    }

                    else if(empty($value[config('app.locale_id')])){
                        $local_de =  array_search($id, $request['id']);
                        //Lấy locale cho ngôn ngữ hiện tại;
                        $lang_item_locale = Language::where('id', $local_de)->value('locale');
                        if($lang_item['id'] != $local_de){
                            $tr[$lang_item['id']] = new GoogleTranslate($lang_item_locale, $lang_item['locale']);
                        }
                    }
                }



                foreach ($request_all  as $key =>$value){
                    if(is_array($value)) {
                        if ($key != 'locale' and $key != 'id'  and $key != 'translate_auto' ) {

                            if (!empty($value[$lang_item['id']]) ){
                                if(isset($request_all['translate_auto'][config('app.locale_id')]) and isset($request_all['translate_auto'][$lang_item['id']]) and $lang_item['id'] != config('app.locale_id')  and !empty($fields[$key]['field_type'])  and  !in_array($fields[$key]['field_type'], [2, 4, 5, 7, 8, 9, 12, 13, 14, 15, 17] )){
                                    $request_lang[$lang_item['id']][$key] = $tr[$lang_item['id']]->translate($value[config('app.locale_id')]);
                                }
                                else {
                                    $request_lang[$lang_item['id']][$key] = $value[$lang_item['id']];
                                }

                            }

                            else if (!empty($value[config('app.locale_id')])){
                                $request_lang[$lang_item['id']][$key] = $value[config('app.locale_id')];

                            }
                            else {
                                if(!empty($value[array_search($id, $request_all['id'])]) )
                                     $request_lang[$lang_item['id']][$key] = $value[array_search($id, $request_all['id'])];
                                else $request_lang[$lang_item['id']][$key] = null;

                            }
                        }
                        else if ($key == 'locale') {
                            $request_lang[$lang_item['id']][$key] = $lang_item['id'];
                        }
                        else if ($key == 'id' and isset($value[$lang_item['id']])) {
                            $request_lang[$lang_item['id']][$key] = $value[$lang_item['id']];
                        }
                        else if($key == 'translate_auto'){
                            if(!empty($value[$lang_item['id']]))
                                $request_lang[$lang_item['id']][$key] = $value[$lang_item['id']];
                            else $request_lang[$lang_item['id']][$key] = 0;
                        }
                    }
                    else if(strpos($key, '_lang_') !== false )
                    {
                        $key = explode("_lang_",$key);
                        $request_lang[$key[1]][$key[0]] = $value;
                    }

                }
            }
            if(!empty($request_lang)){
                foreach ($request_lang as $key => $request) {
                    if(!empty($request['id'])) {
                        $insert_id = Module::updateRow($module_name, $request, $request['id']);
                    }
                    else {
                        $insert_id = Module::insert($module_name, $request);
                    }
                }
                return $insert_id;
            }

            else return false;
        }
        else return false;
    }


}


?>