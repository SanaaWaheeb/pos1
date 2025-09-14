<?php

use Illuminate\Support\Facades\Route;

// default is /edfapay/notification
Route::post(config('edfapay.notification_path', '/edfapay/notification'), function (Illuminate\Http\Request $requests) {
    $notification_class = get_class(config('edfapay.notification_class'));

    (new $notification_class($requests->getContent()));

})->name('edfapay.notification.url');
