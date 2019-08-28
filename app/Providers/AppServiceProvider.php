<?php

namespace App\Providers;

use App;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {

      //dump(Session::get('locale'));
      // Schema::defaultStringLength(255);
/*
      Event::listen(StatementPrepared::class, function ($event) {
          $event->statement->setFetchMode(\PDO::FETCH_ASSOC);
      });
*/

//      DB::listen(function ($query){
//         dump($query->sql) ;
//         //dump($query->bindings);
//      });

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        //
    }
}
