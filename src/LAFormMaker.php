<?php
/**
 * Code generated using IdeaGroup
 * Help: lehung.hut@gmail.com
 * LaraAdmin is open-sourced software licensed under the MIT license.
 * Developed by: Lehungdev IT Solutions
 * Developer Website: http://ideagroup.vn
 */

namespace Lehungdev\Crmadmin;

use Schema;
use Auth;
use Collective\Html\FormFacade as Form;
use Lehungdev\Crmadmin\Models\Module;
use Lehungdev\Crmadmin\Models\ModuleFieldTypes;
use Illuminate\Support\Str;
use Request;

/**
 * Class LAFormMaker
 * @package Lehungdev\Crmadmin
 *
 * This class is blade directive implementation for Form Elements in Module as well as other utilities
 * for Access Control. It also has method process_values which processes fields data from its context.
 *
 */
class LAFormMaker
{
    /**
     * Print input field enclosed within form.
     *
     * Uses blade syntax @la_input('name')
     *
     * @param $module Module Object
     * @param $field_name Field Name for which input has be created
     * @param null $default_val Default Value of Field. This will override default value from context.
     * @param null $required2 Is this field mandatory.
     * @param string $class Custom css class. Default would be bootstrap 'form-control' class
     * @param array $params Additional Parameters for Customization
     * @return string This return html string with field inputs
     */
    public static function input($module, $field_name, $default_val = null, $required2 = null, $class = 'form-control', $params = [])
    {
        // Check Field Write Aceess
        if(Module::hasFieldAccess($module->id, $module->fields[$field_name]['id'], $access_type = "write")) {

            $row = null;
            if(isset($module->row)) {
                $row = $module->row;
            }

            //print_r($module->fields);
            $label = $module->fields[$field_name]['label'];
            $field_type = $module->fields[$field_name]['field_type'];
            $unique = $module->fields[$field_name]['unique'];
            $defaultvalue = $module->fields[$field_name]['defaultvalue'];
            $minlength = $module->fields[$field_name]['minlength'];
            $maxlength = $module->fields[$field_name]['maxlength'];
            $required = $module->fields[$field_name]['required'];
            $popup_vals = $module->fields[$field_name]['popup_vals'];

            if($required2 != null) {
                $required = $required2;
            }

            $field_type = ModuleFieldTypes::find($field_type);

            $out = '<div class="form-group" id="' . $field_name . '">';
            $required_ast = "";

            if(!isset($params['class'])) {
                $params['class'] = $class;
            }
            if(!isset($params['placeholder'])) {
                $params['placeholder'] = 'Enter ' . $label;
            }
            if(isset($minlength)) {
                $params['data-rule-minlength'] = $minlength;
            }
            if(isset($maxlength)) {
                $params['data-rule-maxlength'] = $maxlength;
            }
            if($unique && !isset($params['unique'])) {
                $params['data-rule-unique'] = "true";
                $params['field_id'] = $module->fields[$field_name]['id'];
                $params['adminRoute'] = config('crmadmin.adminRoute');
                if(isset($row)) {
                    $params['isEdit'] = true;
                    $params['row_id'] = $row->id;
                } else {
                    $params['isEdit'] = false;
                    $params['row_id'] = 0;
                }
                $out .= '<input type="hidden" name="_token_' . $module->fields[$field_name]['id'] . '" value="' . csrf_token() . '">';
            }

            if($required && !isset($params['required'])) {
                $params['required'] = $required;
                $required_ast = "*";
            }

            switch($field_type->name) {
                case 'Address':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }

                    $params['cols'] = 30;
                    $params['rows'] = 3;
                    $out .= Form::textarea($field_name, $default_val, $params);
                    break;
                case 'Checkbox':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';
                    $out .= '<input type="hidden" value="false" name="' . $field_name . '_hidden">';

                    // ############### Remaining
                    unset($params['placeholder']);
                    unset($params['data-rule-maxlength']);

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }

                    $out .= Form::checkbox($field_name, $field_name, $default_val, $params);
                    $out .= '<div class="Switch Round On" style="vertical-align:top;margin-left:10px;"><div class="Toggle"></div></div>';
                    break;
                case 'Currency':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }

                    if($params['data-rule-maxlength'] != "" && $params['data-rule-maxlength'] != 0) {
                        $params['max'] = $params['data-rule-maxlength'];
                    }
                    if($params['data-rule-minlength'] != "" && $params['data-rule-minlength'] != 0) {
                        $params['min'] = $params['data-rule-minlength'];
                    }

                    unset($params['data-rule-minlength']);
                    unset($params['data-rule-maxlength']);

