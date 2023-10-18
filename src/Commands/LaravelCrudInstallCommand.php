<?php

namespace TOTS\LaravelCrudGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class LaravelCrudInstallCommand extends Command
{
    protected $signature = 'crud:install';
    protected $description = 'Install Laravel CRUD generator';

    public function handle()
    {
        $this->newLine();
        $this->titleLine( 'LARAVEL CRUD GENERATOR' );

        $configFilePath = __DIR__ . '/../config.php';
        $laravelConfigPath = base_path( 'config/laravelCrudGenerator.php' );
        File::copy( $configFilePath, $laravelConfigPath );
        $this->line( '<options=bold;fg=white;> ║</><options=bold;fg=blue;>                  📃</><options=bold;fg=white;> Config file created                   </><options=bold;fg=white;>║</>' );

        $jsonFilePath = __DIR__ . '/../laravel-crud-generator.json';
        $laravelPath = base_path( 'laravel-crud-generator.json' );
        File::copy( $jsonFilePath, $laravelPath );
        $this->line( '<options=bold;fg=white;> ║</><options=bold;fg=blue;>                  📃</><options=bold;fg=white;> Json file created                     </><options=bold;fg=white;>║</>' );
        $this->boxLine( 'Laravel CRUD generator has been installed successfully' );
        $this->newLine();
    }

    private function titleLine( $lineText )
    {
        $space = '              ';
        $borderLine = str_repeat( '═', strlen( $lineText ) + strlen( $space ) * 2 + 9 );
        $this->line( '<options=bold;fg=white;> ╔' . $borderLine . '╗ </>' );
        $this->line( '<options=bold;fg=white;> ║' . $space . '</><fg=yellow;> ✨ </><options=bold;fg=bright-magenta;>' . $lineText .'</><fg=yellow;> ✨ </>' . $space . '<options=bold;fg=white;> ║ </>' );
        $this->line( '<options=bold;fg=white;> ╠' . $borderLine . '╣ </>' );
    }

    private function boxLine( $lineText )
    {
        $borderLine = str_repeat( '═', strlen( $lineText ) + 5 );
        $this->line( '<options=bold;fg=white;> ╠' . $borderLine . '╣ </>' );
        $this->line( '<options=bold;fg=white;> ║</><options=bold;fg=bright-green;> ✔ </><options=bold;fg=bright-cyan;>' . $lineText .' </><options=bold;fg=white;> ║ </>' );
        $this->line( '<options=bold;fg=white;> ╚' . $borderLine . '╝ </>' );
    }
}
