<?php

namespace FileUpload\Module\App;

use BEAR\Sunday\Module as SundayModule;
use BEAR\Package\Module\Package\PackageModule;
use BEAR\Package\Provide as ProvideModule;
use Ray\Di\AbstractModule;

/**
 * Application module
 */
class AppModule extends AbstractModule
{
    /**
     * @var array
     */
    private $config;

    /**
     * @param $mode string
     *
     * @throws \LogicException
     */
    public function __construct($mode)
    {
        $packageDir = dirname(dirname(__DIR__));

        $modeConfig = $packageDir . "/config/{$mode}.php";
        if (! file_exists($modeConfig)) {
            throw new \LogicException("Invalid mode {$mode}");
        }
        $this->config = (require $modeConfig) + (require $packageDir . '/config/prod.php');
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        // install package module
        $this->install(new PackageModule($this->config));

        // dependency binding for application
        $this->bind('BEAR\Sunday\Extension\Application\AppInterface')->to('FileUpload\App');
        // install twig
        $this->install(new ProvideModule\TemplateEngine\Twig\TwigModule($this));

        $this->bind()->annotatedWith("img_tmp_path")->toInstance('/img/tmp/');

        $img_real_path = dirname(__FILE__) . '/../../data/uploadfiles/';
        $this->bind()->annotatedWith("img_real_path")->toInstance($img_real_path);

        $img_tmp_dir = dirname(__FILE__) . '/../../public/img/tmp/';
        $this->bind()->annotatedWith("img_tmp_dir")->toInstance($img_tmp_dir);
    }
}