                    $params['data-rule-currency'] = "true";
                    $params['min'] = "0";
                    $out .= Form::number($field_name, $default_val, $params);
                    break;
                case 'Date':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }
                    $dval = $default_val;
                    $is_null = "";
                    if($default_val == "NULL") {
                        $is_null = " checked";
                        $params['readonly'] = "";
                    } else if($default_val != "") {
                        $dval = date("d/m/Y", strtotime($default_val));
                    }

                    unset($params['data-rule-maxlength']);
                    // $params['data-rule-date'] = "true";

                    $out .= "<div class='input-group date'>";
                    $out .= Form::text($field_name, $dval, $params);
                    $out .= "<span class='input-group-addon input_dt'><span class='fa fa-calendar'></span></span><span class='input-group-addon null_date'><input class='cb_null_date' type='checkbox' name='null_date_" . $field_name . "' $is_null value='true'> Null ?</span></div>";
                    // $out .= Form::date($field_name, $default_val, $params);
                    break;
                case 'Datetime':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }

                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }

                    $is_null = "";
                    if($default_val == "NULL") {
                        $is_null = " checked";
                        $params['readonly'] = "";
                    } else if($default_val == null) {
                        $default_val = $defaultvalue;
                    }

                    // ############### Remaining
                    $dval = $default_val;
                    if($default_val == "now()") {
                        $dval = date("d/m/Y h:i A");
                    } else if($default_val != NULL && $default_val != "" && $default_val != "NULL") {
                        $dval = date("d/m/Y h:i A", strtotime($default_val));
                    }
                    $out .= "<div class='input-group datetime'>";
                    $out .= Form::text($field_name, $dval, $params);
                    $out .= "<span class='input-group-addon input_dt'><span class='fa fa-calendar'></span></span><span class='input-group-addon null_date'><input class='cb_null_date' type='checkbox' name='null_date_" . $field_name . "' $is_null value='true'> Null ?</span></div>";
                    break;
                case 'Decimal':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }

                    if($params['data-rule-maxlength'] != "" && $params['data-rule-maxlength'] != 0) {
                        $params['max'] = $params['data-rule-maxlength'];
                    }
                    if($params['data-rule-minlength'] != "" && $params['data-rule-minlength'] != 0) {
                        $params['min'] = $params['data-rule-minlength'];
                    }

                    unset($params['data-rule-minlength']);
                    unset($params['data-rule-maxlength']);

                    $out .= Form::number($field_name, $default_val, $params);
                    break;
                case 'Dropdown':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    unset($params['data-rule-minlength']);
                    unset($params['data-rule-maxlength']);
                    $params['data-placeholder'] = $params['placeholder'];
                    unset($params['placeholder']);
                    $params['rel'] = "select2";

                    //echo $defaultvalue;
                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && $row->$field_name) {
                        $default_val = $row->$field_name;
                    } else if($default_val == NULL || $default_val == "" || $default_val == "NULL") {
                        // When Adding Record if we dont have default value let's not show NULL By Default
                        if($popup_vals == '@users'){
                            $default_val = Auth::user()->id;
                        } else {
                            $default_val = "0";
                        }

                    }

                    // Bug here - NULL value Item still shows Not null in Form
                    if($default_val == NULL) {
                        $params['disabled'] = "";
                    }


                    $popup_vals_str = $popup_vals;
                    if(is_string($popup_vals) && Str::startsWith($popup_vals, '@') && $popup_vals == "@categories") {
                        $url = Request::url();
//                        $url_array = explode("categories", $url);
                        $url_array = explode("/", $url);
                        if(is_numeric($url_array[count($url_array) - 1]) || ( is_numeric($url_array[count($url_array) - 2]) and  $url_array[count($url_array) - 1] == 'edit' )){

                            $id_cat = is_numeric($url_array[count($url_array) - 2]) ? $url_array[count($url_array) - 2]:$url_array[count($url_array) - 1];

                            if(is_numeric($url_array[count($url_array) - 2])) {
                                $tb_name = substr( $url_array[count($url_array) - 3],  0, strlen($url_array[count($url_array) - 3]) - 5 );
                                $item_info = \DB::table($tb_name)->where('id',$url_array[count($url_array) - 2])->first();
//                                dd($item_info);
                                $id_cat = $item_info->categories_id;
                            }


                        } else {
                            //Lấy danh sách module
                            $module_table = \DB::table('module_tables')->pluck('module_table', 'id');
                            $module_table_item = array();
                            foreach ($module_table as $key => $value) {

                                if(strpos($url, $value)){
                                    $module_table_item[] = $key;
                                }

                            }

                            $id_cat = 0;

                        }

                        // Get Module / Table Name
                        $json = str_ireplace("@", "", $popup_vals);
                        $table_name = strtolower(Str::plural($json));
                        // Search Module
                        $module = Module::getByTable($table_name);
                        $module_table_item = array();
                        if(!empty($id_cat)){
                            $cat_info = \DB::table($table_name)->where('id',$id_cat)->get();
                            $module_table_item[] = $cat_info[0]->module_table_id;

                            //Lấy danh sách module
                            $module_table = \DB::table('module_tables')->pluck('module_table', 'id');
                            foreach ($module_table as $key => $value) {

                                if($module_table[$cat_info[0]->module_table_id] == $value ){
                                    $module_table_item[] = $key;
                                }

                            }

                        }

                        if(!empty($module_table_item))
                            $categories = \DB::table($table_name)->where("status", 1)->where("deleted_at", null)->whereIn('module_table_id',$module_table_item)->get();
                        else
                            $categories = \DB::table($table_name)->where("status", 1)->where("deleted_at", null)->get();

                        $popup_vals = LAFormMaker::showCategories($categories, count($categories), $id_cat);
                        $result_name = array();
                        if(empty($cat_info)){
                            $result_name[0] = 'Chọn nhóm';
                        } else {
                            $result_name[0] = 'Chọn nhóm';
                            if(!empty($popup_vals))
                                $result_name[''.$cat_info[0]->name] = [];
                            else $result_name[$cat_info[0]->id] = $cat_info[0]->name;
                        }
                        foreach ($popup_vals as $key =>$value ){
                            $string = '';
                            for($i=0; $i <= $value->level; $i++){
                                $string = $string.'&nbsp; &nbsp;';
                            }
                            if($key < 1){
                                $result_name[$value->id] = $string.$string.$value->name_cat;
                            }
                            else if (isset($value->stt) and strpos($value->stt, $popup_vals[$key-1]->stt) !== false and $value->stt != $popup_vals[$key-1]->stt ) {
                                if($value->level > 1){
                                    $result_name['&nbsp; &nbsp; &nbsp; '.$string.$popup_vals[$key-1]->name] = [];
                                } else
                                    $result_name[' '.$string.$popup_vals[$key-1]->name] = [];
                                unset($result_name[$popup_vals[$key-1]->id]);
                                $result_name[$value->id] =  ' '.$string.$string.$value->name_cat;
                            } else {
                                if($value->level > 1) {
                                    $result_name[$value->id] = $string . $string . $value->name_cat;
                                } else  $result_name[$value->id] = '&nbsp; '.$string . $string . $value->name_cat;
                            }
                            $string = '';
                        }

                        $popup_vals = $result_name;

                    }

                    else {
                        if ($popup_vals != "") {
                            $popup_vals = LAFormMaker::process_values($popup_vals);
                        } else {
                            $popup_vals = array();
                        }

                        $popup_vals[0] = "None";
                        ksort($popup_vals);
                    }

                    $out .= Form::select($field_name, $popup_vals, $default_val, $params);

                    break;

                case 'DropdownCat':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    unset($params['data-rule-minlength']);
                    unset($params['data-rule-maxlength']);
                    $params['data-placeholder'] = $params['placeholder'];
                    unset($params['placeholder']);
                    $params['rel'] = "select2";

                    //echo $defaultvalue;
                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && $row->$field_name) {
                        $default_val = $row->$field_name;
                    } else if($default_val == NULL || $default_val == "" || $default_val == "NULL") {
                        // When Adding Record if we dont have default value let's not show NULL By Default
                        $default_val = "0";
                    }

                    // Bug here - NULL value Item still shows Not null in Form
                    if($default_val == NULL) {
                        $params['disabled'] = "";
                    }

                    $popup_vals_str = $popup_vals;
                    if($popup_vals != "") {
                        $popup_vals = LAFormMaker::process_values($popup_vals);
                    } else {
                        $popup_vals = array();
                    }

                    $popup_vals[0] = "None";
                    ksort($popup_vals);
                    $out .= Form::select($field_name, $popup_vals, $default_val, $params);

                    break;

                case 'Email':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }

                    $params['data-rule-email'] = "true";
                    $out .= Form::email($field_name, $default_val, $params);
                    break;
                case 'File':
                    $out .= '<label for="' . $field_name . '" style="display:block;">' . $label . $required_ast . ' :</label>';

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }
                    if(!is_numeric($default_val)) {
                        $default_val = 0;
                    }
                    $out .= Form::hidden($field_name, $default_val, $params);

                    if($default_val != 0) {
                        $upload = \App\Models\Upload::find($default_val);
                    }
                    if(isset($upload->id)) {
                        $out .= "<a class='btn btn-default btn_upload_file hide' file_type='file' selecter='" . $field_name . "'>Upload <i class='fa fa-cloud-upload'></i></a>
                            <a class='uploaded_file' target='_blank' href='" . url("files/" . $upload->hash . DIRECTORY_SEPARATOR . $upload->name) . "'><i class='fa fa-file-o'></i><i title='Remove File' class='fa fa-times'></i></a>";
                    } else {
                        $out .= "<a class='btn btn-default btn_upload_file' file_type='file' selecter='" . $field_name . "'>Upload <i class='fa fa-cloud-upload'></i></a>
                            <a class='uploaded_file hide' target='_blank'><i class='fa fa-file-o'></i><i title='Remove File' class='fa fa-times'></i></a>";
                    }
                    break;

                case 'Files':
                    $out .= '<label for="' . $field_name . '" style="display:block;">' . $label . $required_ast . ' :</label>';

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }
                    if(is_array($default_val)) {
                        $default_val = json_encode($default_val);
                    }

                    $default_val_arr = json_decode($default_val);

                    if(is_array($default_val_arr) && count($default_val_arr) > 0) {
                        $uploadIds = array();
                        $uploadImages = "";
                        foreach($default_val_arr as $uploadId) {
                            $upload = \App\Models\Upload::find($uploadId);
                            if(isset($upload->id)) {
                                $uploadIds[] = $upload->id;
                                $fileImage = "";
                                if(in_array($upload->extension, ["jpg", "png", "gif", "jpeg"])) {
                                    $fileImage = "<img src='" . url("files/" . $upload->hash . DIRECTORY_SEPARATOR . $upload->name . "?s=90") . "'>";
                                } else {
                                    $fileImage = "<i class='fa fa-file-o'></i>";
                                }
                                $uploadImages .= "<a class='uploaded_file2' upload_id='" . $upload->id . "' target='_blank' href='" . url("files/" . $upload->hash . DIRECTORY_SEPARATOR . $upload->name) . "'>" . $fileImage . "<i title='Remove File' class='fa fa-times'></i></a>";
                            }
                        }

                        $out .= Form::hidden($field_name, json_encode($uploadIds), $params);
                        if(count($uploadIds) > 0) {
                            $out .= "<div class='uploaded_files'>" . $uploadImages . "</div>";
                        }
                    } else {
                        $out .= Form::hidden($field_name, "[]", $params);
                        $out .= "<div class='uploaded_files'></div>";
                    }
                    $out .= "<a class='btn btn-default btn_upload_files' file_type='files' selecter='" . $field_name . "' style='margin-top:5px;'>Upload <i class='fa fa-cloud-upload'></i></a>";
                    break;

                case 'Float':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }

//                    if($params['data-rule-maxlength'] != "" && $params['data-rule-maxlength'] != 0) {
//                        $params['max'] = $params['data-rule-maxlength'];
//                    }
//                    if($params['data-rule-minlength'] != "" && $params['data-rule-minlength'] != 0) {
//                        $params['min'] = $params['data-rule-minlength'];
//                    }

