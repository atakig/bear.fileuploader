<?php

namespace FileUpload\Resource\Page;

use BEAR\Resource\AbstractObject as Page;
use BEAR\Sunday\Inject\ResourceInject;
use BEAR\Resource\ResourceInterface;
use BEAR\Package\Module\Database\Dbal\Setter\DbSetterTrait;
use BEAR\Sunday\Annotation\Db;


/**
 * Index page
 *
 * @Db
 */
class Index extends Page
{
    use ResourceInject;
    use DbSetterTrait;

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
        return $this;
    }
}
