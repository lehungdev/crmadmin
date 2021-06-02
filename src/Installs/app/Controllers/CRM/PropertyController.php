<?php
/**
 * Controller generated using IdeaGroup
 * Help: lehung.hut@gmail.com
 * CrmAdmin is open-sourced software licensed under the MIT license.
 * Developed by: Lehungdev IT Solutions
 * Developer Website: http://rellifetech.com
 */

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Str;
use Auth;
use DB;
use Validator;
use Datatables;
use Collective\Html\FormFacade as Form;
use Lehungdev\Crmadmin\Models\Module;
use Lehungdev\Crmadmin\Models\ModuleFields;

use App\Models\Property;
use App\Models\Language;

class PropertyController extends Controller
{
    public $show_action = true;

    /**
     * Display a listing of the Properties.
     *
     * @return mixed
     */
    public function index()
    {
        $module = Module::get('Properties');
        $pvd_language = Language::get();

        if(Module::hasAccess($module->id)) {
            return View('crm.properties.index', [
                'show_actions' => $this->show_action,
                'listing_cols' => Module::getListingColumns('Properties'),
                'module' => $module
            ]);
        } else {
            return redirect(config('crmadmin.adminRoute') . "/");
        }
    }

    /**
     * Show the form for creating a new property.
     *
     * @return mixed
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created property in database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        if(Module::hasAccess("Properties", "create")) {

            $module = Module::get('Properties');
            $pvd_language = Language::get();

            foreach($module->fields as $key_field => $field){
                if(!empty($field['lang_active']) and isset($request->$key_field)){
                    if($field['label'] == 'Image'){
                        $value_image = [];
                        foreach ($pvd_language as $key_lang => $value_lang){
                            $field_image = $key_field.'_'.$value_lang->id;
                            $value_image[$value_lang->id] = $request->$field_image;
                            unset($request->$field_image);
                        }
                        $request->merge([$key_field=>$value_image]);
                    } else {
                        $request->merge([$key_field=>json_encode($request->$key_field, JSON_UNESCAPED_UNICODE)]);
                    }

                } else {
                    if(isset($request->$key_field)){
                        $request->merge([$key_field=>$request->$key_field[config('app.locale_id')]]) ;
                    } else {
                        if($field['label'] == 'Image'){
                            $field_image = $key_field.'_'.config('app.locale_id');
                            $request->merge([$key_field=>$request->$field_image]) ;
                            unset($request->$field_image);
                        } else $request->merge([$key_field=>$request->$key_field]);
                    }
                }
            }

            $rules = Module::validateRules("Properties", $request);

            $validator = Validator::make($request->all(), $rules);

            if($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $insert_id = Module::insert("Properties", $request);

            return redirect()->route(config('crmadmin.adminRoute') . '.properties.index');

        } else {
            return redirect(config('crmadmin.adminRoute') . "/");
        }
    }

    /**
     * Display the specified property.
     *
     * @param int $id property ID
     * @return mixed
     */
    public function show($id)
    {
        if(Module::hasAccess("Properties", "view")) {

            $property = Property::find($id);
            if(isset($property->id)) {
                $module = Module::get('Properties');
                $module->row = $property;

                return view('crm.properties.show', [
                    'module' => $module,
                    'view_col' => $module->view_col,
                    'no_header' => true,
                    'no_padding' => "no-padding"
                ])->with('property', $property);
            } else {
                return view('errors.404', [
                    'record_id' => $id,
                    'record_name' => ucfirst("property"),
                ]);
            }
        } else {
            return redirect(config('crmadmin.adminRoute') . "/");
        }
    }