//                    unset($params['data-rule-minlength']);
//                    unset($params['data-rule-maxlength']);

                    $out .= Form::number($field_name, $default_val, $params);
                    break;
                case 'HTML':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }
                    $params['class'] = 'htmlbox';
                    $out .= Form::textarea($field_name, $default_val, $params);
                    break;
                case 'Image':
                    $out .= '<label for="' . $field_name . '" style="display:block;">' . $label . $required_ast . ' :</label>';

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }
                    if(!is_numeric($default_val)) {
                        $default_val = 0;
                    }
                    $out .= Form::hidden($field_name, $default_val, $params);

                    if($default_val != 0) {
                        $upload = \App\Models\Upload::find($default_val);
                    }
                    if(isset($upload->id)) {
                        $path = explode("/",$upload->path);
                        $img_name = $path[count($path) - 1];
                        $date_append = substr($img_name, 2, 15 );
//                        $out .= "<a class='btn btn-default btn_upload_image hide' file_type='image' selecter='" . $field_name . "'>Upload <i class='fa fa-cloud-upload'></i></a>
//                            <div class='uploaded_image'><img src='" . url("files/" . $upload->hash . DIRECTORY_SEPARATOR . $upload->name . "?s=150") . "'><i title='Remove Image' class='fa fa-times'></i></div>";
                        $out .= "<a class='btn btn-default btn_upload_image hide' file_type='image' selecter='" . $field_name . "'>Upload <i class='fa fa-cloud-upload'></i></a>
                            <div class='uploaded_image'><img src='" . url("/s80x80/$upload->caption/$date_append/$upload->name") . "'><i title='Remove Image' class='fa fa-times'></i></div>";

                    } else {
                        $out .= "<a class='btn btn-default btn_upload_image' file_type='image' selecter='" . $field_name . "'>Upload <i class='fa fa-cloud-upload'></i></a>
                            <div class='uploaded_image hide'><img src=''><i title='Remove Image' class='fa fa-times'></i></div>";
                    }

                    break;
                case 'Integer':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

//                    if($params['data-rule-maxlength'] != "" && $params['data-rule-maxlength'] != 0) {
//                        $params['max'] = $params['data-rule-maxlength'];
//                    }
//                    if($params['data-rule-minlength'] != "" && $params['data-rule-minlength'] != 0) {
//                        $params['min'] = $params['data-rule-minlength'];
//                    }
//
//                    unset($params['data-rule-minlength']);
//                    unset($params['data-rule-maxlength']);

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }
                    // $params['min'] = "0"; // Required for Non-negative numbers
                    $out .= Form::number($field_name, $default_val, $params);
                    break;
                case 'Mobile':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }

                    $out .= Form::text($field_name, $default_val, $params);
                    break;
                case 'Multiselect':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    unset($params['data-rule-maxlength']);
                    $params['data-placeholder'] = "Select multiple " . Str::plural($label);
                    unset($params['placeholder']);
                    $params['multiple'] = "true";
                    $params['rel'] = "select2";
                    if($default_val == null) {
                        if($defaultvalue != "") {
                            $default_val = json_decode($defaultvalue);
                        } else {
                            $default_val = "";
                        }
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = json_decode($row->$field_name);
                    }

                    if($popup_vals != "") {
                        $popup_vals = LAFormMaker::process_values($popup_vals);
                    } else {
                        $popup_vals = array();
                    }

                    $out .= Form::select($field_name . "[]", $popup_vals, $default_val, $params);
                    break;
                case 'Name':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }

                    $out .= Form::text($field_name, $default_val, $params);
                    break;
                case 'Password':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    $out .= Form::password($field_name, $params);
                    break;
                case 'Radio':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' : </label><br>';

                    // ############### Remaining
                    unset($params['placeholder']);
                    unset($params['data-rule-maxlength']);

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }

                    if(Str::startsWith($popup_vals, '@')) {
                        $popup_vals = LAFormMaker::process_values($popup_vals);
                        $out .= '<div class="radio">';
                        foreach($popup_vals as $key => $value) {
                            $sel = false;
                            if($default_val != "" && $default_val == $value) {
                                $sel = true;
                            }
                            $out .= '<label>' . (Form::radio($field_name, $key, $sel)) . ' ' . $value . ' </label>';
                        }
                        $out .= '</div>';
                        break;
                    } else {
                        if($popup_vals != "") {
                            $popup_vals = array_values(json_decode($popup_vals));
                        } else {
                            $popup_vals = array();
                        }
                        $out .= '<div class="radio">';
                        foreach($popup_vals as $value) {
                            $sel = false;
                            if($default_val != "" && $default_val == $value) {
                                $sel = true;
                            }
                            $out .= '<label>' . (Form::radio($field_name, $value, $sel)) . ' ' . $value . ' </label>';
                        }
                        $out .= '</div>';
                        break;
                    }
                case 'String':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }
                    $out .= Form::text($field_name, $default_val, $params);
                    break;
                case 'Taginput':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    if(isset($params['data-rule-maxlength'])) {
                        $params['maximumSelectionLength'] = $params['data-rule-maxlength'];
                        unset($params['data-rule-maxlength']);
                    }
                    $params['multiple'] = "true";
                    $params['rel'] = "taginput";
                    $params['data-placeholder'] = "Add multiple " . Str::plural($label);
                    unset($params['placeholder']);

                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = json_decode($row->$field_name);
                    }

                    if($default_val == null) {
                        $defaultvalue2 = json_decode($defaultvalue);
                        if(is_array($defaultvalue2)) {
                            $default_val = $defaultvalue;
                        } else if(is_string($defaultvalue)) {
                            if(strpos($defaultvalue, ',') !== false) {
                                $default_val = array_map('trim', explode(",", $defaultvalue));
                            } else {
                                $default_val = [$defaultvalue];
                            }
                        } else {
                            $default_val = array();
                        }
                    }
                    $default_val = LAFormMaker::process_values($default_val);
                    $out .= Form::select($field_name . "[]", $default_val, $default_val, $params);
                    break;
                case 'Textarea':
                    $out .= '<div class="textTinymce1">';
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    $params['cols'] = 30;
                    $params['rows'] = 6;


                    if($params['data-rule-maxlength'])
                        unset($params['data-rule-maxlength']);

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }

                    $out .= Form::textarea($field_name, $default_val, $params);
                    $out .= '</div>';
                    break;
                case 'TextField':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }

                    $out .= Form::text($field_name, $default_val, $params);
                    break;


                //Text Editor
                case 'Text':
                    $out .= '<div class="textTinymce">';
                    $out .= '<label for="'.$field_name.'">'.$label.$required_ast.' :</label>';
                    unset($params['data-rule-minlength']);
                    unset($params['data-rule-maxlength']);
                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }

                    $params['cols'] = 30;
                    $params['rows'] = 15;
                    $out .= Form::textarea($field_name, $default_val, $params);
//                    $out .= '<script> tinymce.init({ selector: "textarea[name='.$field_name.']" }) </script> ';
                    $out .= '</div>';
                    break;


                //Longtext Editor
                case 'LongText':
                    $out .= '<div class="textTinymce">';
                    $out .= '<label for="'.$field_name.'">'.$label.$required_ast.' :</label>';
                    unset($params['data-rule-minlength']);
                    unset($params['data-rule-maxlength']);
                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }

                    $params['cols'] = 30;
                    $params['rows'] = 15;
                    $out .= Form::textarea($field_name, $default_val, $params);
//                    $out .= '<script> tinymce.init({ selector: "textarea[name='.$field_name.']" }) </script> ';
                    $out .= '</div>';
                    break;

                case 'URL':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }

                    $params['data-rule-url'] = "true";
                    $out .= Form::text($field_name, $default_val, $params);
                    break;


                case 'UsersCreated1':
                    $out .= '<label for="'.$field_name.'">'.$label.$required_ast.' :</label>';

                    if($default_val == null) {
                        $default_val =  Auth::user()->id;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }

                    $out .= Form::hidden($field_name, $default_val, $params);
                    break;


                case 'UsersCreated':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    unset($params['data-rule-minlength']);
                    unset($params['data-rule-maxlength']);
                    $params['data-placeholder'] = $params['placeholder'];
                    unset($params['placeholder']);
                    $params['rel'] = "select2";

                    //echo $defaultvalue;
