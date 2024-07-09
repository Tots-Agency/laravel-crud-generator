<?php

return [
    "default_file_path" => env( "LARAVEL_CRUD_GENERATOR_FILE_PATH" ) ?? 'laravel-crud-generator.json',
    "rewrite" => env( "LARAVEL_CRUD_GENERATOR_RERWRITE" ) ?? false,
    "files" => [ "routes", "model", "controller", "migration", "resource", "factory", "service", "test", "mock" ],
    "relations" => [],
    "routes" => [
        "use" => [],
        "traits" => [],
        "interfaces" => [],
        "extends" => null,
        "file_path" => "routes/entities",
        "namespace" => null,
        "rewrite" => false
    ],
    "model" => [
        "use" => [],
        "traits" => [ 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory' ],
        "interfaces" => [],
        "extends" => 'Illuminate\\Database\\Eloquent\\Model',
        "file_path" => "app/Models",
        "namespace" => "App\\Models",
        "rewrite" => false
    ],
    "controller" => [
        "use" => [],
        "traits" => [],
        "interfaces" => [],
        "extends" => 'App\\Http\\Controllers\\Controller',
        "file_path" => "app/Http/Controllers",
        "namespace" => "App\\Http\\Controllers",
        "methods" => [ "list", "show", "store", "update", "delete" ],
        "response" => "\\Illuminate\\Http\\JsonResponse",
        "rewrite" => false
    ],
    "request" => [
        "use" => [],
        "traits" => [],
        "interfaces" => [],
        "extends" => 'Illuminate\\Foundation\\Http\\FormRequest',
        "file_path" => "app/Http/Requests",
        "namespace" => "App\\Http\\Requests",
        "methods" => [ "list", "show", "store", "update", "delete" ],
        "rewrite" => false
    ],
    "resource" => [
        "use" => [],
        "traits" => [],
        "interfaces" => [],
        "extends" => 'Illuminate\\Http\\Resources\\Json\\JsonResource',
        "file_path" => "app/Http/Resources",
        "namespace" => "App\\Http\\Resources",
        "rewrite" => false
    ],
    "service" => [
        "use" => [],
        "traits" => [],
        "interfaces" => [],
        "extends" => null,
        "file_path" => "app/Services",
        "namespace" => "App\\Services",
        "static_methods" => false,
        "methods" => [ "list", "fetch", "store", "update", "delete" ],
        "rewrite" => false
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
        "rewrite" => false
    ],
    "factory" => [
        "use" => [ 'Illuminate\\Support\\Str' ],
        "traits" => [],
        "interfaces" => [],
        "extends" => 'Illuminate\\Database\\Eloquent\\Factories\\Factory',
        "file_path" => "database/factories",
        "namespace" => "Database\\Factories",
        "rewrite" => false
    ],
    "mock" => [
        "use" => [],
        "traits" => [],
        "interfaces" => [],
        "extends" => 'Illuminate\\Database\\Seeder',
        "file_path" => "database/seeders/mocks",
        "namespace" => "Database\\Seeders\\Mocks",
        "count" => 50,
        "rewrite" => false
    ],
    "test" => [
        "use" => [ 'Illuminate\\Foundation\\Testing\\WithFaker' ],
        "traits" => [ 'Illuminate\\Foundation\\Testing\\RefreshDatabase' ],
        "interfaces" => [],
        "extends" => 'Tests\\TestCase',
        "file_path" => "tests/Feature",
        "namespace" => "Tests\\Feature",
        "rewrite" => false
    ]
];
