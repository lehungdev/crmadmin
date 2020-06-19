<?php

/* ================== Homepage ================== */
Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index');
Route::auth();

/* ================== Access Uploaded Files ================== */
Route::get('files/{hash}/{name}', 'LA\UploadsController@get_file');

/*
|--------------------------------------------------------------------------
| Admin Application Routes
|--------------------------------------------------------------------------
*/

$as = "";
if(\Lehungdev\Crmadmin\Helpers\LAHelper::laravel_ver() != 5.3) {
	$as = config('crmadmin.adminRoute').'.';
	
	// Routes for Laravel 5.5
	Route::get('/logout', 'Auth\LoginController@logout');
}

Route::group(['as' => $as, 'middleware' => ['auth', 'permission:ADMIN_PANEL']], function () {
	
	/* ================== Dashboard ================== */
	
	Route::get(config('crmadmin.adminRoute'), 'LA\DashboardController@index');
	Route::get(config('crmadmin.adminRoute'). '/dashboard', 'LA\DashboardController@index');
	
	/* ================== Users ================== */
	Route::resource(config('crmadmin.adminRoute') . '/users', 'LA\UsersController');
	Route::get(config('crmadmin.adminRoute') . '/user_dt_ajax', 'LA\UsersController@dtajax');
	
	/* ================== Uploads ================== */
	Route::resource(config('crmadmin.adminRoute') . '/uploads', 'LA\UploadsController');
	Route::post(config('crmadmin.adminRoute') . '/upload_files', 'LA\UploadsController@upload_files');
	Route::get(config('crmadmin.adminRoute') . '/uploaded_files', 'LA\UploadsController@uploaded_files');
	Route::post(config('crmadmin.adminRoute') . '/uploads_update_caption', 'LA\UploadsController@update_caption');
	Route::post(config('crmadmin.adminRoute') . '/uploads_update_filename', 'LA\UploadsController@update_filename');
	Route::post(config('crmadmin.adminRoute') . '/uploads_update_public', 'LA\UploadsController@update_public');
	Route::post(config('crmadmin.adminRoute') . '/uploads_delete_file', 'LA\UploadsController@delete_file');
	
	/* ================== Roles ================== */
	Route::resource(config('crmadmin.adminRoute') . '/roles', 'LA\RolesController');
	Route::get(config('crmadmin.adminRoute') . '/role_dt_ajax', 'LA\RolesController@dtajax');
	Route::post(config('crmadmin.adminRoute') . '/save_module_role_permissions/{id}', 'LA\RolesController@save_module_role_permissions');
	
	/* ================== Permissions ================== */
	Route::resource(config('crmadmin.adminRoute') . '/permissions', 'LA\PermissionsController');
	Route::get(config('crmadmin.adminRoute') . '/permission_dt_ajax', 'LA\PermissionsController@dtajax');
	Route::post(config('crmadmin.adminRoute') . '/save_permissions/{id}', 'LA\PermissionsController@save_permissions');
	
	/* ================== Departments ================== */
	Route::resource(config('crmadmin.adminRoute') . '/departments', 'LA\DepartmentsController');
	Route::get(config('crmadmin.adminRoute') . '/department_dt_ajax', 'LA\DepartmentsController@dtajax');
	
	/* ================== Employees ================== */
	Route::resource(config('crmadmin.adminRoute') . '/employees', 'LA\EmployeesController');
	Route::get(config('crmadmin.adminRoute') . '/employee_dt_ajax', 'LA\EmployeesController@dtajax');
	Route::post(config('crmadmin.adminRoute') . '/change_password/{id}', 'LA\EmployeesController@change_password');
	
	/* ================== Organizations ================== */
	Route::resource(config('crmadmin.adminRoute') . '/organizations', 'LA\OrganizationsController');
	Route::get(config('crmadmin.adminRoute') . '/organization_dt_ajax', 'LA\OrganizationsController@dtajax');

	/* ================== Backups ================== */
	Route::resource(config('crmadmin.adminRoute') . '/backups', 'LA\BackupsController');
	Route::get(config('crmadmin.adminRoute') . '/backup_dt_ajax', 'LA\BackupsController@dtajax');
	Route::post(config('crmadmin.adminRoute') . '/create_backup_ajax', 'LA\BackupsController@create_backup_ajax');
	Route::get(config('crmadmin.adminRoute') . '/downloadBackup/{id}', 'LA\BackupsController@downloadBackup');
});
