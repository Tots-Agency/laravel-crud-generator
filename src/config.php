<?php

return [
    "default_file_path" => env( "LARAVEL_CRUD_GENERATOR_FILE_PATH" ) ?? 'laravel-crud-generator.json',
    "rewrite" => env( "LARAVEL_CRUD_GENERATOR_RERWRITE" ) ?? true,
    "files" => [ "routes", "model", "controller", "migration", "factory", "service", "test" ],
    "relations" => [],
    "model" => [
        "traits" => [],
        "interfaces" => [],
        "extends" => null,
        "file_path" => "app/Models",
        "namespace" => "App\\Models",
        "rewrite" => true
    ],
    "controller" => [
        "traits" => [],
        "interfaces" => [],
        "extends" => "Illuminate\\Routing\\Controller as BaseController",
        "methods" => [ "list", "store", "update", "delete" ],
        "rewrite" => false
    ],
];
