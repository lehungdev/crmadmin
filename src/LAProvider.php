<?php
/**
 * Code generated using IdeaGroup
 * Help: lehung.hut@gmail.com
 * CrmAdmin is open-sourced software licensed under the MIT license.
 * Developed by: Lehungdev IT Solutions
 * Developer Website: http://ideagroup.vn
 */

namespace Lehungdev\Crmadmin;

use Artisan;
use Illuminate\Support\Facades\Blade;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

use Lehungdev\Crmadmin\Helpers\LAHelper;

/**
 * Class LAProvider
 * @package Lehungdev\Crmadmin
 *
 * This is CrmAdmin Service Provider which looks after managing aliases, other required providers, blade directives
 * and Commands.
 */
class LAProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // @mkdir(base_path('resources/crmadmin'));
        // @mkdir(base_path('database/migrations/crmadmin'));
        /*
        $this->publishes([
            __DIR__.'/Templates' => base_path('resources/crmadmin'),
            __DIR__.'/config.php' => base_path('config/crmadmin.php'),
            __DIR__.'/Migrations' => base_path('database/migrations/crmadmin')
        ]);
        */
        //echo "Crmadmin Migrations started...";
        // Artisan::call('migrate', ['--path' => "vendor/lehungdev/crmadmin/src/Migrations/"]);
        //echo "Migrations completed !!!.";
        // Execute by php artisan vendor:publish --provider="Lehungdev\Crmadmin\LAProvider"

        /*
        |--------------------------------------------------------------------------
        | Blade Directives for LaravelEntrust not working in Laravel 5.5
        |--------------------------------------------------------------------------
        */
        if(LAHelper::laravel_ver() != 5.3) {

            // Call to LaravelEntrust::hasRole
            Blade::directive('role', function ($expression) {
                return "<?php if (\\LaravelEntrust::hasRole({$expression})) : ?>";
            });

            // Call to LaravelEntrust::can
            Blade::directive('permission', function ($expression) {
                return "<?php if (\\LaravelEntrust::can({$expression})) : ?>";
            });

            // Call to LaravelEntrust::ability
            Blade::directive('ability', function ($expression) {
                return "<?php if (\\LaravelEntrust::ability({$expression})) : ?>";
            });
        }
    }

    /**
     * Register the application services including routes, Required Providers, Alias, Controllers, Blade Directives
     * and Commands.
     *
     * @return void
     */
    public function register()
    {
        include __DIR__ . '/routes.php';

        // For LAEditor
        if(file_exists(__DIR__ . '/../../laeditor')) {
            include __DIR__ . '/../../laeditor/src/routes.php';
        }

        /*
        |--------------------------------------------------------------------------
        | Providers
        |--------------------------------------------------------------------------
        */

        // Collective HTML & Form Helper
        $this->app->register(\Collective\Html\HtmlServiceProvider::class);
        // For Datatables
        $this->app->register(\Yajra\Datatables\DatatablesServiceProvider::class);
        // For Gravatar
        $this->app->register(\Creativeorange\Gravatar\GravatarServiceProvider::class);
        // For LaravelEntrust
        $this->app->register(\Shanmuga\LaravelEntrust\LaravelEntrustServiceProvider::class);
        // For Spatie Backup
        $this->app->register(\Spatie\Backup\BackupServiceProvider::class);

        /*
        |--------------------------------------------------------------------------
        | Register the Alias
        |--------------------------------------------------------------------------
        */

        $loader = AliasLoader::getInstance();

        // Collective HTML & Form Helper
        $loader->alias('Form', \Collective\Html\FormFacade::class);
        $loader->alias('HTML', \Collective\Html\HtmlFacade::class);

        // For Gravatar User Profile Pics
        $loader->alias('Gravatar', \Creativeorange\Gravatar\Facades\Gravatar::class);

        // For CrmAdmin Code Generation
        $loader->alias('CodeGenerator', \Lehungdev\Crmadmin\CodeGenerator::class);

        // For CrmAdmin Form Helper
        $loader->alias('LAFormMaker', \Lehungdev\Crmadmin\LAFormMaker::class);

        // For CrmAdmin Helper
        $loader->alias('LAHelper', \Lehungdev\Crmadmin\Helpers\LAHelper::class);

        // CrmAdmin Module Model
        $loader->alias('Module', \Lehungdev\Crmadmin\Models\Module::class);

        // For CrmAdmin Configuration Model
        $loader->alias('LAConfigs', \Lehungdev\Crmadmin\Models\LAConfigs::class);

        // For LaravelEntrust
        $loader->alias('LaravelEntrust', \Shanmuga\LaravelEntrust\LaravelEntrustFacade::class);
        $loader->alias('role', \Shanmuga\LaravelEntrust\Middleware\EntrustRole::class);
        $loader->alias('permission', \Shanmuga\LaravelEntrust\Middleware\LaravelEntrustPermission::class);
        $loader->alias('ability', \Shanmuga\LaravelEntrust\Middleware\LaravelEntrustAbility::class);

        /*
        |--------------------------------------------------------------------------
        | Register the Controllers
        |--------------------------------------------------------------------------
        */

        $this->app->make('Lehungdev\Crmadmin\Controllers\ModuleController');
        $this->app->make('Lehungdev\Crmadmin\Controllers\FieldController');
        $this->app->make('Lehungdev\Crmadmin\Controllers\MenuController');

        // For LAEditor
        if(file_exists(__DIR__ . '/../../laeditor')) {
            $this->app->make('Lehungdev\Laeditor\Controllers\CodeEditorController');
        }

        /*
        |--------------------------------------------------------------------------
        | Blade Directives
        |--------------------------------------------------------------------------
        */

        // LAForm Input Maker
        Blade::directive('la_input', function ($expression) {
            if(LAHelper::laravel_ver() != 5.3) {
                $expression = "(" . $expression . ")";
            }
            return "<?php echo LAFormMaker::input$expression; ?>";
        });

        // LAForm Form Maker
        Blade::directive('la_form', function ($expression) {
            if(LAHelper::laravel_ver() != 5.3) {
                $expression = "(" . $expression . ")";
            }
            return "<?php echo LAFormMaker::form$expression; ?>";
        });

        // LAForm Maker - Display Values
        Blade::directive('la_display', function ($expression) {
            if(LAHelper::laravel_ver() != 5.3) {
                $expression = "(" . $expression . ")";
            }
            return "<?php echo LAFormMaker::display$expression; ?>";
        });

        // LAForm Maker - Check Whether User has Module Access
        Blade::directive('la_access', function ($expression) {
            if(LAHelper::laravel_ver() != 5.3) {
                $expression = "(" . $expression . ")";
            }
            return "<?php if(LAFormMaker::la_access$expression) { ?>";
        });
        Blade::directive('endla_access', function ($expression) {
            return "<?php } ?>";
        });

        // LAForm Maker - Check Whether User has Module Field Access
        Blade::directive('la_field_access', function ($expression) {
            if(LAHelper::laravel_ver() != 5.3) {
                $expression = "(" . $expression . ")";
            }
            return "<?php if(LAFormMaker::la_field_access$expression) { ?>";
        });
        Blade::directive('endla_field_access', function ($expression) {
            return "<?php } ?>";
        });

        /*
        |--------------------------------------------------------------------------
        | Register the Commands
        |--------------------------------------------------------------------------
        */

        $commands = [
            \Lehungdev\Crmadmin\Commands\Migration::class,
            \Lehungdev\Crmadmin\Commands\Crud::class,
            \Lehungdev\Crmadmin\Commands\Packaging::class,
            \Lehungdev\Crmadmin\Commands\LAInstall::class
        ];

        // For LAEditor
        if(file_exists(__DIR__ . '/../../laeditor')) {
            $commands[] = \Lehungdev\Laeditor\Commands\LAEditor::class;
        }

        $this->commands($commands);
    }
}
