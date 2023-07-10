<?php

namespace TOTS\LaravelCrudGenerator\Interfaces;

interface FileGeneratorInterface
{
    public function setGeneratorType() : void;
    public function setFilePath() : void;
    public function initFileContent() : void;
    public function generateFile() : void;
    public function generateFileContent() : void;
    public function createFile() : bool;
}
