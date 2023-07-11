<?php

namespace TOTS\LaravelCrudGenerator\Interfaces;

interface FileGeneratorInterface
{
    public function setFilePath() : void;
    public function setFileContent() : void;
    public function generateFile() : void;
    public function generateFileContent() : void;
    public function createFile() : bool;
}
