<?php

namespace alalm3i\EdfaPay;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class EdfaPayServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('edfapay-laravel')
            ->hasConfigFile('edfapay');
        //            ->hasRoute('edfapay-api.php')
    }
}