//                    if($default_val == null) {
//                        $default_val = $defaultvalue;
//                    }

                    if($default_val == null) {
                        $default_val =  Auth::user()->id;
                    }

                    // Override the edit value
                    if(isset($row) && $row->$field_name) {
                        $default_val = $row->$field_name;
                    } else if($default_val == NULL || $default_val == "" || $default_val == "NULL") {
                        // When Adding Record if we dont have default value let's not show NULL By Default
                        $default_val = "0";
                    }

                    // Bug here - NULL value Item still shows Not null in Form
                    if($default_val == NULL) {
                        $params['disabled'] = "";
                    }

                    $popup_vals_str = $popup_vals;
                    if($popup_vals != "") {
                        $popup_vals = LAFormMaker::process_values($popup_vals);
                    } else {
                        $popup_vals = array();
                    }

                    if(!$required) {
                        array_unshift($popup_vals, "None");
                    }
                    $out .= Form::select($field_name, $popup_vals, $default_val, $params);
                    $out .= '<script> $("select[name=\''.$field_name.'\']").prop("disabled", \'true\'); $(\'form\').on(\'submit\', function() { $("select[name=\''.$field_name.'\']" ).prop("disabled", false); }); </script> ';

                    break;
            }
            $out .= '</div>';
            return $out;
        } else {
            return "";
        }
    }
    /**
     * Print input field enclosed within form.
     *
     * Uses blade syntax @la_input_lang('name')
     *
     * @param $module Module Object
     * @param $field_name Field Name for which input has be created
     * @param null $default_val Default Value of Field. This will override default value from context.
     * @param null $required2 Is this field mandatory.
     * @param string $class Custom css class. Default would be bootstrap 'form-control' class
     * @param array $params Additional Parameters for Customization
     * @return string This return html string with field inputs
     */
    public static function input_lang($module, $field_name, $language = [], $default_val = null, $required2 = null, $class = 'form-control', $params = [])
    {
        // Check Field Write Aceess
        if(Module::hasFieldAccess($module->id, $module->fields[$field_name]['id'], $access_type = "write") and !empty($field_name)) {

            $row = null;
            $default_language = config('app.locale_id'); //dd($default_language);
            if(isset($module->row[$language->id])) {
                $row = $module->row[$language->id];
                if(isset($module->row[$default_language]))
                    $row_default = $module->row[$default_language];
                else {
                    $row_default = $row;
                }
            } else {
                if(isset($module->row[$default_language]))
                    $row_default = $module->row[$default_language];
                else if (!empty($module->row))
                    foreach ($module->row as $value_default) {
                        $row_default = $value_default;
                        break;
                    }

            }
//dd($row);



            //print_r($module->fields);
            $label = $module->fields[$field_name]['label'];
            $field_type = $module->fields[$field_name]['field_type'];
            $unique = $module->fields[$field_name]['unique'];
            $defaultvalue = $module->fields[$field_name]['defaultvalue'];
            $minlength = $module->fields[$field_name]['minlength'];
            $maxlength = $module->fields[$field_name]['maxlength'];
            $required = $module->fields[$field_name]['required'];
            $popup_vals = $module->fields[$field_name]['popup_vals'];
            $lang_active_vals = $module->fields[$field_name]['lang_active'];
            $field_name_lang = $field_name.'['.$language->id.']';

            $class_active_lang = null;

            if($required2 != null) {
                $required = $required2;
            }

            $field_type = ModuleFieldTypes::find($field_type);

            if($field_type->name == 'Image' or $field_type->name == 'Files'){
                $field_name_lang = $field_name.'_lang_'.$language->id;
            }


            $out = '<div class="form-group" id="' . $field_name . '">';
            $required_ast = "";

            if(!isset($params['class'])) {
                $params['class'] = $class;
            }
            if(!isset($params['placeholder'])) {
                $params['placeholder'] = 'Enter ' . $label;
            }
            if(isset($minlength)) {
                $params['data-rule-minlength'] = $minlength;
            }
            if(isset($maxlength)) {
                $params['data-rule-maxlength'] = $maxlength;
            }
            if($unique && !isset($params['unique'])) {
                $params['data-rule-unique'] = "true";
                $params['field_id'] = $module->fields[$field_name]['id'];
                $params['adminRoute'] = config('crmadmin.adminRoute');
                if(isset($row)) {
                    $params['isEdit'] = true;
                    $params['row_id'] = $row->id;
                } else {
                    $params['isEdit'] = false;
                    $params['row_id'] = 0;
                }
                $out .= '<input type="hidden" name="_token_' . $module->fields[$field_name]['id'] . '" value="' . csrf_token() . '">';
            }

            if($required && !isset($params['required'])) {
                $params['required'] = $required;
                $required_ast = "*";
            }

//            if($language->id == 2 and $lang_active_vals != 1)
//                dd($row_default->$field_name);

            //Gán giá trị không thay đổi với ngôn ngữ mặc định
            if($lang_active_vals != 1 and !empty($row_default->$field_name) ){ //$language->id != $default_language and

                if($field_name != 'locale' and $field_name != 'local_parent')
                    $default_val = $row_default->$field_name;
                else if($field_name == 'locale') {
                    $default_val = $language->id;
                    $out .= '<input type="hidden" value="'.$language->id.'" name="locale['.$language->id.']">';
                    if(!empty($module->row[$language->id]->id))
                        $out .= '<input type="hidden" value="'.$module->row[$language->id]->id.'" name="id['.$language->id.']">';
                } else //if($field_name == 'local_parent')
                {
                    $default_val = $row_default->id;
                }
                if($language->id != $default_language)
                    $params['class'] = 'form-control active_lang';

            }
            else if($field_name == 'local_parent')  {
                if(isset($row_default->id))
                    $default_val = $row_default->id;
                if($language->id != $default_language)
                    $params['class'] = 'form-control active_lang';
            }

            //Trường hợp thêm mới
            if(empty($row_default)) {
                if($field_name == 'locale') {
                    $default_val = $language->id;
                    $params['class'] = 'form-control active_lang';
                    $out .= '<input type="hidden" value="'.$language->id.'" name="locale['.$language->id.']">';
                }
                if($language->id != $default_language and $lang_active_vals != 1)
                    $params['class'] = 'form-control active_lang';

            }



            switch($field_type->name) {
                case 'Address':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }

                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }

                    $params['cols'] = 30;
                    $params['rows'] = 3;
                    $out .= Form::textarea($field_name , $default_val, $params);
                    break;
                case 'Checkbox':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';
                    $out .= '<input type="hidden" value="false" name="' . $field_name . '_hidden[]">';

                    // ############### Remaining
                    unset($params['placeholder']);
                    unset($params['data-rule-maxlength']);

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }

                    $out .= Form::checkbox($field_name_lang, $field_name, $default_val, $params);
                    $out .= '<div class="Switch Round On" style="vertical-align:top;margin-left:10px;"><div class="Toggle"></div></div>';
                    break;
                case 'Currency':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }

                    if($params['data-rule-maxlength'] != "" && $params['data-rule-maxlength'] != 0) {
                        $params['max'] = $params['data-rule-maxlength'];
                    }
                    if($params['data-rule-minlength'] != "" && $params['data-rule-minlength'] != 0) {
                        $params['min'] = $params['data-rule-minlength'];
                    }

                    unset($params['data-rule-minlength']);
                    unset($params['data-rule-maxlength']);

                    $params['data-rule-currency'] = "true";
                    $params['min'] = "0";
                    $out .= Form::number($field_name_lang, $default_val, $params);
                    break;
                case 'Date':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }
                    $dval = $default_val;
                    $is_null = "";
                    if($default_val == "NULL") {
                        $is_null = " checked";
                        $params['readonly'] = "";
                    } else if($default_val != "") {
                        $dval = date("d/m/Y", strtotime($default_val));
                    }

                    unset($params['data-rule-maxlength']);
                    // $params['data-rule-date'] = "true";

                    $out .= "<div class='input-group date'>";
                    $out .= Form::text($field_name_lang, $dval, $params);
                    $out .= "<span class='input-group-addon input_dt'><span class='fa fa-calendar'></span></span><span class='input-group-addon null_date'><input class='cb_null_date' type='checkbox' name='null_date_" . $field_name . "' $is_null value='true'> Null ?</span></div>";
                    // $out .= Form::date($field_name, $default_val, $params);
                    break;
                case 'Datetime':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }

                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }

                    $is_null = "";
                    if($default_val == "NULL") {
                        $is_null = " checked";
                        $params['readonly'] = "";
                    } else if($default_val == null) {
                        $default_val = $defaultvalue;
                    }

                    // ############### Remaining
                    $dval = $default_val;
                    if($default_val == "now()") {
                        $dval = date("d/m/Y h:i A");
                    } else if($default_val != NULL && $default_val != "" && $default_val != "NULL") {
                        $dval = date("d/m/Y h:i A", strtotime($default_val));
                    }
                    $out .= "<div class='input-group datetime'>";
                    $out .= Form::text($field_name_lang, $dval, $params);
                    $out .= "<span class='input-group-addon input_dt'><span class='fa fa-calendar'></span></span><span class='input-group-addon null_date'><input class='cb_null_date' type='checkbox' name='null_date_" . $field_name . "' $is_null value='true'> Null ?</span></div>";
                    break;
                case 'Decimal':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }

                    if($params['data-rule-maxlength'] != "" && $params['data-rule-maxlength'] != 0) {
                        $params['max'] = $params['data-rule-maxlength'];
                    }
                    if($params['data-rule-minlength'] != "" && $params['data-rule-minlength'] != 0) {
                        $params['min'] = $params['data-rule-minlength'];
                    }

                    unset($params['data-rule-minlength']);
                    unset($params['data-rule-maxlength']);

                    $out .= Form::number($field_name_lang, $default_val, $params);
                    break;


                case 'Dropdown':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    unset($params['data-rule-minlength']);
                    unset($params['data-rule-maxlength']);
                    $params['data-placeholder'] = $params['placeholder'];
                    unset($params['placeholder']);
                    $params['rel'] = "select2";

                    //echo $defaultvalue;
                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && $row->$field_name) {
                        $default_val = $row->$field_name;
                    } else if($default_val == NULL || $default_val == "" || $default_val == "NULL") {
                        // When Adding Record if we dont have default value let's not show NULL By Default
                        if($popup_vals == '@users'){
                            $default_val = Auth::user()->id;
                        } else {
                            $default_val = "0";
                        }
                    }

                    // Bug here - NULL value Item still shows Not null in Form
                    if($default_val == NULL) {
                        $params['disabled'] = "";
                    }


                    $popup_vals_str = $popup_vals;
                    $url = Request::url();
