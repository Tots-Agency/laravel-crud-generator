### TOTS Laravel CRUD Generator

[![Latest Version on Packagist](https://img.shields.io/packagist/v/tots/laravel-crud-generator.svg?style=flat-square)](https://packagist.org/packages/tots/laravel-crud-generator)
[![Total Downloads](https://img.shields.io/packagist/dt/tots/laravel-crud-generator?style=flat-square)](https://packagist.org/packages/tots/laravel-crud-generator)

---
TOTS Laravel CRUD Generator is a library to automate the creation of files for Laravel APIs

1. Run "composer require tots/laravel-crud-generator" command to install the package
2. Add "TOTS\LaravelCrudGenerator\LaravelCrudGeneratorServiceProvider::class," as a package service provider in your config/app.php file
3. Run "php artisan crud:install" to generate package config files
4. Update json file called "laravel-crud-generator.json" with all the things that you need
5. Run "php artisan crud:generate" to generate all the files for the definitions given at laravel-crud-generator.json file
---
