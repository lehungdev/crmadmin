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
use Auth;
use DB;
use Validator;
use Datatables;
use Collective\Html\FormFacade as Form;
use Lehungdev\Crmadmin\Models\Module;
use Lehungdev\Crmadmin\Models\ModuleFields;
use Illuminate\Support\Str;

use App\Models\Department;

class DepartmentsController extends Controller
{
	public $show_action = true;

	/**
	 * Display a listing of the Departments.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$module = Module::get('Departments');

		if(Module::hasAccess($module->id)) {
			return View('crm.departments.index', [
				'show_actions' => $this->show_action,
				'listing_cols' => Module::getListingColumns('Departments'),
				'module' => $module
			]);
		} else {
            return redirect(config('crmadmin.adminRoute')."/");
        }
	}

	/**
	 * Show the form for creating a new department.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created department in database.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		if(Module::hasAccess("Departments", "create")) {

			$rules = Module::validateRules("Departments", $request);

			$validator = Validator::make($request->all(), $rules);

			if ($validator->fails()) {
				return redirect()->back()->withErrors($validator)->withInput();
			}

			$insert_id = Module::insert("Departments", $request);

			return redirect()->route(config('crmadmin.adminRoute') . '.departments.index');

		} else {
			return redirect(config('crmadmin.adminRoute')."/");
		}
	}

	/**
	 * Display the specified department.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		if(Module::hasAccess("Departments", "view")) {

			$department = Department::find($id);
			if(isset($department->id)) {
				$module = Module::get('Departments');
				$module->row = $department;

				return view('crm.departments.show', [
					'module' => $module,
					'view_col' => $module->view_col,
					'no_header' => true,
					'no_padding' => "no-padding"
				])->with('department', $department);
			} else {
				return view('errors.404', [
					'record_id' => $id,
					'record_name' => ucfirst("department"),
				]);
			}
		} else {
			return redirect(config('crmadmin.adminRoute')."/");
		}
	}

	/**
	 * Show the form for editing the specified department.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		if(Module::hasAccess("Departments", "edit")) {
			$department = Department::find($id);
			if(isset($department->id)) {
				$module = Module::get('Departments');

				$module->row = $department;

				return view('crm.departments.edit', [
					'module' => $module,
					'view_col' => $module->view_col,
				])->with('department', $department);
			} else {
				return view('errors.404', [
					'record_id' => $id,
					'record_name' => ucfirst("department"),
				]);
			}
		} else {
			return redirect(config('crmadmin.adminRoute')."/");
		}
	}

	/**
	 * Update the specified department in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		if(Module::hasAccess("Departments", "edit")) {

			$rules = Module::validateRules("Departments", $request, true, $id);

			$validator = Validator::make($request->all(), $rules);

			if ($validator->fails()) {
				return redirect()->back()->withErrors($validator)->withInput();;
			}

			$insert_id = Module::updateRow("Departments", $request, $id);

			return redirect()->route(config('crmadmin.adminRoute') . '.departments.index');

		} else {
			return redirect(config('crmadmin.adminRoute')."/");
		}
	}

	/**
	 * Remove the specified department from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		if(Module::hasAccess("Departments", "delete")) {
			Department::find($id)->delete();

			// Redirecting to index() method
			return redirect()->route(config('crmadmin.adminRoute') . '.departments.index');
		} else {
			return redirect(config('crmadmin.adminRoute')."/");
		}
	}

	/**
	 * Datatable Ajax fetch
	 *
	 * @return
	 */
	public function dtajax(Request $request)
	{
		$module = Module::get('Departments');
		$listing_cols = Module::getListingColumns('Departments');

		$values = DB::table('departments')->select($listing_cols)->whereNull('deleted_at');
		$out = \DataTables::of($values)->make();
		$data = $out->getData();

		$fields_popup = ModuleFields::getModuleFields('Departments');

		for($i=0; $i < count($data->data); $i++) {
            $data->data[$i] =(array)$data->data[$i];
			for ($j=0; $j < count($listing_cols); $j++) {
                $col = $listing_cols[$j];
                $data->data[$i][$j] = $data->data[$i][$col];
				if($fields_popup[$col] != null && Str::of($fields_popup[$col]->popup_vals)->startsWith('@')) {
					$data->data[$i][$j] = ModuleFields::getFieldValue($fields_popup[$col], $data->data[$i][$col]);
				}
				if($col == $module->view_col) {
					$data->data[$i][$j] = '<a href="'.url(config('crmadmin.adminRoute') . '/departments/'.$data->data[$i][$listing_cols[0]]).'">'.$data->data[$i][$col].'</a>';
				}
				// else if($col == "author") {
				//    $data->data[$i][$j];
				// }
			}

			if($this->show_action) {
				$output = '';
				if(Module::hasAccess("Departments", "edit")) {
					$output .= '<a href="'.url(config('crmadmin.adminRoute') . '/departments/'.$data->data[$i][$listing_cols[0]].'/edit').'" class="btn btn-warning btn-xs" style="display:inline;padding:2px 5px 3px 5px;"><i class="fa fa-edit"></i></a>';
				}

				if(Module::hasAccess("Departments", "delete")) {
					$output .= Form::open(['route' => [config('crmadmin.adminRoute') . '.departments.destroy', $data->data[$i][$listing_cols[0]]], 'method' => 'delete', 'style'=>'display:inline']);
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
