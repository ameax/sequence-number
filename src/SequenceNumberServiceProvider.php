<?php

namespace Ameax\SequenceNumber;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Ameax\SequenceNumber\Commands\SequenceNumberCommand;

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
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_sequence_number_table')
            ->hasCommand(SequenceNumberCommand::class);
    }
}