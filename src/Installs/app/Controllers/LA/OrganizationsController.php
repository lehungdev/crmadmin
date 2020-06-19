<?php
/**
 * Controller generated using IdeaGroup
 * Help: lehung.hut@gmail.com
 * CrmAdmin is open-sourced software licensed under the MIT license.
 * Developed by: Lehungdev IT Solutions
 * Developer Website: http://ideagroup.vn
 */

namespace App\Http\Controllers\LA;

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

use App\Models\Organization;

class OrganizationsController extends Controller
{
	public $show_action = true;

	/**
	 * Display a listing of the Organizations.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$module = Module::get('Organizations');

		if(Module::hasAccess($module->id)) {
			return View('la.organizations.index', [
				'show_actions' => $this->show_action,
				'listing_cols' => Module::getListingColumns('Organizations'),
				'module' => $module
			]);
		} else {
            return redirect(config('crmadmin.adminRoute')."/");
        }
	}

	/**
	 * Show the form for creating a new organization.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created organization in database.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		if(Module::hasAccess("Organizations", "create")) {

			$rules = Module::validateRules("Organizations", $request);

			$validator = Validator::make($request->all(), $rules);

			if ($validator->fails()) {
				return redirect()->back()->withErrors($validator)->withInput();
			}

			$insert_id = Module::insert("Organizations", $request);

			return redirect()->route(config('crmadmin.adminRoute') . '.organizations.index');

		} else {
			return redirect(config('crmadmin.adminRoute')."/");
		}
	}

	/**
	 * Display the specified organization.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		if(Module::hasAccess("Organizations", "view")) {

			$organization = Organization::find($id);
			if(isset($organization->id)) {
				$module = Module::get('Organizations');
				$module->row = $organization;

				return view('la.organizations.show', [
					'module' => $module,
					'view_col' => $module->view_col,
					'no_header' => true,
					'no_padding' => "no-padding"
				])->with('organization', $organization);
			} else {
				return view('errors.404', [
					'record_id' => $id,
					'record_name' => ucfirst("organization"),
				]);
			}
		} else {
			return redirect(config('crmadmin.adminRoute')."/");
		}
	}

	/**
	 * Show the form for editing the specified organization.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		if(Module::hasAccess("Organizations", "edit")) {
			$organization = Organization::find($id);
			if(isset($organization->id)) {
				$module = Module::get('Organizations');

				$module->row = $organization;

				return view('la.organizations.edit', [
					'module' => $module,
					'view_col' => $module->view_col,
				])->with('organization', $organization);
			} else {
				return view('errors.404', [
					'record_id' => $id,
					'record_name' => ucfirst("organization"),
				]);
			}
		} else {
			return redirect(config('crmadmin.adminRoute')."/");
		}
	}

	/**
	 * Update the specified organization in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		if(Module::hasAccess("Organizations", "edit")) {

			$rules = Module::validateRules("Organizations", $request, true);

			$validator = Validator::make($request->all(), $rules);

			if ($validator->fails()) {
				return redirect()->back()->withErrors($validator)->withInput();;
			}

			$insert_id = Module::updateRow("Organizations", $request, $id);

			return redirect()->route(config('crmadmin.adminRoute') . '.organizations.index');

		} else {
			return redirect(config('crmadmin.adminRoute')."/");
		}
	}

	/**
	 * Remove the specified organization from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		if(Module::hasAccess("Organizations", "delete")) {
			Organization::find($id)->delete();

			// Redirecting to index() method
			return redirect()->route(config('crmadmin.adminRoute') . '.organizations.index');
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
		$module = Module::get('Organizations');
		$listing_cols = Module::getListingColumns('Organizations');

		$values = DB::table('organizations')->select($listing_cols)->whereNull('deleted_at');
		$out = Datatables::of($values)->make();
		$data = $out->getData();

		$fields_popup = ModuleFields::getModuleFields('Organizations');

		for($i=0; $i < count($data->data); $i++) {
			for ($j=0; $j < count($listing_cols); $j++) {
				$col = $listing_cols[$j];
				if($fields_popup[$col] != null && $fields_popup[$col]->field_type_str == "Image") {
					if($data->data[$i][$j] != 0) {
						$img = \App\Models\Upload::find($data->data[$i][$j]);
						if(isset($img->name)) {
							$data->data[$i][$j] = '<img src="'.$img->path().'?s=50">';
						} else {
							$data->data[$i][$j] = "";
						}
					} else {
						$data->data[$i][$j] = "";
					}
				}
				if($fields_popup[$col] != null && Str::of($fields_popup[$col]->popup_vals)->startsWith('@')) {
					$data->data[$i][$j] = ModuleFields::getFieldValue($fields_popup[$col], $data->data[$i][$j]);
				}
				if($col == $module->view_col) {
					$data->data[$i][$j] = '<a href="'.url(config('crmadmin.adminRoute') . '/organizations/'.$data->data[$i][0]).'">'.$data->data[$i][$j].'</a>';
				}
				// else if($col == "author") {
				//    $data->data[$i][$j];
				// }
			}

			if($this->show_action) {
				$output = '';
				if(Module::hasAccess("Organizations", "edit")) {
					$output .= '<a href="'.url(config('crmadmin.adminRoute') . '/organizations/'.$data->data[$i][0].'/edit').'" class="btn btn-warning btn-xs" style="display:inline;padding:2px 5px 3px 5px;"><i class="fa fa-edit"></i></a>';
				}

				if(Module::hasAccess("Organizations", "delete")) {
					$output .= Form::open(['route' => [config('crmadmin.adminRoute') . '.organizations.destroy', $data->data[$i][0]], 'method' => 'delete', 'style'=>'display:inline']);
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
