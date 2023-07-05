<?php

return [
    "default_file_path" => env( "LARAVEL_CRUD_GENERATOR_FILE_PATH" ) ?? 'laravel-crud-generator.json',
    "rerwrite" => env( "LARAVEL_CRUD_GENERATOR_RERWRITE" ) ?? false,
    "files" => [ "routes", "model", "controller", "migration", "factory", "service", "tests" ],
    "relations" => [],
    "model" => [
        "traits" => [],
        "interfaces" => [],
        "inheritance" => "Illuminate\\Database\\Eloquent\\Model",
        "file-path" => "app/Models",
        "table-primary-key" => "id",
        "rewrite" => false
    ],
    "controller" => [
        "traits" => [],
        "interfaces" => [],
        "inheritance" => "Illuminate\\Routing\\Controller as BaseController",
        "methods" => [ "list", "store", "update", "delete" ]
    ],
];
