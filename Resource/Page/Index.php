<?php

namespace FileUpload\Resource\Page;

use BEAR\Resource\AbstractObject as Page;
use BEAR\Sunday\Inject\ResourceInject;
use BEAR\Resource\ResourceInterface;
use BEAR\Package\Module\Database\Dbal\Setter\DbSetterTrait;
use BEAR\Sunday\Annotation\Db;
use Ray\Di\Di\Inject;
use Ray\Di\Di\Named;


/**
 * Index page
 *
 * @Db
 */
class Index extends Page
{
    use ResourceInject;
    use DbSetterTrait;

    private $img_web_path = '';

    /**
     * @Inject
     * @Named("img_web_path")
     */
    public function  __construct($img_web_path){
        $this->img_web_path = $img_web_path;
    }
    /**
     * @var array
     */
    public $body = [
        'result' =>  ''
    ];

    public function onGet($msg = '')
    {
        $stmt = $this->db->query('SELECT * FROM upload_files');
        $this['result'] = $stmt->fetchAll();
        $this['msg'] = $msg;
        $this["img_web_path"] = $this->img_web_path;
        return $this;
    }
}