    /**
     * Show the form for editing the specified property.
     *
     * @param int $id property ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        if(Module::hasAccess("Properties", "edit")) {
            $property = Property::find($id);
            if(isset($property->id)) {
                $module = Module::get('Properties');

                $module->row = $property;

                return view('crm.properties.edit', [
                    'module' => $module,
                    'view_col' => $module->view_col,
                ])->with('property', $property);
            } else {
                return view('errors.404', [
                    'record_id' => $id,
                    'record_name' => ucfirst("property"),
                ]);
            }
        } else {
            return redirect(config('crmadmin.adminRoute') . "/");
        }
    }

    /**
     * Update the specified property in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id property ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        if(Module::hasAccess("Properties", "edit")) {

            $module = Module::get('Properties');
            $pvd_language = Language::get();

            foreach($module->fields as $key_field => $field){
                if(!empty($field['lang_active']) and isset($request->$key_field)){
                    if($field['label'] == 'Image'){
                        $value_image = [];
                        foreach ($pvd_language as $key_lang => $value_lang){
                            $field_image = $key_field.'_'.$value_lang->id;
                            $value_image[$value_lang->id] = $request->$field_image;
                            unset($request->$field_image);
                        }
                        $request->merge([$key_field=>$value_image]);
                    } else {
                        $request->merge([$key_field=>json_encode($request->$key_field, JSON_UNESCAPED_UNICODE)]);
                    }

                } else {
                    if(isset($request->$key_field)){
                        $request->merge([$key_field=>$request->$key_field[config('app.locale_id')]]) ;
                    } else {
                        if($field['label'] == 'Image'){
                            $field_image = $key_field.'_'.config('app.locale_id');
                            $request->merge([$key_field=>$request->$field_image]) ;
                            unset($request->$field_image);
                        } else $request->merge([$key_field=>$request->$key_field]);
                    }

                }
            }

            $rules = Module::validateRules("Properties", $request, true, $id);

            $validator = Validator::make($request->all(), $rules);

            if($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();;
            }

            $insert_id = Module::updateRow("Properties", $request, $id);

            return redirect()->route(config('crmadmin.adminRoute') . '.properties.index');

        } else {
            return redirect(config('crmadmin.adminRoute') . "/");
        }
    }

    /**
     * Remove the specified property from storage.
     *
     * @param int $id property ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        if(Module::hasAccess("Properties", "delete")) {
            Property::find($id)->delete();

            // Redirecting to index() method
            return redirect()->route(config('crmadmin.adminRoute') . '.properties.index');
        } else {
            return redirect(config('crmadmin.adminRoute') . "/");
        }
    }

    /**
     * Server side Datatable fetch via switch
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function dtswitch(Request $request)
    {
        if(Module::hasFieldAccess("Properties", $request->switchName, "write")){
            if($request->state == "true") {
                $state = 1;
            } else {
                $state = 0;
            }
            $item = Property::find($request->switchId);
            if(isset($item->id)) {
                $item[$request['switchName']] = $state;
                $item->save();
                return response()->json(['status' => 'success', 'message' => "Property field switch ". $request->switchName ." saved to " . $state]);
            } else {
                return response()->json(['status' => 'failed', 'message' => "Property field not found"]);
            }
        }
    }

    /**
     * Server side Datatable fetch via Ajax
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function dtajax(Request $request)
    {
        $module = Module::get('Properties');
        $listing_cols = Module::getListingColumns('Properties');

        $values = DB::table('properties')->select($listing_cols)->whereNull('deleted_at');
        $out = \DataTables::of($values)->make();
        $data = $out->getData();

        $fields_popup = ModuleFields::getModuleFields('Properties');

        for($i = 0; $i < count($data->data); $i++) {
            $data->data[$i] =(array)$data->data[$i];
            for($j = 0; $j < count($listing_cols); $j++) {
                $col = $listing_cols[$j];

                //Set value langue
                if(!empty($module->fields[$col]) and !empty($module->fields[$col]['lang_active'])){
                    $data_col = json_decode(str_replace('&quot;', '"', $data->data[$i][$col]), true);
                    if(!empty($data_col)){
                        $data->data[$i][$col] = $data_col[config('app.locale_id')];
                    }
                }//End set value langue

                $data->data[$i][$j] = $data->data[$i][$col];
                if($fields_popup[$col] != null && Str::of($fields_popup[$col]->popup_vals)->startsWith('@')) {
                    $fieldValue = ModuleFields::getFieldValue($fields_popup[$col], $data->data[$i][$col]);
                    if(isset($fieldValue)){
                        $data_col = json_decode(str_replace('&quot;', '"', $fieldValue), true);
                        if(!empty($data_col) and isset($data_col[config('app.locale_id')])){
                            $data->data[$i][$j] = '<small class="label label-primary"><i class="fa fa-caret-right"></i> '. $data_col[config('app.locale_id')] .'</small> ';
                        } else  $data->data[$i][$j] = '<small class="label label-primary"><i class="fa fa-caret-right"></i> '. $fieldValue .'</small> ';
                    }
                    else $data->data[$i][$j] = '';
                }

                if($col == $module->view_col) {
                    $data->data[$i][$j] = '<a href="' . url(config('crmadmin.adminRoute') . '/properties/' . $data->data[$i][$listing_cols[0]]) . '">' . $data->data[$i][$col] . '</a>';
                }

                //Path field Image
                 if($fields_popup[$col] != null && $fields_popup[$col]->field_type_str == "Image") {
                    if($data->data[$i][$col]){
                        $json_image = str_replace('&quot;', '"', $data->data[$i][$col]);
                        $json_image = json_decode($json_image);
                        $data->data[$i][$j] = '<img src="'.url('/s50x50'.$json_image->path).'">';
                    } else {
                        $data->data[$i][$j] = "";
                    }
                }

                //Field CheckBox
                if(!empty($module->fields[$col]) && $module->fields[$col]['field_type'] == 2){
                    if(Module::hasFieldAccess("Properties", $col, "write")){
                        if(!empty($data->data[$i][$col])){
                            $switch = 'Off';
                        } else $switch = 'On';
                        $data->data[$i][$j] = '<div class="Switch Ajax '. $col .' Round '. $switch .'" switchName="'. $col .'" " switchId="'. $data->data[$i][$listing_cols[0]] .'" style="vertical-align:top;margin-left:10px;"><div class="Toggle"></div></div>';
                    } else {
                        if(!empty($data->data[$i][$col])){
                            if($data->data[$i][$col] == 1){
                                $classStatus = 'success';
                                $status = $module->fields[$col]['colname'];
                            } else{
                                $classStatus = 'warning';
                                $status = 'Chờ '. $module->fields[$col]['colname'];
                            }
                        } else {
                            $classStatus = 'danger';
                            $status = 'Not '. $module->fields[$col]['colname'];
                        }
                        $data->data[$i][$j] = '<small class="label label-'. $classStatus .'">'. Str::ucfirst($status) .'</small> ';
                    }
                }

                //Field Multiselect
                if(!empty($module->fields[$col]) && $module->fields[$col]['field_type'] == 15){
                    $popup_vals = str_replace('@', '', $module->fields[$col]['popup_vals']);
                    $json = str_replace('&quot;', '"', $data->data[$i][$col]);
                    $values_json = DB::table($popup_vals)->whereIn('id',json_decode($json))->pluck('name');
                    $data->data[$i][$j] = '';
                    foreach($values_json as $value_json){
                        $data->data[$i][$j] .= '<small class="label label-primary">'. $value_json .'</small> ';
                    }
                }

                if(!empty($module->fields[$col]) && $module->fields[$col]['field_type'] == 20){
                    $values_json = json_decode(str_replace('&quot;', '"', $data->data[$i][$col]), true);
                    $data->data[$i][$j] = '';
                    foreach($values_json as $value_json){
                        $data->data[$i][$j] .= '<small class="label label-primary">'. $value_json .'</small> ';
                    }
                }

                if( $col!== 'id')
                    unset($data->data[$i][$col]);
            }

            if($this->show_action) {
                $output = '';
                if(Module::hasAccess("Properties", "edit")) {
                    $output .= '<a href="' . url(config('crmadmin.adminRoute') . '/properties/' . $data->data[$i][$listing_cols[0]] . '/edit') . '" class="btn btn-warning btn-xs" style="display:inline;padding:2px 5px 3px 5px;"><i class="fa fa-edit"></i></a>';
                }

                if(Module::hasAccess("Properties", "delete")) {
                    $output .= Form::open(['route' => [config('crmadmin.adminRoute') . '.properties.destroy', $data->data[$i][$listing_cols[0]]], 'method' => 'delete', 'style' => 'display:inline']);
                    $output .= ' <button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-times"></i></button>';
                    $output .= Form::close();
                }
                $data->data[$i][] = (string)$output;
            }
            unset($data->data[$i]['id']);
        }
        unset($data->queries);
        $out->setData($data);
        return $out;
    }
}