//                        $url_array = explode("categories", $url);
                    $url_array = explode("/", $url);
                    if(is_string($popup_vals) && Str::startsWith($popup_vals, '@') && $popup_vals == "@categories" && $url_array[count($url_array) - 1] != "categories") {

                        if(is_numeric($url_array[count($url_array) - 1]) || ( is_numeric($url_array[count($url_array) - 2]) and  $url_array[count($url_array) - 1] == 'edit' )){

                            $id_cat = is_numeric($url_array[count($url_array) - 2]) ? $url_array[count($url_array) - 2]:$url_array[count($url_array) - 1];

                            if(is_numeric($url_array[count($url_array) - 2])) {
                                $tb_name = substr( $url_array[count($url_array) - 3],  0, strlen($url_array[count($url_array) - 3]) - 5 );
                                $item_info = \DB::table($tb_name)->where('id',$url_array[count($url_array) - 2])->first();
//                                dd($item_info);
                                $id_cat = $item_info->categories_id;
                            }


                        } else {
                            //Lấy danh sách module
                            $module_table = \DB::table('module_tables')->pluck('module_table', 'id');
                            $module_table_item = array();
                            foreach ($module_table as $key => $value) {

                                if(strpos($url, $value)){
                                    $module_table_item[] = $key;
                                }

                            }

                            $id_cat = 0;

                        }

                        // Get Module / Table Name
                        $json = str_ireplace("@", "", $popup_vals);
                        $table_name = strtolower(Str::plural($json));
                        // Search Module
                        $module = Module::getByTable($table_name);
                        $module_table_item = array();
                        if(!empty($id_cat)){
                            $cat_info = \DB::table($table_name)->where('id',$id_cat)->get();
                            $module_table_item[] = $cat_info[0]->module_table_id;

                            //Lấy danh sách module
                            $module_table = \DB::table('module_tables')->pluck('module_table', 'id');
                            foreach ($module_table as $key => $value) {

                                if($module_table[$cat_info[0]->module_table_id] == $value ){
                                    $module_table_item[] = $key;
                                }

                            }

                        }

                        if(!empty($module_table_item))
                            $categories = \DB::table($table_name)->where("status", 1)->where("deleted_at", null)->whereIn('module_table_id',$module_table_item)->get();
                        else
                            $categories = \DB::table($table_name)->where("status", 1)->where("deleted_at", null)->get();

                        $popup_vals = LAFormMaker::showCategories($categories, count($categories), $id_cat);
                        $result_name = array();
                        if(empty($cat_info)){
                            $result_name[0] = 'Chọn nhóm';
                        } else {
                            $result_name[0] = 'Chọn nhóm';
                            if(!empty($popup_vals))
                                $result_name[''.$cat_info[0]->name] = [];
                            else $result_name[$cat_info[0]->id] = $cat_info[0]->name;
                        }
                        foreach ($popup_vals as $key =>$value ){
                            $string = '';
                            for($i=0; $i <= $value->level; $i++){
                                $string = $string.'&nbsp; &nbsp;';
                            }
                            if($key < 1){
                                $result_name[$value->id] = $string.$string.$value->name_cat;
                            }
                            else if (isset($value->stt) and strpos($value->stt, $popup_vals[$key-1]->stt) !== false and $value->stt != $popup_vals[$key-1]->stt ) {
                                if($value->level > 1){
                                    $result_name['&nbsp; &nbsp; &nbsp; '.$string.$popup_vals[$key-1]->name] = [];
                                } else
                                    $result_name[' '.$string.$popup_vals[$key-1]->name] = [];
                                unset($result_name[$popup_vals[$key-1]->id]);
                                $result_name[$value->id] =  ' '.$string.$string.$value->name_cat;
                            } else {
                                if($value->level > 1) {
                                    $result_name[$value->id] = $string . $string . $value->name_cat;
                                } else  $result_name[$value->id] = '&nbsp; '.$string . $string . $value->name_cat;
                            }
                            $string = '';
                        }

                        $popup_vals = $result_name;

                    }
                    else {
                        if ($popup_vals != "") {
//                            $popup_vals = LAFormMaker::process_values($popup_vals);
                            $popup_vals = LAFormMaker::process_values($popup_vals, $default_language,$language->id);
                        } else {
                            $popup_vals = array();
                        }

                        $popup_vals[0] = "None";
                        ksort($popup_vals);
                    }
                    $out .= Form::select($field_name_lang, $popup_vals, $default_val, $params);

                    break;

                case 'DropdownCat':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    unset($params['data-rule-minlength']);
                    unset($params['data-rule-maxlength']);
                    $params['data-placeholder'] = $params['placeholder'];
                    unset($params['placeholder']);
                    $params['rel'] = "select2";

                    //echo $defaultvalue;
                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && $row->$field_name) {
                        $default_val = $row->$field_name;
                    } else if($default_val == NULL || $default_val == "" || $default_val == "NULL") {
                        // When Adding Record if we dont have default value let's not show NULL By Default
                        $default_val = "0";
                    }

                    // Bug here - NULL value Item still shows Not null in Form
                    if($default_val == NULL) {
                        $params['disabled'] = "";
                    }

                    $popup_vals_str = $popup_vals;
                    if($popup_vals != "") {
                        $popup_vals = LAFormMaker::process_values($popup_vals, $default_language, $language->id);
                    } else {
                        $popup_vals = array();
                    }

                    $popup_vals[0] = "None";
                    ksort($popup_vals);
                    $out .= Form::select($field_name_lang, $popup_vals, $default_val, $params);

                    break;

                case 'Email':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }

                    $params['data-rule-email'] = "true";
                    $out .= Form::email($field_name_lang, $default_val, $params);
                    break;
                case 'File':
                    $out .= '<label for="' . $field_name . '" style="display:block;">' . $label . $required_ast . ' :</label>';

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }
                    if(!is_numeric($default_val)) {
                        $default_val = 0;
                    }
                    $out .= Form::hidden($field_name_lang, $default_val, $params);

                    if($default_val != 0) {
                        $upload = \App\Models\Upload::find($default_val);
                    }
                    if(isset($upload->id)) {
                        $out .= "<a class='btn btn-default btn_upload_file hide' file_type='file' selecter='" . $field_name_lang . "'>Upload <i class='fa fa-cloud-upload'></i></a>
                            <a class='uploaded_file' target='_blank' href='" . url("files/" . $upload->hash . DIRECTORY_SEPARATOR . $upload->name) . "'><i class='fa fa-file-o'></i><i title='Remove File' class='fa fa-times'></i></a>";
                    } else {
                        $out .= "<a class='btn btn-default btn_upload_file' file_type='file' selecter='" . $field_name_lang . "'>Upload <i class='fa fa-cloud-upload'></i></a>
                            <a class='uploaded_file hide' target='_blank'><i class='fa fa-file-o'></i><i title='Remove File' class='fa fa-times'></i></a>";
                    }
                    break;

                case 'Files':
                    $out .= '<label for="' . $field_name . '" style="display:block;">' . $label . $required_ast . ' :</label>';

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }
                    if(is_array($default_val)) {
                        $default_val = json_encode($default_val);
                    }

                    $default_val_arr = json_decode($default_val);

                    if(is_array($default_val_arr) && count($default_val_arr) > 0) {
                        $uploadIds = array();
                        $uploadImages = "";
                        foreach($default_val_arr as $uploadId) {
                            $upload = \App\Models\Upload::find($uploadId);
                            if(isset($upload->id)) {
                                $uploadIds[] = $upload->id;
                                $fileImage = "";
                                if(in_array($upload->extension, ["jpg", "png", "gif", "jpeg"])) {
                                    $fileImage = "<img src='" . url("files/" . $upload->hash . DIRECTORY_SEPARATOR . $upload->name . "?s=90") . "'>";
                                } else {
                                    $fileImage = "<i class='fa fa-file-o'></i>";
                                }
                                $uploadImages .= "<a class='uploaded_file2' upload_id='" . $upload->id . "' target='_blank' href='" . url("files/" . $upload->hash . DIRECTORY_SEPARATOR . $upload->name) . "'>" . $fileImage . "<i title='Remove File' class='fa fa-times'></i></a>";
                            }
                        }

                        $out .= Form::hidden($field_name_lang, json_encode($uploadIds), $params);
                        if(count($uploadIds) > 0) {
                            $out .= "<div class='uploaded_files'>" . $uploadImages . "</div>";
                        }
                    } else {
                        $out .= Form::hidden($field_name_lang, "[]", $params);
                        $out .= "<div class='uploaded_files'></div>";
                    }
                    $out .= "<a class='btn btn-default btn_upload_files' file_type='files' selecter='" . $field_name_lang . "' style='margin-top:5px;'>Upload <i class='fa fa-cloud-upload'></i></a>";
                    break;

                case 'Float':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }

