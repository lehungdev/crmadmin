<?php
/**
 * Controller generated using IdeaGroup
 * Help: lehung.hut@gmail.com
 * CrmAdmin is open-sourced software licensed under the MIT license.
 * Developed by: Lehungdev IT Solutions
 * Developer Website: http://ideagroup.vn
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

use App\Models\Language;

class LanguageController extends Controller
{
    public $show_action = true;

    /**
     * Display a listing of the Languages.
     *
     * @return mixed
     */
    public function index()
    {
        $module = Module::get('Languages');

        if(Module::hasAccess($module->id)) {
            return View('crm.languages.index', [
                'show_actions' => $this->show_action,
                'listing_cols' => Module::getListingColumns('Languages'),
                'module' => $module
            ]);
        } else {
            return redirect(config('crmadmin.adminRoute') . "/");
        }
    }

    /**
     * Show the form for creating a new language.
     *
     * @return mixed
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created language in database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        if(Module::hasAccess("Languages", "create")) {

            $rules = Module::validateRules("Languages", $request);

            $validator = Validator::make($request->all(), $rules);

            if($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $insert_id = Module::insert("Languages", $request);

            return redirect()->route(config('crmadmin.adminRoute') . '.languages.index');

        } else {
            return redirect(config('crmadmin.adminRoute') . "/");
        }
    }

    /**
     * Display the specified language.
     *
     * @param int $id language ID
     * @return mixed
     */
    public function show($id)
    {
        if(Module::hasAccess("Languages", "view")) {

            $language = Language::find($id);
            if(isset($language->id)) {
                $module = Module::get('Languages');
                $module->row = $language;

                return view('crm.languages.show', [
                    'module' => $module,
                    'view_col' => $module->view_col,
                    'no_header' => true,
                    'no_padding' => "no-padding"
                ])->with('language', $language);
            } else {
                return view('errors.404', [
                    'record_id' => $id,
                    'record_name' => ucfirst("language"),
                ]);
            }
        } else {
            return redirect(config('crmadmin.adminRoute') . "/");
        }
    }

    /**
     * Show the form for editing the specified language.
     *
     * @param int $id language ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        if(Module::hasAccess("Languages", "edit")) {
            $language = Language::find($id);
            if(isset($language->id)) {
                $module = Module::get('Languages');

                $module->row = $language;

                return view('crm.languages.edit', [
                    'module' => $module,
                    'view_col' => $module->view_col,
                ])->with('language', $language);
            } else {
                return view('errors.404', [
                    'record_id' => $id,
                    'record_name' => ucfirst("language"),
                ]);
            }
        } else {
            return redirect(config('crmadmin.adminRoute') . "/");
        }
    }

    /**
     * Update the specified language in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id language ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        if(Module::hasAccess("Languages", "edit")) {

            $rules = Module::validateRules("Languages", $request, true);

            $validator = Validator::make($request->all(), $rules);

            if($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();;
            }

            $insert_id = Module::updateRow("Languages", $request, $id);

            return redirect()->route(config('crmadmin.adminRoute') . '.languages.index');

        } else {
            return redirect(config('crmadmin.adminRoute') . "/");
        }
    }

    /**
     * Remove the specified language from storage.
     *
     * @param int $id language ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        if(Module::hasAccess("Languages", "delete")) {
            Language::find($id)->delete();

            // Redirecting to index() method
            return redirect()->route(config('crmadmin.adminRoute') . '.languages.index');
        } else {
            return redirect(config('crmadmin.adminRoute') . "/");
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
        $module = Module::get('Languages');
        $listing_cols = Module::getListingColumns('Languages');

        $values = DB::table('languages')->select($listing_cols)->whereNull('deleted_at');
        $out = \\DataTables::of($values)->make();
        $data = $out->getData();

        $fields_popup = ModuleFields::getModuleFields('Languages');

        for($i = 0; $i < count($data->data); $i++) {
            $data->data[$i] =(array)$data->data[$i];
            for($j = 0; $j < count($listing_cols); $j++) {
                $col = $listing_cols[$j];
                $data->data[$i][$j] = $data->data[$i][$col];
                if($fields_popup[$col] != null && Str::of($fields_popup[$col]->popup_vals)->startsWith('@')) {
                    $data->data[$i][$j] = ModuleFields::getFieldValue($fields_popup[$col], $data->data[$i][$col]);
                }
                if($col == $module->view_col) {
                    $data->data[$i][$j] = '<a href="' . url(config('crmadmin.adminRoute') . '/languages/' . $data->data[$i][$listing_cols[0]]) . '">' . $data->data[$i][$col] . '</a>';
                }
                if($fields_popup[$col] != null && $fields_popup[$col]->field_type_str == "Image") {
                    if($data->data[$i][$col] != 0) {
                        $img = \App\Models\Upload::find($data->data[$i][$col]);
                        if(isset($img->name)) {
                            $data->data[$i][$j] = '<img src="'.$img->path().'?s=50">';
                        } else {
                            $data->data[$i][$j] = "";
                        }
                    } else {
                        $data->data[$i][$j] = "";
                    }
                }

                // else if($col == "author") {
                //    $data->data[$i][$col];
                // }
            }

            if($this->show_action) {
                $output = '';
                if(Module::hasAccess("Languages", "edit")) {
                    $output .= '<a href="' . url(config('crmadmin.adminRoute') . '/languages/' . $data->data[$i][$listing_cols[0]] . '/edit') . '" class="btn btn-warning btn-xs" style="display:inline;padding:2px 5px 3px 5px;"><i class="fa fa-edit"></i></a>';
                }

                if(Module::hasAccess("Languages", "delete")) {
                    $output .= Form::open(['route' => [config('crmadmin.adminRoute') . '.languages.destroy', $data->data[$i][$listing_cols[0]]], 'method' => 'delete', 'style' => 'display:inline']);
                    $output .= ' <button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-times"></i></button>';
                    $output .= Form::close();
                }
                $data->data[$i][] = (string)$output;
            }
        }
        $out->setData($data);
        return $out;
    }
}
