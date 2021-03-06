<?php

/**
 * Code generated using CrmAdmin
 * Help: http://crmadmin.com
 * CrmAdmin is open-sourced software licensed under the MIT license.
 * Developed by: Lehungdev IT Solutions
 * Developer Website: http://lehungdevitsolutions.com
 */

namespace Lehungdev\Crmadmin\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Lehungdev\Crmadmin\Helpers\LAHelper;
use Illuminate\Support\Str;
use Eloquent;
use DB;

/**
 * Class LAInstall
 * @package Lehungdev\Crmadmin\Commands
 *
 * Command to install CrmAdmin package into project which moves lot of file from 'src/Installs' directory to Project
 */
class LAInstall extends Command
{
    // Model Names to be handled during Install
    var $modelsInstalled = ["User", "Role", "Permission", "Employee", "Department", "Language", "Property",  "Category", "Upload", "Organization", "Backup"];

    // The command signature.
    protected $signature = 'crm:install';

    // The command description.
    protected $description = 'Install CrmAdmin Package. Generate whole structure for /admin.';

    // Copy From Folder - Package Install Files
    protected $from;

    // Copy to Folder - Project Folder
    protected $to;

    /**
     * Generates and Moves files to install CrmAdmin package.
     * At the end runs migrations and ask to create Super Admin in order to complete the installation.
     *
     * @throws Exception
     */
    public function handle()
    {
        try {
            $this->info('CrmAdmin installation started...');

            $from = base_path('vendor/lehungdev/crmadmin/src/Installs');
            $to = base_path();

            $this->info('from: ' . $from . " to: " . $to);

            $this->line("\nDB Assistant:");
            if ($this->confirm("Want to set your Database config in the .env file ?", true)) {
                $this->line("DB Assistant Initiated....");
                $db_data = array();

                $db_data['host'] = $this->ask('Database Host', 'localhost');
                $db_data['port'] = $this->ask('Database Port', '3306');
                $db_data['db'] = $this->ask('Database Name', 'crmadmin1');
                $db_data['dbuser'] = $this->ask('Database User', 'root');
                $dbpass = $this->ask('Database Password', false);

                if ($dbpass !== FALSE) {
                    $db_data['dbpass'] = $dbpass;
                } else {
                    $db_data['dbpass'] = "";
                }

                $default_db_conn = env('DB_CONNECTION', 'mysql'); 
                config(['database.connections.' . $default_db_conn . '.host' => $db_data['host']]);
                config(['database.connections.' . $default_db_conn . '.port' => $db_data['port']]);
                LAHelper::setenv("DB_HOST", $db_data['host']);
                LAHelper::setenv("DB_PORT", $db_data['port']); 

                config(['database.connections.' . $default_db_conn . '.database' => $db_data['db']]);
                config(['database.connections.' . $default_db_conn . '.username' => $db_data['dbuser']]);
                config(['database.connections.' . $default_db_conn . '.password' => $db_data['dbpass']]);
                LAHelper::setenv("DB_DATABASE", $db_data['db']);
                LAHelper::setenv("DB_USERNAME", $db_data['dbuser']);
                LAHelper::setenv("DB_PASSWORD", $db_data['dbpass']);
            }

            if (env('CACHE_DRIVER') != "array") {
                config(['cache.default' => 'array']);
                LAHelper::setenv("CACHE_DRIVER", "array");
            }

            if ($this->confirm("This process may change/append to the following of your existing project files:"
                . "\n\n\t app/Http/routes.php"
                . "\n\t app/Models/User.php"
                . "\n\t database/migrations/2014_10_12_000000_create_users_table.php"
                . "\n\t gulpfile.js"
                . "\n\t app/Providers/AuthServiceProvider.php"
                . "\n\t config/auth.php"
                . "\n\t app/Http/Kernel.php"
                . "\n\n Please take backup or use git. Do you wish to continue ?", true)) {

                // Controllers
                $this->line("\n" . 'Generating Controllers...');
                $this->copyFolder($from . "/app/Controllers/Auth", $to . "/app/Http/Controllers/Auth");
                $this->replaceFolder($from . "/app/Controllers/Helpers", $to . "/app/Http/Controllers/Helpers");
                    unlink($to . "/app/Http/Controllers/Auth/ForgotPasswordController.php");
                    unlink($to . "/app/Http/Controllers/Auth/LoginController.php");
                    unlink($to . "/app/Http/Controllers/Auth/RegisterController.php");
                    unlink($to . "/app/Http/Controllers/Auth/ResetPasswordController.php");
                $this->replaceFolder($from . "/app/Controllers/CRM", $to . "/app/Http/Controllers/CRM");
                $this->copyFile($from . "/app/Controllers/Controller.php", $to . "/app/Http/Controllers/Controller.php");
                $this->copyFile($from . "/app/Controllers/HomeController.php", $to . "/app/Http/Controllers/HomeController.php");

                // Middleware 
                $this->copyFile($from . "/app/Middleware/RedirectIfAuthenticated.php", $to . "/app/Http/Middleware/RedirectIfAuthenticated.php");
                $this->copyFile($from . "/app/Middleware/CheckClientCredentials.php", $to . "/app/Http/Middleware/CheckClientCredentials.php");  ###changed 

                // Config
                $this->line('Generating Config...');
                $this->copyFile($from . "/config/crmadmin.php", $to . "/config/crmadmin.php");

                // Models
                $this->line('Generating Models...');
                if (!file_exists($to . "/app/Models")) {
                    $this->info("mkdir: (" . $to . "/app/Models)");
                    mkdir($to . "/app/Models");
                }
                foreach ($this->modelsInstalled as $model) {
                    $this->copyFile($from . "/app/Models/" . $model . ".php", $to . "/app/Models/" . $model . ".php");
                }

                // Custom Admin Route

                // Generate Uploads / Thumbnails folders in /storage
                $this->line('Generating Uploads / Thumbnails folders...');
                if (!file_exists($to . "/storage/uploads")) {
                    $this->info("mkdir: (" . $to . "/storage/uploads)");
                    mkdir($to . "/storage/uploads");
                }
                if (!file_exists($to . "/storage/thumbnails")) {
                    $this->info("mkdir: (" . $to . "/storage/thumbnails)");
                    mkdir($to . "/storage/thumbnails");
                }

                // la-assets
                $this->line('Generating CrmAdmin Public Assets...');
                $this->replaceFolder($from . "/la-assets", $to . "/public/la-assets");
                // Use "git config core.fileMode false" for ignoring file permissions

                // check CACHE_DRIVER to be array or else
                // It is required for Zizaco/Entrust
                // https://github.com/Zizaco/entrust/issues/468

                // migrations
                $this->line('Generating migrations...');
                $this->copyFolder($from . "/migrations", $to . "/database/migrations");

                $this->line('Copying seeders...');
                $this->copyFile($from . "/seeders/DatabaseSeeder.php", $to . "/database/seeders/DatabaseSeeder.php");


                // resources
                $this->line('Generating resources: assets + views...');
                $this->copyFolder($from . "/resources/assets", $to . "/resources/assets");
                $this->copyFolder($from . "/resources/views", $to . "/resources/views");

                // Traits & Transformer
                $this->line('Generating resources: Traits + Transformer...');
                $this->replaceFolder($from . "/Core", $to . "/app/Core");
                $this->replaceFolder($from . "/Traits", $to . "/app/Traits");

                // Checking database
                $this->line('Checking database connectivity...');
                DB::connection()->reconnect();

                // Running migrations...
                $this->line('Running migrations...');
                $this->call('clear-compiled');
                $this->call('cache:clear');
                $composer_path = "composer";
                if (PHP_OS == "Darwin") {
                    $composer_path = "/usr/bin/composer.phar";
                } else if (PHP_OS == "Linux") {
                    $composer_path = "/usr/bin/composer";
                } else if (PHP_OS == "Windows") {
                    $composer_path = "composer";
                }
                $this->info(exec($composer_path . ' dump-autoload'));

                $this->call('migrate:refresh');

                $this->call('db:seed');
                // Install Spatie Backup
                $this->call('vendor:publish', ['--provider' => 'Spatie\Backup\BackupServiceProvider']);

                // Edit config/database.php for Spatie Backup Configuration
                if (LAHelper::getLineWithString('config/database.php', "dump_command_path") == -1) {
                    $newDBConfig = "            'driver' => 'mysql',\n"
                        . "            'dump_command_path' => '/opt/lampp/bin', // only the path, so without 'mysqldump' or 'pg_dump'\n"
                        . "            'dump_command_timeout' => 60 * 5, // 5 minute timeout\n"
                        . "            'dump_using_single_transaction' => true, // perform dump using a single transaction\n";

                    $envfile = $this->openFile('config/database.php');
                    $mysqldriverline = LAHelper::getLineWithString('config/database.php', "'driver' => 'mysql'");
                    $envfile = str_replace($mysqldriverline, $newDBConfig, $envfile);
                    file_put_contents('config/database.php', $envfile);
                }

                // Routes
                $this->line('Appending routes...');
                if (LAHelper::getLineWithString($to . "/routes/web.php", "require __DIR__.'/admin_routes.php';") == -1) {
                    $this->appendFile($from . "/app/routes.php", $to . "/routes/web.php");
                }
                $this->copyFile($from . "/app/admin_routes.php", $to . "/routes/admin_routes.php");
                $this->copyFile($from . "/app/api.php", $to . "/routes/api.php");  ###changed

                // tests
                $this->line('Generating tests...');
                $this->copyFolder($from . "/tests", $to . "/tests");
                unlink($to . '/tests/TestCase.php');

                // Utilities
                $this->line('Generating Utilities...');
                if (file_exists($to . "/gulpfile.js")) {
                    if (LAHelper::getLineWithString($to . "/gulpfile.js", "mix.less('admin-lte/AdminLTE.less', 'public/la-assets/css');") == -1) {
                        $this->appendFile($from . "/gulpfile.js", $to . "/gulpfile.js");
                    }
                }
                // Creating Super Admin User

                $user = \App\Models\User::where('context_id', "1")->first();
                if (!isset($user['id'])) {

                    $this->line('Creating Super Admin User...');

                    $data = array();
                    $data['name'] = $this->ask('Super Admin name', 'Super Admin');
                    $data['email'] = $this->ask('Super Admin email', 'lehung.hut@gmail.com');
                    $data['password'] = bcrypt($this->secret('Super Admin password'));
                    $data['context_id'] = "1";
                    $data['type'] = "Employee";
                    $user = \App\Models\User::create($data);

                    // TODO: This is Not Standard. Need to find alternative
                    Eloquent::unguard();

                    \App\Models\Employee::create([
                        'name' => $data['name'],
                        'designation' => "Super Admin",
                        'mobile' => "0888183869",
                        'mobile2' => "",
                        'email' => $data['email'],
                        'gender' => 'Male',
                        'dept' => "1",
                        'city' => "HaNoi",
                        'address' => "Hai Ba Trung",
                        'about' => "About user / biography",
                        'date_birth' => date("Y-m-d"),
                        'date_hire' => date("Y-m-d"),
                        'date_left' => date("Y-m-d"),
                        'salary_cur' => 0,
                    ]);

                    $this->info("Super Admin User '" . $data['name'] . "' successfully created. ");
                } else {
                    $this->info("Super Admin User '" . $user['name'] . "' exists. ");
                }
                $role = \App\Models\Role::whereName('SUPER_ADMIN')->first();
                $user->attachRole($role);

                $this->info("\n");
                //Install passport authentication
                $this->line('Install passport authentication...');
                $this->call('passport:install');
                $this->info("Passport successfully installed.");

                $this->line('Auto config Passport:');
                //Add Trait To User Class
                $this->info("Add Trait To User Class: HasApiTokens");
                // Call Passport Routes And Add Some Configs
                $this->info("Call Passport Routes And Add Some Configs");
                $this->copyFile($from . "/app/Providers/AuthServiceProvider.php", $to . "/app/Providers/AuthServiceProvider.php");  ###changed
                $this->copyFile($from . "/.htaccess", $to . "/.htaccess");  ###changed
                $this->copyFile($from . "/public/.htaccess", $to . "/public/.htaccess");  ###changed
                $this->copyFile($from . "/public/resize.php", $to . "/public/resize.php");  ###changed
                //Finally You Need To Change The Api Driver
                $this->info("Finally You Need To Change The Api Driver: config/auth.php");
                $this->copyFile($from . "/config/auth.php", $to . "/config/auth.php");  ###changed
                //Creat Folder API in Controllers
                $this->replaceFolder($from . "/app/Controllers/Api", $to . "/app/Http/Controllers/Api");


                //publish DataTables\DataTablesServiceProvider"
                $this->line('Publish DataTables\DataTablesServiceProvider done');
                $this->call('vendor:publish', ['--provider' => 'Yajra\DataTables\DataTablesServiceProvider']);

                /////////////Note Change Kernel.php
                $this->line("\nChange Kernel.php: \$routeMiddleware -> 'CheckClientCredentials' => \App\Http\Middleware\CheckClientCredentials::class #changed");
                $contents_kernel = file_get_contents(base_path('app/Http/Kernel.php'));
                if (!Str::contains($contents_kernel, "CheckClientCredentials")) {
                    $contents_kernel = str_replace("'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,", "'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,\n\t\t'CheckClientCredentials' => \App\Http\Middleware\CheckClientCredentials::class #changed", $contents_kernel);
                    file_put_contents('app/Http/Kernel.php', $contents_kernel);
                }
                ///////////////////////////////////////////////////////

                $this->info("\nPassport complate.");
                $this->line("\nPublish Spatie\Fractal\FractalServiceProvider done");
                $this->call('vendor:publish', ['--provider' => 'Spatie\Fractal\FractalServiceProvider']);

                //Add IdeaHelper, RedisManager to file app.php
                //Add Kreait to file app.php
                $this->line("\n++ Add IdeaHelper, RedisManager to file config/app.php \n++ Add Kreait to file config/app.php");
                $contents_app = file_get_contents(base_path('config/app.php'));
                if (!Str::contains($contents_app, "'IdeaHelper'")) {
                    $contents_app = str_replace("'View' => Illuminate\Support\Facades\View::class,", "'View' => Illuminate\Support\Facades\View::class,\n\t\t'IdeaHelper' => App\Http\Controllers\Helpers\IdeaHelper::class,  #changed\n\t\t'RedisManager' => Illuminate\Support\Facades\Redis::class,  #changed", $contents_app);
                    file_put_contents('config/app.php', $contents_app);
                }

                if (!Str::contains($contents_app, "Kreait\Laravel\Firebase")) {
                    $contents_app = str_replace("Lehungdev\Crmadmin\LAProvider::class,", "Lehungdev\Crmadmin\LAProvider::class,\n\t\tKreait\Laravel\Firebase\ServiceProvider::class, #changed", $contents_app);
                    file_put_contents('config/app.php', $contents_app);
                }
                if(!Str::contains($contents_app, "locale_id")) {
                    $contents_app = str_replace("'locale' => 'en',", "'locale' => 'en',\n\n\t'locale_id' => '2', #changed", $contents_app);
                    file_put_contents('config/app.php', $contents_app);
                }

                //Add Models Language to file AppServiceProvider.php
                $this->line("\nAdd Models Language to file AppServiceProvider.php");
                $contents_AppService = file_get_contents(base_path('app/Providers/AppServiceProvider.php'));
                if(!Str::contains($contents_AppService, "App\Models\Language")){
                    $contents_AppService = str_replace("use Illuminate\Support\ServiceProvider;", "use Illuminate\Support\ServiceProvider;\nuse Illuminate\Support\Facades\Schema; #changed\nuse App\Models\Language; #changed\nuse View; #changed", $contents_AppService);
                    $contents_AppService = str_replace("public function boot()\n", "public function boot()\n\t{\n\t\tSchema::defaultStringLength(191); #changed\n\t\t\$this->language   =  Language::get(); #changed\n\t\tView::share('pvd_language', \$this->language); #changed", $contents_AppService);
                    $contents_AppService = str_replace("#changed    {", "#changed", $contents_AppService);
                    file_put_contents('app/Providers/AppServiceProvider.php', $contents_AppService);
                }


                $this->call('vendor:publish', ['--provider' => 'Kreait\Laravel\Firebase\ServiceProvider'], '--tag=config');

                ///Edit phpredis -> predis in file database.php
                $this->line("\nEdit phpredis -> predis in file database.php");
                $contents_database = file_get_contents(base_path('config/database.php'));
                $contents_database = str_replace("phpredis", "predis", $contents_database);
                file_put_contents('config/database.php', $contents_database);


                ///Add line FIREBASE_CREDENTIALS=/full/path/to/firebase_credentials.json in file .env
                $this->line("Add line FIREBASE_CREDENTIALS in file .env");
                $contents_env = file_get_contents(base_path('.env'));
                if (!Str::contains($contents_env, "FIREBASE_CREDENTIALS")) {
                    $contents_env .= "\nFIREBASE_CREDENTIALS=/full/path/to/firebase_credentials.json";
                    file_put_contents('.env', $contents_env);
                }


                $this->info("\nCrmAdmin successfully installed.");
                $this->info("You can now login from yourdomain.com/" . config('crmadmin.adminRoute') . " !!!\n");
            } else {
                $this->error("Installation aborted. Please try again after backup / git. Thank you...");
            }
        } catch (Exception $e) {
            $msg = $e->getMessage();
            if (strpos($msg, 'SQLSTATE') !== false) {
                throw new Exception("LAInstall: Database is not connected. Connect database (.env) and run 'php artisan crm:install' again.\n" . $msg, 1);
            } else {
                $this->error("LAInstall::handle exception: " . $e);
                throw new Exception("LAInstall::handle Unable to install : " . $msg, 1);
            }
        }
    }