//                    if($params['data-rule-maxlength'] != "" && $params['data-rule-maxlength'] != 0) {
//                        $params['max'] = $params['data-rule-maxlength'];
//                    }
//                    if($params['data-rule-minlength'] != "" && $params['data-rule-minlength'] != 0) {
//                        $params['min'] = $params['data-rule-minlength'];
//                    }

//                    unset($params['data-rule-minlength']);
//                    unset($params['data-rule-maxlength']);

                    $out .= Form::number($field_name_lang, $default_val, $params);
                    break;
                case 'HTML':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }
                    $params['class'] = 'htmlbox';
                    $out .= Form::textarea($field_name_lang, $default_val, $params);
                    break;
                case 'Image':
                    $out .= '<label for="' . $field_name . '" style="display:block;">' . $label . $required_ast . ' :</label>';

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }
                    if(!is_numeric($default_val)) {
                        $default_val = 0;
                    }
                    $params['class'] = 'form-control '.$field_name_lang;
                    $out .= Form::hidden($field_name_lang, $default_val, $params);

                    if($default_val != 0) {
                        $upload = \App\Models\Upload::find($default_val);
                    }
                    if(isset($upload->id)) {
                        $path = explode("/",$upload->path);
                        $img_name = $path[count($path) - 1];
                        $date_append = substr($img_name, 2, 15 );
//                        $out .= "<a class='btn btn-default btn_upload_image hide' file_type='image' selecter='" . $field_name . "'>Upload <i class='fa fa-cloud-upload'></i></a>
//                            <div class='uploaded_image'><img src='" . url("files/" . $upload->hash . DIRECTORY_SEPARATOR . $upload->name . "?s=150") . "'><i title='Remove Image' class='fa fa-times'></i></div>";
                        $out .= "<a class='btn btn-default btn_upload_image hide' file_type='image' selecter='" . $field_name_lang . "'>Upload <i class='fa fa-cloud-upload'></i></a>
                            <div class='uploaded_image'><img src='" . url("/s80x80/$upload->caption/$date_append/$upload->name") . "'><i title='Remove Image' class='fa fa-times'></i></div>";

                    } else {
                        $out .= "<a class='btn btn-default btn_upload_image' file_type='image' selecter='" . $field_name_lang . "'>Upload <i class='fa fa-cloud-upload'></i></a>
                            <div class='uploaded_image hide'><img src=''><i title='Remove Image' class='fa fa-times'></i></div>";
                    }

                    break;
                case 'Integer':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

//                    if($params['data-rule-maxlength'] != "" && $params['data-rule-maxlength'] != 0) {
//                        $params['max'] = $params['data-rule-maxlength'];
//                    }
//                    if($params['data-rule-minlength'] != "" && $params['data-rule-minlength'] != 0) {
//                        $params['min'] = $params['data-rule-minlength'];
//                    }
//
//                    unset($params['data-rule-minlength']);
//                    unset($params['data-rule-maxlength']);

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }
                    // $params['min'] = "0"; // Required for Non-negative numbers
                    $out .= Form::number($field_name_lang, $default_val, $params);
                    break;
                case 'Mobile':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }

                    $out .= Form::text($field_name_lang, $default_val, $params);
                    break;
                case 'Multiselect':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    unset($params['data-rule-maxlength']);
                    $params['data-placeholder'] = "Select multiple " . Str::plural($label);
                    unset($params['placeholder']);
                    $params['multiple'] = "true";
                    $params['rel'] = "select2";
                    if($default_val == null) {
                        if($defaultvalue != "") {
                            $default_val = json_decode($defaultvalue);
                        } else {
                            $default_val = "";
                        }
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = json_decode($row->$field_name);
                    }

                    if($popup_vals != "") {
                        $popup_vals = LAFormMaker::process_values($popup_vals, $default_language, $language->id);
                    } else {
                        $popup_vals = array();
                    }

                    $out .= Form::select($field_name_lang . "[]", $popup_vals, $default_val, $params);
                    break;
                case 'Name':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }

                    $out .= Form::text($field_name_lang, $default_val, $params);
                    break;
                case 'Password':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    $out .= Form::password($field_name_lang, $params);
                    break;
                case 'Radio':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' : </label><br>';

                    // ############### Remaining
                    unset($params['placeholder']);
                    unset($params['data-rule-maxlength']);

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }

                    if(Str::startsWith($popup_vals, '@')) {
                        $popup_vals = LAFormMaker::process_values($popup_vals, $default_language, $language->id);
                        $out .= '<div class="radio">';
                        foreach($popup_vals as $key => $value) {
                            $sel = false;
                            if($default_val != "" && $default_val == $value) {
                                $sel = true;
                            }
                            $out .= '<label>' . (Form::radio($field_name, $key, $sel)) . ' ' . $value . ' </label>';
                        }
                        $out .= '</div>';
                        break;
                    } else {
                        if($popup_vals != "") {
                            $popup_vals = array_values(json_decode($popup_vals));
                        } else {
                            $popup_vals = array();
                        }
                        $out .= '<div class="radio">';
                        foreach($popup_vals as $value) {
                            $sel = false;
                            if($default_val != "" && $default_val == $value) {
                                $sel = true;
                            }
                            $out .= '<label>' . (Form::radio($field_name_lang, $value, $sel)) . ' ' . $value . ' </label>';
                        }
                        $out .= '</div>';
                        break;
                    }
                case 'String':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }
                    $out .= Form::text($field_name_lang, $default_val, $params);
                    break;
                case 'Taginput':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    if(isset($params['data-rule-maxlength'])) {
                        $params['maximumSelectionLength'] = $params['data-rule-maxlength'];
                        unset($params['data-rule-maxlength']);
                    }
                    $params['multiple'] = "true";
                    $params['rel'] = "taginput";
                    $params['data-placeholder'] = "Add multiple " . Str::plural($label);
                    unset($params['placeholder']);

                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = json_decode($row->$field_name);
                    }

                    if($default_val == null) {
                        $defaultvalue2 = json_decode($defaultvalue);
                        if(is_array($defaultvalue2)) {
                            $default_val = $defaultvalue;
                        } else if(is_string($defaultvalue)) {
                            if(strpos($defaultvalue, ',') !== false) {
                                $default_val = array_map('trim', explode(",", $defaultvalue));
                            } else {
                                $default_val = [$defaultvalue];
                            }
                        } else {
                            $default_val = array();
                        }
                    }
                    $default_val = LAFormMaker::process_values($default_val, $default_language, $language->id);
                    $out .= Form::select($field_name_lang . "[]", $default_val, $default_val, $params);
                    break;
                case 'Textarea':
                    $out .= '<div class="textTinymce1">';
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    $params['cols'] = 30;
                    $params['rows'] = 6;


                    if($params['data-rule-maxlength'])
                        unset($params['data-rule-maxlength']);

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }

                    $out .= Form::textarea($field_name_lang, $default_val, $params);
                    $out .= '</div>';
                    break;
                case 'TextField':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }

                    $out .= Form::text($field_name_lang, $default_val, $params);
                    break;


                //Text Editor
                case 'Text':
                    $out .= '<div class="textTinymce">';
                    $out .= '<label for="'.$field_name.'">'.$label.$required_ast.' :</label>';
                    unset($params['data-rule-minlength']);
                    unset($params['data-rule-maxlength']);
                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }

                    $params['cols'] = 30;
                    $params['rows'] = 15;
                    $out .= Form::textarea($field_name_lang, $default_val, $params);
//                    $out .= '<script> tinymce.init({ selector: "textarea[name='.$field_name.']" }) </script> ';
                    $out .= '</div>';
                    break;


                //Longtext Editor
                case 'LongText':
                    $out .= '<div class="textTinymce">';
                    $out .= '<label for="'.$field_name.'">'.$label.$required_ast.' :</label>';
                    unset($params['data-rule-minlength']);
                    unset($params['data-rule-maxlength']);
                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }

                    $params['cols'] = 30;
                    $params['rows'] = 15;
                    $out .= Form::textarea($field_name_lang, $default_val, $params);
