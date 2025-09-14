<?php

namespace App\Http\Middleware;

use App\Models\LandingPageSections;
use App\Models\Utility;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class XSS
{
    use \RachidLaasri\LaravelInstaller\Helpers\MigrationsHelper;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(\Auth::check())
        {
            $user = \Auth::user()->lang;
            \App::setLocale($user);

            if(\Auth::user()->type == 'super admin')
            {
                $migrations = $this->getMigrations();
                $dbMigrations = $this->getExecutedMigrations();
                $Modulemigrations = glob(base_path().'/Modules/LandingPage/Database'.DIRECTORY_SEPARATOR.'Migrations'.DIRECTORY_SEPARATOR.'*.php');
                $numberOfUpdatesPending = (count($migrations) + count($Modulemigrations)) - count($dbMigrations);

                if($numberOfUpdatesPending > 0)
                {
                    // Log the issue instead of redirecting
                    Log::warning("Pending migrations detected. Please run 'php artisan migrate'.");

                    // Show a session warning message to Super Admins
                    Session::flash('warning', 'Pending migrations detected. Please update the system.');

                    // Allow Super Admin to proceed without redirection
                }
            }
        }

        $input = $request->all();
        // Uncomment if you want to strip HTML tags from inputs
        // array_walk_recursive($input, function (&$input) {
        //     $input = strip_tags($input);
        // });
        $request->merge($input);

        return $next($request);
    }
}
