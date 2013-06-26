<?php

namespace FileUpload\Resource\Page;

use BEAR\Resource\AbstractObject as Page;
use BEAR\Sunday\Inject\ResourceInject;
use FileUpload\Module\App\AppModule;
use Ray\Di\Di\Inject;
use Ray\Di\Di\Named;

/**
 * Index page
 */
class Index extends Page
{
    use ResourceInject;
    private $conn = '';

    /**
     * @var array
     */
    public $body = [
        'result' =>  ''
    ];

    /**
     * @Inject
     * @Named("conn")
     */
    public function __construct($conn) {
        echo "######" .$conn . "######" . PHP_EOL;
        $this->conn = $conn;
    }
    /*
      create table upload_files(
        id INTEGER PRIMARY KEY,
        tmp_filename TEXT,
        upload_filename TEXT,
        memo TEXT,
        ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      );

     create table upload_files_bkup(
         id INTEGER,
         tmp_filename TEXT,
         upload_filename TEXT,
         memo TEXT,
         ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP
     );
     */

    public function onGet($msg = '')
    {
        $injecter = Inject::create([new AppModule]);
var_dump($injecter);
//        $data_dir = dirname(__FILE__) . '/../../data/';
//        $db = new \SQLite3($data_dir . 'uploadFiles.sqlite');
        echo "----------------" . PHP_EOL;
        echo $this->conn . PHP_EOL;
        echo "----------------" . PHP_EOL;
        $db = new \SQLite3($this->conn);
        $t = $db->query('SELECT * FROM upload_files');
        $result = [];
        $i = 0;
        while($res = $t->fetchArray(SQLITE3_ASSOC)){
              $result[$i]['tmp_filename'] = $res['tmp_filename'];
              $result[$i]['upload_filename'] = $res['upload_filename'];
              $result[$i]['memo'] = $res['memo'];
              $result[$i]['ts'] = $res['ts'];
              $result[$i]['id'] = $res['id'];
              $i++;
          }

        $this['result'] = $result;
        $this['msg'] = $msg;
        return $this;
    }
}
