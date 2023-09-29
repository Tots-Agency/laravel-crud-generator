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
        "extends" => 'Illuminate\\Database\\Eloquent\\Model',
        "file_path" => "app/Models",
        "namespace" => "App\\Models",
        "rewrite" => true
    ],
    "controller" => [
        "use" => [],
        "traits" => [],
        "interfaces" => [],
        "extends" => 'App\\Http\\Controllers\\Controller',
        "file_path" => "app/Http/Controllers",
        "namespace" => "App\\Http\\Controllers",
        "methods" => [ "list", "fetch", "store", "update", "delete" ],
        "response" => "\\Illuminate\\Http\\JsonResponse",
        "rewrite" => true
    ],
    "request" => [
        "use" => [],
        "traits" => [],
        "interfaces" => [],
        "extends" => 'Illuminate\\Foundation\\Http\\FormRequest',
        "file_path" => "app/Http/Requests",
        "namespace" => "App\\Http\\Requests",
        "methods" => [ "list", "fetch", "store", "update", "delete" ],
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
    "migration" => [
        "use" => [ 'Illuminate\\Database\\Schema\\Blueprint', 'Illuminate\\Support\\Facades\\Schema' ],
        "traits" => [],
        "interfaces" => [],
        "extends" => "Illuminate\\Database\\Migrations\\Migration",
        "file_path" => "database/migrations",
        "namespace" => null,
        "timestamps" => true,
        "softDeletes" => true,
        "id" => true,
        "rewrite" => true
    ],
];
