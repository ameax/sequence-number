<?php

namespace Ameax\SequenceNumber;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SequenceNumberServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('sequence-number')
            ->hasMigration('create_sequence_number_tables')
            ->hasConfigFile('sequence-number');
    }
}
