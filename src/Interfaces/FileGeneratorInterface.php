<?php

namespace TOTS\LaravelCrudGenerator\Interfaces;

interface FileGeneratorInterface
{
    public function setGeneratorType() : void;
    public function setFilePath() : void;
    // public function setFileUseUrls() : void;
    public function initFileContentFromStub() : void;
    public function generateFile() : void;
    public function generateFileContent() : void;
    public function createFile() : bool;
}