//                    $out .= '<script> tinymce.init({ selector: "textarea[name='.$field_name.']" }) </script> ';
                    $out .= '</div>';
                    break;

                case 'URL':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    if($default_val == null) {
                        $default_val = $defaultvalue;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }

                    $params['data-rule-url'] = "true";
                    $out .= Form::text($field_name_lang, $default_val, $params);
                    break;


                case 'UsersCreated1':
                    $out .= '<label for="'.$field_name.'">'.$label.$required_ast.' :</label>';

                    if($default_val == null) {
                        $default_val =  Auth::user()->id;
                    }
                    // Override the edit value
                    if(isset($row) && isset($row->$field_name)) {
                        $default_val = $row->$field_name;
                    }

                    $out .= Form::hidden($field_name_lang, $default_val, $params);
                    break;


                case 'UsersCreated':
                    $out .= '<label for="' . $field_name . '">' . $label . $required_ast . ' :</label>';

                    unset($params['data-rule-minlength']);
                    unset($params['data-rule-maxlength']);
                    $params['data-placeholder'] = $params['placeholder'];
                    unset($params['placeholder']);
                    $params['rel'] = "select2";

                    //echo $defaultvalue;
//                    if($default_val == null) {
//                        $default_val = $defaultvalue;
//                    }

                    if($default_val == null) {
                        $default_val =  Auth::user()->id;
                    }

                    // Override the edit value
                    if(isset($row) && $row->$field_name) {
                        $default_val = $row->$field_name;
                    } else if($default_val == NULL || $default_val == "" || $default_val == "NULL") {
                        // When Adding Record if we dont have default value let's not show NULL By Default
                        $default_val = "0";
                    }

                    // Bug here - NULL value Item still shows Not null in Form
                    if($default_val == NULL) {
                        $params['disabled'] = "";
                    }

                    $popup_vals_str = $popup_vals;
                    if($popup_vals != "") {
                        $popup_vals = LAFormMaker::process_values($popup_vals, $default_language, $language->id);
                    } else {
                        $popup_vals = array();
                    }

                    if(!$required) {
                        array_unshift($popup_vals, "None");
                    }
                    $out .= Form::select($field_name_lang, $popup_vals, $default_val, $params);
                    $out .= '<script> $("select[name=\''.$field_name.'\']").prop("disabled", \'true\'); $(\'form\').on(\'submit\', function() { $("select[name=\''.$field_name.'\']" ).prop("disabled", false); }); </script> ';

                    break;
            }
            $out .= '</div>';
            return $out;
        } else {
            return "";
        }
    }

    /**
     * Processes the populated values for Multiselect / Taginput / Dropdown
     * get data from module / table whichever is found if starts with '@'
     **/
    // $values = LAFormMaker::process_values($data);
    public static function process_values($json, $language_default = null, $language_id = null)
    {
        $out = array();
        // Check if populated values are from Module or Database Table
        if(is_string($json) && Str::startsWith($json, '@')) {

            // Get Module / Table Name
            $json = str_ireplace("@", "", $json);
            $table_name = strtolower(Str::plural($json));
            // Search Module
            $module = Module::getByTable($table_name, $language_default, $language_id);
            if(isset($module->id)) {
                $out = Module::getDDArray($module->name, $language_default, $language_id); // if($table_name == 'properties_cats' and $language_id == 2 ) dd($out);
            } else {
                // Search Table if no module found
                if(Schema::hasTable($table_name)) {
                    if(file_exists(resource_path('app/Models/' . ucfirst(Str::singular($table_name) . ".php")))) {
                        $model = "App\\Models\\" . ucfirst(Str::singular($table_name));
                        $result = $model::all();
                    } else {
                        $result = \DB::table($table_name)->get();
                    }
                    // find view column name
                    $view_col = "";
                    // Check if atleast one record exists
                    if(isset($result[0])) {
                        $view_col_test_1 = "name";
                        $view_col_test_2 = "title";
                        if(isset($result[0]->$view_col_test_1)) {
                            // Check whether view column name == "name"
                            $view_col = $view_col_test_1;
                        } else if(isset($result[0]->$view_col_test_2)) {
                            // Check whether view column name == "title"
                            $view_col = $view_col_test_2;
                        } else {
                            // retrieve the second column name which comes after "id"
                            $arr2 = $result[0]->toArray();
                            $arr2 = array_keys($arr2);
                            $view_col = $arr2[1];
                            // if second column not exists
                            if(!isset($result[0]->$view_col)) {
                                $view_col = "";
                            }
                        }
                        // If view column name found successfully through all above efforts
                        if($view_col != "") {
                            // retrieve rows of table
                            foreach($result as $row) {
                                $out[$row->id] = $row->$view_col;
                            }
                        } else {
                            // Failed to find view column name
                        }
                    } else {
                        // Skipped efforts to detect view column name
                    }
                } else if(Schema::hasTable($json)) {
                    // $array = \DB::table($table_name)->get();
                }
            }
        } else if(is_string($json)) {
            $array = json_decode($json);
            if(is_array($array)) {
                foreach($array as $value) {
                    $out[$value] = $value;
                }
            } else {
                // TODO: Check posibility of comma based pop values.
            }
        } else if(is_array($json)) {
            foreach($json as $value) {
                $out[$value] = $value;
            }
        }
        return $out;
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
//                if ($item->parent == null ) {
//                    $item->level = $level;
//                    $item->name_cat = $char . $item->name;
//                    $item->stt = ($item->id < 10) ? '0'.$parent_id : $parent_id;
//                    $result[$item->id] = $item;
//                }
            }

            if ($item->parent == $parent_id  )
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
//else {
//                $item->level        = $level;
//                $item->name_cat     = $char.$item->name;
//                $item->stt    = $item->hierarchy;
//                $result[$item->id] = $item;
//            }
            if(!empty($item->id_parent)){
                $result[$item->id] = $item;
                $stt1 = $stt1+1;
            }
            if ($item->parent == $parent_id)
            {
                LAFormMaker::showCategories($categories, $count, $item->id, $char, $result, $level + 1, $stt1, $idchar, $hierarchy, $hierarchy,   $dem, $children); // . '&nbsp; &nbsp; &nbsp; &nbsp; '
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
                if(isset($value->name_cat))
                    $result_name[$value->id] = $value->name_cat;
                else $result_name[$value->id] = $value->name;
            }
