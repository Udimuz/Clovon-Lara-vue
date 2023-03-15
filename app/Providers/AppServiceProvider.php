<?php

namespace App\Providers;

use Carbon\Carbon;
//use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {
//		Factory::guessFactoryNamesUsing(function (string $modelName) {
//			return 'Database\\Factories\\'.class_basename($modelName).'Factory';
//		});

        Carbon::macro('toFormattedDate', function () {
			return $this->format('d-m-Y');
		});
    }
}
