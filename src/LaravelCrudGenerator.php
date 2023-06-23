<?php

namespace TOTS\LaravelCrudGenerator;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class LaravelCrudGenerator
{
    private $entityName;
    private $options;

    public function __construct( $entityName, $options )
    {
        $this->entityName = $entityName;
        $this->options = $options;
    }

    public function generateFiles()
    {
         if (in_array('model', $this->options)) {
            Artisan::call('make:model ' . $this->entityName);
            // TO DO
        }

        if (in_array('controller', $this->options)) {
            Artisan::call('make:controller ' . $this->entityName . 'Controller');
            // TO DO
        }

        if (in_array('service', $this->options)) {
            $this->createService($this->entityName);
        }

        if (in_array('routes', $this->options)) {
            $this->createRoutes($this->entityName);
        }

        if (in_array('migration', $this->options)) {
            Artisan::call('make:migration create_' . strtolower($this->entityName) . 's_table --create=' . strtolower($this->entityName));
            // TO DO
        }

        if (in_array('test', $this->options)) {
            $this->createTests($this->entityName);
        }

        if (in_array('factory', $this->options)) {
            Artisan::call('make:factory ' . $this->entityName . 'Factory --model=' . $this->entityName);
            // TO DO
        }
    }

    private function createService()
    {
        $serviceStub = File::get(__DIR__ . '/Stubs/Service.stub');
        $serviceContent = str_replace('{{entity}}', $this->entityName, $serviceStub);

        $serviceFolderPath = app_path('Services');
        if (!File::exists($serviceFolderPath)) {
            File::makeDirectory($serviceFolderPath);
        }

        $servicePath = $serviceFolderPath . '/' . $this->entityName . 'Service.php';
        File::put($servicePath, $serviceContent);
    }

    private function createRoutes()
    {
        $routesStub = File::get(__DIR__ . '/Stubs/Routes.stub');
        $routesContent = str_replace('{{entity}}', $this->entityName, $routesStub);
        $routesPath = base_path('routes/' . strtolower($this->entityName) . '.php');
        File::put($routesPath, $routesContent);
    }

    private function createTests()
    {
        $testsStub = File::get(__DIR__ . '/Stubs/Tests.stub');
        $testsContent = str_replace('{{entity}}', $this->entityName, $testsStub);
        $testsPath = base_path('tests/Feature/' . $this->entityName . 'Test.php');
        File::put($testsPath, $testsContent);
    }

}