    /**
     * Copy Folder contents
     *
     * @param $from from folder
     * @param $to to folder
     */
    private function copyFolder($from, $to)
    {
        // $this->info("copyFolder: ($from, $to)");
        LAHelper::recurse_copy($from, $to);
    }

    /**
     * Replace Folder contents by deleting content of to folder first
     *
     * @param $from from folder
     * @param $to to folder
     */
    private function replaceFolder($from, $to)
    {
        // $this->info("replaceFolder: ($from, $to)");
        if (file_exists($to)) {
            LAHelper::recurse_delete($to);
        }
        LAHelper::recurse_copy($from, $to);
    }

    /**
     * Copy file contents. If file not exists create it.
     *
     * @param $from from file
     * @param $to to file
     */
    private function copyFile($from, $to)
    {
        // $this->info("copyFile: ($from, $to)");
        if (!file_exists(dirname($to))) {
            $this->info("mkdir: (" . dirname($to) . ")");
            mkdir(dirname($to));
        }
        copy($from, $to);
    }

    /**
     * Get file contents
     *
     * @param $from file name
     * @return string file contents in string
     */
    private function openFile($from)
    {
        $md = file_get_contents($from);
        return $md;
    }

    /**
     * Append content of 'from' file to 'to' file
     *
     * @param $from from file
     * @param $to to file
     */
    private function appendFile($from, $to)
    {
        // $this->info("appendFile: ($from, $to)");

        $md = file_get_contents($from);

        file_put_contents($to, $md, FILE_APPEND);
    }

    /**
     * Copy contents from one file to another
     *
     * @param $from content to be copied from this file
     * @param $to content will be written to this file
     */
    private function writeFile($from, $to)
    {
        $md = file_get_contents($from);
        file_put_contents($to, $md);
    }

    /**
     * does file contains given text
     *
     * @param $filePath file to search text for
     * @param $text text to be searched in file
     * @return bool return true if text found in given file
     */
    private function fileContains($filePath, $text)
    {
        // TODO: Method not working properly

        $fileData = file_get_contents($filePath);
        if (strpos($fileData, $text) === false) {
            return true;
        } else {
            return false;
        }
    }
}