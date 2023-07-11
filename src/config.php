<?php

return [
    "default_file_path" => env( "LARAVEL_CRUD_GENERATOR_FILE_PATH" ) ?? 'laravel-crud-generator.json',
    "rewrite" => env( "LARAVEL_CRUD_GENERATOR_RERWRITE" ) ?? true,
    "files" => [ "routes", "model", "controller", "migration", "factory", "service", "test" ],
    "relations" => [],
    "model" => [
        "use" => [],
        "traits" => [],
        "interfaces" => [],
        "extends" => null,
        "file_path" => "app/Models",
        "namespace" => "App\\Models",
        "rewrite" => true
    ],
    "controller" => [
        "use" => [ 'Illuminate\Http\Request' ],
        "traits" => [],
        "interfaces" => [],
        "extends" => "Controller",
        "file_path" => "app/Http/Controllers",
        "namespace" => "App\\Http\\Controllers",
        "methods" => [ "list", "store", "update", "delete" ],
        "rewrite" => true
    ],
    "service" => [
        "use" => [],
        "traits" => [],
        "interfaces" => [],
        "extends" => null,
        "file_path" => "app/Services",
        "namespace" => "App\\Services",
        "rewrite" => true
    ],
];
