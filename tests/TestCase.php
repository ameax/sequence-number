<?php

namespace Ameax\SequenceNumber\Tests;

use Ameax\SequenceNumber\SequenceNumberServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Ameax\\SequenceNumber\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            SequenceNumberServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        //        config()->set('database.default', 'testing');
        Schema::dropAllTables();

        $migration = include __DIR__.'/../database/migrations/create_sequence_number_tables.php.stub';
        $migration->up();

    }
}
