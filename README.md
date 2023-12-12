
<h1 align="center" style="text-align:center">TOTS Laravel CRUD Generator</h1>

<div align="center">
  
[![Latest Version on Packagist](https://img.shields.io/packagist/v/tots/laravel-crud-generator.svg?style=flat-square)](https://packagist.org/packages/tots/laravel-crud-generator)
[![Total Downloads](https://img.shields.io/packagist/dt/tots/laravel-crud-generator?style=flat-square)](https://packagist.org/packages/tots/laravel-crud-generator)

TOTS Laravel CRUD Generator is a library to automate the creation of files for Laravel APIs

</div>

---
## Installation
1. To install the package run
```bash
composer require --dev tots/laravel-crud-generator
```
3. Add "TOTS\LaravelCrudGenerator\LaravelCrudGeneratorServiceProvider::class," as a package service provider in your config/app.php file
4. To generate package config files run
```bash
php artisan crud:install
```
6. Update json file called "laravel-crud-generator.json" with all the things that you need
7. Run "php artisan crud:generate" to generate all the files for the definitions given at laravel-crud-generator.json file
---

## General configuration
Inside config folder you will find a file called "laravelCrudGenerator.php". There you can modify the default behavor of the generator.
Here you have a table with all the attributes you can modify:

|Attribute|Description|Type|Example|
| :---: | :----- | :--: | :----- |
| **default_file_path** | Indicates the path to json file | string | ```'laravel-crud-generator.json'``` |
| **rewrite** | Indicates if files are allowed to be rewritten if the generator runs | boolean | ```false``` |
| **files** | Indicates what type of files the generator is allowed to generate. All [types of file](#type-of-files) are listed in the example. | array | ```["routes", "model", "controller", "migration", "resource", "factory", "service", "test", "mock" ]``` |
| **relations** | Indicates the default relationships of the files generated. This is in case you need to stablish a relationship by default for all files. | array of relations | ```[ [ "BelognsTo" => [ "General" => [ "related" => "App\\Models\\General" ] ] ] ]``` |
| **routes** | Indicates the default configuration for routes files | array | ```[ "use" => [], "traits" => [], "interfaces" => [], "extends" => null, "file_path" => "routes/entities", "namespace" => null, "rewrite" => false ]``` |
| **model** | Indicates the default configuration for model files | array | ```[ "use" => [], "traits" => [ 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory' ], "interfaces" => [], "extends" => 'Illuminate\\Database\\Eloquent\\Model', "file_path" => "app/Models", "namespace" => "App\\Models", "rewrite" => false ]``` |
| **controller** | Indicates the default configuration for controller files | array | ```[ "use" => [],"traits" => [], "interfaces" => [], "extends" => 'App\\Http\\Controllers\\Controller', "file_path" => "app/Http/Controllers", "namespace" => "App\\Http\\Controllers", "methods" => [ "list", "show", "store", "update", "delete" ], "response" => "\\Illuminate\\Http\\JsonResponse", "rewrite" => false ]``` |
| **request** | Indicates the default configuration for request files | array | ```[ "use" => [], "traits" => [], "interfaces" => [], "extends" => 'Illuminate\\Foundation\\Http\\FormRequest', "file_path" => "app/Http/Requests", "namespace" => "App\\Http\\Requests", "methods" => [ "list", "show", "store", "update", "delete" ], "rewrite" => false ]``` |
| **resource** | Indicates the default configuration for resource files | array | ```[ "use" => [], "traits" => [], "interfaces" => [], "extends" => 'Illuminate\\Http\\Resources\\Json\\JsonResource', "file_path" => "app/Http/Resources", "namespace" => "App\\Http\\Resources", "rewrite" => false ]``` |
| **service** | Indicates the default configuration for service files | array | ```[ "use" => [], "traits" => [], "interfaces" => [], "extends" => null, "file_path" => "app/Services", "namespace" => "App\\Services", "methods" => [ "list", "fetch", "store", "update", "delete" ], "rewrite" => false ]``` |
| **migration** | Indicates the default configuration for migration files | array | ```[ "use" => [ 'Illuminate\\Database\\Schema\\Blueprint', 'Illuminate\\Support\\Facades\\Schema' ], "traits" => [], "interfaces" => [], "extends" => "Illuminate\\Database\\Migrations\\Migration", "file_path" => "database/migrations", "namespace" => null, "timestamps" => true, "softDeletes" => true, "id" => true, "rewrite" => false ]``` |
| **factory** | Indicates the default configuration for factory files | array | ```[ "use" => [ 'Illuminate\\Support\\Str' ], "traits" => [], "interfaces" => [], "extends" => 'Illuminate\\Database\\Eloquent\\Factories\\Factory', "file_path" => "database/factories", "namespace" => "Database\\Factories", "rewrite" => false ]``` |
| **mock** | Indicates the default configuration for mock seeder files | array | ```[ "use" => [], "traits" => [], "interfaces" => [], "extends" => 'Illuminate\\Database\\Seeder', "file_path" => "database/seeders/mocks", "namespace" => "Database\\Seeders\\Mocks", "count" => 50, "rewrite" => false ]``` |
| **test** | Indicates the default configuration for test files | array | ```[ "use" => [ 'Illuminate\\Foundation\\Testing\\WithFaker' ], "traits" => [ 'Illuminate\\Foundation\\Testing\\RefreshDatabase' ], "interfaces" => [], "extends" => 'Tests\\TestCase', "file_path" => "tests/Feature", "namespace" => "Tests\\Feature", "rewrite" => false ]``` |

---
## Configuration of laravel-crud-generator.json
As you may see, this file comes with an example. The hierarchy of the file is this:
> Entity

>> Enetity general configuration attributes

>>File

>>> File attributes configuration

---
## General Attributes

|Attribute|Description|Type|Example|
| :---: | :----- | :--: | :----- |
| **nameSingular** | Indicates the singular name of the entity | string (optional) | ```"product"``` |
| **namePlural** | Indicates the plural name of the entity | string (optional) | ```"products"``` |
| **rewrite** | Indicates if the files of the entity should be rewritten | boolean (optional) | ```false``` |
| **files** | Indicates what files of the entity should be created | array (optional) | ```[ "routes", "model", "controller" ]``` |
| **attributes** | Indicates the attributes of the entity | object of objects (optional) | ```"title": { "type": "string", "nullable": false, "unique": true, "default": "Default Test Product" }, "description": { "type": "text", "nullable": true, "unique": false }, "category_id": { "type": "bigInteger", "nullable": false, "unique": false }``` |
| **relations** | Indicates the relationships of the entity | object of objects (optional) | ```"belongsTo": { "Brand" : {}, "ProductCategory": { "related": "App\\Models\\Products\\ProductCategory", "foreingKey": "category_id", "localKey": "idcategory", "relation": "product_category_relation_name" } }, "hasOne": { "Post":{ "related": "App\\Models\\Posts\\Post", "foreingKey": "product_id", "localKey": "id" } }``` |
| **files** | Indicates what files of the entity should be created | array (optional) | ```[ "routes", "model", "controller" ]``` |
| **routes** | Indicates the attributes for routes file of the entity | object (optional) | -- |
| **model** | Indicates the attributes for model file of the entity | object (optional) | -- |
| **controller** | Indicates the attributes for controller file of the entity | object (optional) | -- |
| **request** | Indicates the attributes for request file of the entity | object (optional) | -- |
| **resource** | Indicates the attributes for resource file of the entity | object (optional) | -- |
| **service** | Indicates the attributes for service file of the entity | object (optional) | -- |
| **migration** | Indicates the attributes for migration file of the entity | object (optional) | -- |
| **factory** | Indicates the attributes for factory file of the entity | object (optional) | -- |
| **mock** | Indicates the attributes for mock seeder file of the entity | object (optional) | -- |
| **test** | Indicates the attributes for test file of the entity | object (optional) | -- |

---
### Type of Files
Right now, there are 10 types of files that can be generated:
- Routes
- Model
- Controller
- Request
- Resource
- Service
- Migration
- Factory
- Mock Seeder
- Test

### Routes
|Attribute|Description|Type|Example|
| :---: | :----- | :--: | :----- |
| **filePath** | Indicates the path of the file | string (optional) | ```"routes/entities"``` |
| **use** | Indicates the traits that should be included in this file | array of strings (optional) | ```[ "App\\Controllers\\SomeController", "App\\Controllers\\OtherController" ]``` |
| **rewrite** | Indicates if the file should be rewritted if exist | boolean (optional) | ```false``` |

### Model
|Attribute|Description|Type|Example|
| :---: | :----- | :--: | :----- |
| **filePath** | Indicates the path of the file | string (optional) | ```"app/Models/User"``` |
| **namespace** | Indicates the namespace of the class | string (optional) | ```"App\\Models"``` |
| **classname** | Indicates the name of the class | string (optional) | ```"User"``` |
| **table** | Indicates the table of the model | string (optional) | ```"users"``` |
| **primaryKey** | Indicates the primary key of the model | string (optional) | ```"user_id"``` |
| **traits** | Indicates the traits that should be implemented in this class | array of strings (optional) | ```[ "Laravel\\Sanctum\\HasApiTokens", "Illuminate\\Notifications\\Notifiable" ]``` |
| **use** | Indicates the traits that should be included in this class | array of strings (optional) | ```[ "Illuminate\\Support\\Str", "Faker\\Factory" ]``` |
| **interfaces** | Indicates the interfaces that should implement this class | array of strings (optional) | ```[ "Illuminate\\Contracts\\Auth\\MustVerifyEmail" ]``` |
| **extends** | Indicates the path of the extended class | string (optional) | ```"Illuminate\\Foundation\\Auth\\User as Authenticatable"``` |
| **rewrite** | Indicates if the file should be rewritted if exist | boolean (optional) | ```false``` |

### Controller
|Attribute|Description|Type|Example|
| :---: | :----- | :--: | :----- |
| **filePath** | Indicates the path of the file | string (optional) | ```"app/Http/Controllers/UserController"``` |
| **namespace** | Indicates the namespace of the class | string (optional) | ```"App\\Controllers"``` |
| **classname** | Indicates the name of the class | string (optional) | ```"UserController"``` |
| **traits** | Indicates the traits that should be implemented in this class | array of strings (optional) | ```[ "Illuminate\\Foundation\\Bus\\DispatchesJobs", "Illuminate\\Notifications\\Notifiable" ]``` |
| **use** | Indicates the traits that should be included in this class | array of strings (optional) | ```[ "Illuminate\\Support\\Str", "Illuminate\\Http\\Request" ]``` |
| **interfaces** | Indicates the interfaces that should implement this class | array of strings (optional) | ```[ "App\\Interfaces\\MustHaveCrud" ]``` |
| **extends** | Indicates the path of the extended class | string (optional) | ```"Illuminate\\Routing\\Controller"``` |
| **rewrite** | Indicates if the file should be rewritted if exist | boolean (optional) | ```false``` |
| **methods** | Indicates the methods that should be implemented in this controller. You can add cannonical methods (**list**, **store**, **show**, **update**, **delete**, **restore**) or your own custom methods. | array of strings (optional) | ```[ "list", "store", "delete", "someCustomMethod", "otherCustomMethod" ]``` |


---
## Usage
To generate files is neccesary to config the laravel-crud-generator.json file and then run "php artisan crud:generate" command.
