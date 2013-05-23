<?php

namespace FileUpload\Module;

use Ray\Di\AbstractModule;

/**
 * Production module
 */
class ProdModule extends AbstractModule
{
    protected function configure()
    {
        $this->install(new App\AppModule('prod'));
    }
}
