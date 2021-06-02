
Route::get('/clear-all', function() {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    return "Cache is cleared";
});

/* ================== Homepage + Admin Routes ================== */

require __DIR__.'/admin_routes.php';
