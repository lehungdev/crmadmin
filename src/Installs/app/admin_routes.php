<?php
use Illuminate\Support\Facades\Route;
/* ================== Homepage ================== */
// Route::get('/', 'HomeController@index');
// Route::get('/home', 'HomeController@index');
// Route::auth();

/* ================== Access Uploaded Files ================== */
Route::get('files/{hash}/{name}', 'CRM\UploadsController@get_file');

/*
|--------------------------------------------------------------------------
| Admin Application Routes
|--------------------------------------------------------------------------
*/

$as = "";
if(\Lehungdev\Crmadmin\Helpers\LAHelper::laravel_ver() != 5.3) {
	$as = config('crmadmin.adminRoute').'.';

	// Routes for Laravel
    // Route::get('/logout', 'Auth\LoginController@logout');

    // Authentication Routes...
    Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
    Route::post('login', 'Auth\LoginController@login');
    Route::post('logout', 'Auth\LoginController@logout')->name('logout');

    Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
    Route::post('register', 'Auth\RegisterController@create');

}

Route::group(['as' => $as, 'middleware' => ['auth', 'permission:ADMIN_PANEL']], function () {

	/* ================== Dashboard ================== */

	Route::get(config('crmadmin.adminRoute'), 'CRM\DashboardController@index');
	Route::get(config('crmadmin.adminRoute'). '/dashboard', 'CRM\DashboardController@index');

	/* ================== Users ================== */
	Route::resource(config('crmadmin.adminRoute') . '/users', 'CRM\UsersController');
	Route::get(config('crmadmin.adminRoute') . '/user_dt_ajax', 'CRM\UsersController@dtajax');

	/* ================== Uploads ================== */
	Route::resource(config('crmadmin.adminRoute') . '/uploads', 'CRM\UploadsController');
	Route::post(config('crmadmin.adminRoute') . '/upload_files', 'CRM\UploadsController@upload_files');
    Route::get(config('crmadmin.adminRoute') . '/uploaded_files', 'CRM\UploadsController@uploaded_files');
    Route::get(config('crmadmin.adminRoute') . '/uploaded_files/{folder_end}', 'CRM\UploadsController@uploaded_files')->where(['folder_end' => '[a-zA-z0-9\-]+']);
	Route::post(config('crmadmin.adminRoute') . '/uploads_update_caption', 'CRM\UploadsController@update_caption');
	Route::post(config('crmadmin.adminRoute') . '/uploads_update_filename', 'CRM\UploadsController@update_filename');
	Route::post(config('crmadmin.adminRoute') . '/uploads_update_public', 'CRM\UploadsController@update_public');
	Route::post(config('crmadmin.adminRoute') . '/uploads_delete_file', 'CRM\UploadsController@delete_file');

	/* ================== Roles ================== */
	Route::resource(config('crmadmin.adminRoute') . '/roles', 'CRM\RolesController');
	Route::get(config('crmadmin.adminRoute') . '/role_dt_ajax', 'CRM\RolesController@dtajax');
	Route::post(config('crmadmin.adminRoute') . '/save_module_role_permissions/{id}', 'CRM\RolesController@save_module_role_permissions');

	/* ================== Permissions ================== */
	Route::resource(config('crmadmin.adminRoute') . '/permissions', 'CRM\PermissionsController');
	Route::get(config('crmadmin.adminRoute') . '/permission_dt_ajax', 'CRM\PermissionsController@dtajax');
	Route::post(config('crmadmin.adminRoute') . '/save_permissions/{id}', 'CRM\PermissionsController@save_permissions');

	/* ================== Departments ================== */
	Route::resource(config('crmadmin.adminRoute') . '/departments', 'CRM\DepartmentsController');
	Route::get(config('crmadmin.adminRoute') . '/department_dt_ajax', 'CRM\DepartmentsController@dtajax');

	/* ================== Employees ================== */
	Route::resource(config('crmadmin.adminRoute') . '/employees', 'CRM\EmployeesController');
	Route::get(config('crmadmin.adminRoute') . '/employee_dt_ajax', 'CRM\EmployeesController@dtajax');
	Route::post(config('crmadmin.adminRoute') . '/change_password/{id}', 'CRM\EmployeesController@change_password');

	/* ================== Organizations ================== */
	Route::resource(config('crmadmin.adminRoute') . '/organizations', 'CRM\OrganizationController');
    Route::get(config('crmadmin.adminRoute') . '/organization_dt_ajax', 'CRM\OrganizationController@dtajax');
    Route::post(config('crmadmin.adminRoute') . '/organization_field_switch', 'CRM\OrganizationController@dtswitch');
    Route::post(config('crmadmin.adminRoute') . '/organization_field_slider_switch', 'CRM\OrganizationController@dtSlideSwitch');

    /* ================== Languages ================== */
    Route::resource(config('crmadmin.adminRoute') . '/languages', 'CRM\LanguageController');
    Route::get(config('crmadmin.adminRoute') . '/language_dt_ajax', 'CRM\LanguageController@dtajax');

	/* ================== Backups ================== */
	Route::resource(config('crmadmin.adminRoute') . '/backups', 'CRM\BackupsController');
	Route::get(config('crmadmin.adminRoute') . '/backup_dt_ajax', 'CRM\BackupsController@dtajax');
	Route::post(config('crmadmin.adminRoute') . '/create_backup_ajax', 'CRM\BackupsController@create_backup_ajax');
    Route::get(config('crmadmin.adminRoute') . '/downloadBackup/{id}', 'CRM\BackupsController@downloadBackup');

    /* ================== Categories ================== */
    Route::resource(config('crmadmin.adminRoute') . '/categories', 'CRM\CategoryController');
    Route::get(config('crmadmin.adminRoute') . '/category_dt_ajax', 'CRM\CategoryController@dtajax');
    Route::post(config('crmadmin.adminRoute') . '/category_field_switch', 'CRM\CategoryController@dtswitch');
    Route::post(config('crmadmin.adminRoute') .'/ca_menus/update_hierarchy', 'CRM\CategoryController@update_hierarchy');
    Route::post(config('crmadmin.adminRoute') . '/categories/add', 'CRM\CategoryController@update_status');

    /* ================== Properties ================== */
    Route::resource(config('crmadmin.adminRoute') . '/properties', 'CRM\PropertyController');
    Route::get(config('crmadmin.adminRoute') . '/property_dt_ajax', 'CRM\PropertyController@dtajax');
    Route::post(config('crmadmin.adminRoute') . '/property_field_switch', 'CRM\PropertyController@dtswitch');


});