//            foreach ($result as $ke=>$val) {
//                echo  $val->name_cat.'-'.$val->stt.'<br/>';
//            }
//            return $result_name;
            return $result;
        }


    }


    /**
     * Display field is CRUDs View show.blade.php with Label
     *
     * Uses blade syntax @la_display('name')
     *
     * @param $module Module Object
     * @param $field_name Field Name for which display has be created
     * @param string $class Custom css class. Default would be bootstrap 'form-control' class
     * @return string This return html string with field display with Label
     */
    public static function display($module, $field_name, $class = 'form-control')
    {
        // Check Field View Access
        if(Module::hasFieldAccess($module->id, $module->fields[$field_name]['id'], $access_type = "view")) {

            $fieldObj = $module->fields[$field_name];
            $label = $module->fields[$field_name]['label'];
            $field_type = $module->fields[$field_name]['field_type'];
            $field_type = ModuleFieldTypes::find($field_type);

            $row = null;
            if(isset($module->row)) {
                $row = $module->row;
            }

            $out = '<div class="form-group"  id="' . $field_name . '">';
            $out .= '<label for="' . $field_name . '" class="col-md-4 col-sm-6 col-xs-6">' . $label . ' :</label>';

            $value = $row->$field_name;

            switch($field_type->name) {
                case 'Address':
                    if($value != "") {
                        $value = $value . '<a target="_blank" class="pull-right btn btn-xs btn-primary btn-circle" href="http://maps.google.com/?q=' . $value . '" data-toggle="tooltip" data-placement="left" title="Check location on Map"><i class="fa fa-map-marker"></i></a>';
                    }
                    break;
                case 'Checkbox':

                    if($value == 0) {
                        $value = "<div class='label label-danger'>False</div>";
                    } else {
                        $value = "<div class='label label-success'>True</div>";
                    }
                    break;
                case 'Currency':

                    break;
                case 'Date':
                    if($value == NULL) {
                        $value = "Not Available";
                    } else {
                        $dt = strtotime($value);
                        $value = date("d M Y", $dt);
                    }
                    break;
                case 'Datetime':
                    if($value == NULL) {
                        $value = "Not Available";
                    } else {
                        $dt = strtotime($value);
                        $value = date("d M Y, h:i A", $dt);
                    }
                    break;
                case 'Decimal':

                    break;
                case 'Dropdown':
                    $values = LAFormMaker::process_values($fieldObj['popup_vals']);

                    if(Str::startsWith($fieldObj['popup_vals'], '@')) {
                        if($value != 0) {
                            $moduleVal = Module::getByTable(str_replace("@", "", $fieldObj['popup_vals']));
                            if(isset($moduleVal->id)) {
                                $value = "<a href='" . url(config("crmadmin.adminRoute") . "/" . $moduleVal->name_db . "/" . $value) . "' class='label label-primary'>" . $values[$value] . "</a> ";
                            } else {
                                $value = "<a class='label label-primary'>" . $values[$value] . "</a> ";
                            }
                        } else {
                            $value = "None";
                        }
                    }
                    break;
                case 'Email':
                    $value = '<a href="mailto:' . $value . '">' . $value . '</a>';
                    break;
                case 'File':
                    if($value != 0 && $value != "0") {
                        $upload = \App\Models\Upload::find($value);
                        if(isset($upload->id)) {
                            $value = '<a class="preview" target="_blank" href="' . url("files/" . $upload->hash . DIRECTORY_SEPARATOR . $upload->name) . '">
                            <span class="fa-stack fa-lg"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-file-o fa-stack-1x fa-inverse"></i></span> ' . $upload->name . '</a>';
                        } else {
                            $value = 'Uploaded file not found.';
                        }
                    } else {
                        $value = 'No file';
                    }
                    break;
                case 'Files':
                    if($value != "" && $value != "[]" && $value != "null" && starts_with($value, "[")) {
                        $uploads = json_decode($value);
                        $uploads_html = "";

                        foreach($uploads as $uploadId) {
                            $upload = \App\Models\Upload::find($uploadId);
                            if(isset($upload->id)) {
                                $uploadIds[] = $upload->id;
                                $fileImage = "";
                                if(in_array($upload->extension, ["jpg", "png", "gif", "jpeg"])) {
                                    $fileImage = "<img src='" . url("files/" . $upload->hash . DIRECTORY_SEPARATOR . $upload->name . "?s=90") . "'>";
                                } else {
                                    $fileImage = "<i class='fa fa-file-o'></i>";
                                }
                                // $uploadImages .= "<a class='uploaded_file2' upload_id='".$upload->id."' target='_blank' href='".url("files/".$upload->hash.DIRECTORY_SEPARATOR.$upload->name)."'>".$fileImage."<i title='Remove File' class='fa fa-times'></i></a>";
                                $uploads_html .= '<a class="preview" target="_blank" href="' . url("files/" . $upload->hash . DIRECTORY_SEPARATOR . $upload->name) . '" data-toggle="tooltip" data-placement="top" data-container="body" style="display:inline-block;margin-right:5px;" title="' . $upload->name . '">
                                        ' . $fileImage . '</a>';
                            }
                        }
                        $value = $uploads_html;
                    } else {
                        $value = 'No files found.';
                    }
                    break;
                case 'Float':

                    break;
                case 'HTML':
                    break;
                case 'Image':
                    if($value != 0 && $value != "0") {
                        $upload = \App\Models\Upload::find($value);
                        if(isset($upload->id)) {
                            $value = '<a class="preview" target="_blank" href="' . url("files/" . $upload->hash . DIRECTORY_SEPARATOR . $upload->name) . '"><img src="' . url("files/" . $upload->hash . DIRECTORY_SEPARATOR . $upload->name . "?s=150") . '"></a>';
                        } else {
                            $value = 'Uploaded image not found.';
                        }
                    } else {
                        $value = 'No Image';
                    }
                    break;
                case 'Integer':

                    break;
                case 'Mobile':
                    $value = '<a target="_blank" href="tel:' . $value . '">' . $value . '</a>';
                    break;
                case 'Multiselect':
                    $valueOut = "";
                    $values = LAFormMaker::process_values($fieldObj['popup_vals']);
                    if(count($values)) {
                        if(Str::startsWith($fieldObj['popup_vals'], '@')) {
                            $moduleVal = Module::getByTable(str_replace("@", "", $fieldObj['popup_vals']));
                            $valueSel = json_decode($value);
                            foreach($values as $key => $val) {
                                if(in_array($key, $valueSel)) {
                                    $module_link = "";
                                    if(isset($moduleVal->id)) {
                                        $module_link = "href='" . url(config("crmadmin.adminRoute") . "/" . $moduleVal->name_db . "/" . $key) . "'";
                                    }
                                    $valueOut .= "<a $module_link class='label label-primary'>" . $val . "</a> ";
                                }
                            }
                        } else {
                            $valueSel = json_decode($value);
                            foreach($values as $key => $val) {
                                if(in_array($key, $valueSel)) {
                                    $valueOut .= "<span class='label label-primary'>" . $val . "</span> ";
                                }
                            }
                        }
                    }
                    $value = $valueOut;
                    break;
                case 'Name':

                    break;
                case 'Password':
                    $value = '<a href="#" data-toggle="tooltip" data-placement="top" data-container="body" title="Cannot be declassified !!!">********</a>';
                    break;
                case 'Radio':

                    break;
                case 'String':

                    break;
                case 'Taginput':
                    $valueOut = "";
                    $values = LAFormMaker::process_values($fieldObj['popup_vals']);
                    if(count($values)) {
                        if(Str::startsWith($fieldObj['popup_vals'], '@')) {
                            $moduleVal = Module::getByTable(str_replace("@", "", $fieldObj['popup_vals']));
                            $valueSel = json_decode($value);
                            foreach($values as $key => $val) {
                                if(in_array($key, $valueSel)) {
                                    $valueOut .= "<a href='" . url(config("crmadmin.adminRoute") . "/" . $moduleVal->name_db . "/" . $key) . "' class='label label-primary'>" . $val . "</a> ";
                                }
                            }
                        } else {
                            $valueSel = json_decode($value);
                            foreach($valueSel as $key => $val) {
                                $valueOut .= "<span class='label label-primary'>" . $val . "</span> ";
                            }
                        }
                    } else {
                        $valueSel = json_decode($value);
                        foreach($valueSel as $key => $val) {
                            $valueOut .= "<span class='label label-primary'>" . $val . "</span> ";
                        }
                    }
                    $value = $valueOut;
                    break;
                case 'Textarea':
                    break;

                case 'TextField':

                    break;
                case 'URL':
                    $value = '<a target="_blank" href="' . $value . '">' . $value . '</a>';
                    break;
            }

            $out .= '<div class="col-md-8 col-sm-6 col-xs-6 fvalue">' . $value . '</div>';
            $out .= '</div>';
            return $out;
        } else {
            return "";
        }
    }

    /**
     * Print complete add/edit form for Module
     *
     * Uses blade syntax @la_form($employee_module_object)
     *
     * @param $module Module for which add/edit form has to be created.
     * @param array $fields List of Module Field Names to customize Selective Fields for Form
     * @return string returns HTML for complete Module Add/Edit Form
     */
    public static function form($module, $fields = [])
    {
        if(count($fields) == 0) {
            $fields = array_keys($module->fields);
        }
        $out = "";
        foreach($fields as $field) {
            // Use input method of this class to generate all Module fields
            $out .= LAFormMaker::input($module, $field);
        }
        return $out;
    }
    /**
     * Print complete add/edit form for Module
     *
     * Uses blade syntax @la_form($employee_module_object)
     *
     * @param $module Module for which add/edit form has to be created.
     * @param array $fields List of Module Field Names to customize Selective Fields for Form
     * @return string returns HTML for complete Module Add/Edit Form
     */
    public static function form_language($module, $fields = [], $language = [])
    {
        if (count($fields) == 0) {
            $fields = array_keys($module->fields);
        }
        $out = "";
        foreach ($fields as $field) {
            // Use input method of this class to generate all Module fields
            $out .= LAFormMaker::input_lang($module, $field, $language);
        }
        return $out;
    }

    /**
     * Check Whether User has Module Access
     * Work like @if blade directive of Laravel
     *
     * @param $module_id Module Id for which Access will be checked
     * @param string $access_type Access type like - view / create / edit / delete
     * @param int $user_id User id for which access is checked. By default it takes logged-in user
     * @return bool return whether access for this Module is true / false
     */
    public static function la_access($module_id, $access_type = "view", $user_id = 0)
    {
        // Check Module access by hasAccess method
        return Module::hasAccess($module_id, $access_type, $user_id);
    }

    /**
     * Check Whether User has Module Field Access
     *
     * Work like @if blade directive of Laravel
     *
     * @param $module_id Module Id for which Access will be checked
     * @param $field_id Field Id / Name for which Access will be checked
     * @param string $access_type Field Access type like - view / write
     * @param int $user_id User id for which access is checked. By default it takes logged-in user
     * @return bool return whether access for this Module Field is true / false
     */
    public static function la_field_access($module_id, $field_id, $access_type = "view", $user_id = 0)
    {
        // Check Module Field access by hasFieldAccess method
        return Module::hasFieldAccess($module_id, $field_id, $access_type, $user_id);
    }
}

