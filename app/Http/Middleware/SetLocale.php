<?php

namespace App\Http\Middleware;

use App\Models\Store;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!empty(session()->get('lang'))) {
            $lang = (session()->get('lang')) ? session()->get('lang') : 'en';
        } else {
            if(!empty(Auth::guard('customers')->user())){
                $user = Auth::guard('customers')->user();
                $store = Store::find($user->store_id);
                if(isset($store) && !empty($store)){
                    $lang = isset($store->lang) ? $store->lang : 'en';
                }else{
                    $lang = 'en';
                }
            }else{
                if(Auth::check())
                {
                    $lang=(Auth::user()->lang) ? Auth::user()->lang : 'en';
                }else{
                    $lang = 'en';
                }
            }
        }
        App::setLocale($lang);

        return $next($request);
    }
}
