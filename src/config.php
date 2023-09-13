<?php

return [
    "default_file_path" => env( "LARAVEL_CRUD_GENERATOR_FILE_PATH" ) ?? 'laravel-crud-generator.json',
    "rewrite" => env( "LARAVEL_CRUD_GENERATOR_RERWRITE" ) ?? true,
    "files" => [ "routes", "model", "controller", "migration", "factory", "repository", "test" ],
    "relations" => [],
    "routes" => [
        "use" => [],
        "traits" => [],
        "interfaces" => [],
        "extends" => null,
        "file_path" => "routes/entities",
        "namespace" => null,
        "rewrite" => true
    ],
    "model" => [
        "use" => [],
        "traits" => [],
        "interfaces" => [],
        "extends" => 'Illuminate\Database\Eloquent\Model',
        "file_path" => "app/Models",
        "namespace" => "App\\Models",
        "rewrite" => true
    ],
    "controller" => [
        "use" => [ 'App\\Http\\Controllers\\Controller' ],
        "traits" => [],
        "interfaces" => [],
        "extends" => "Controller",
        "file_path" => "app/Http/Controllers",
        "namespace" => "App\\Http\\Controllers",
        "methods" => [ "list", "fetch", "store", "update", "delete" ],
        "response" => "\\Illuminate\\Http\\JsonResponse",
        "rewrite" => true
    ],
    "repository" => [
        "use" => [],
        "traits" => [],
        "interfaces" => [],
        "extends" => null,
        "file_path" => "app/Repositories",
        "namespace" => "App\\Repositories",
        "methods" => [ "list", "fetch", "store", "update", "delete" ],
        "rewrite" => true
    ],
];
