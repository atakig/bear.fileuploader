<?php

namespace FileUpload\Resource\Page;

use BEAR\Resource\AbstractObject as Page;
use BEAR\Sunday\Inject\ResourceInject;

/**
 * Index page
 */
class Index extends Page
{
    use ResourceInject;

    /**
     * @var array
     */
    public $body = [
        'result' =>  ''
    ];

    /*
      create table upload_files(
        id INTEGER PRIMARY KEY,
        tmp_filename TEXT,
        upload_filename TEXT,
        ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      );

     create table upload_files_bkup(
         id INTEGER,
         tmp_filename TEXT,
         upload_filename TEXT,
         ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP
     );
     */

    public function onGet($msg = '')
    {
        $data_dir = dirname(__FILE__) . '/../../data/';
        $db = new \SQLite3($data_dir . 'uploadFiles.sqlite');
        $t = $db->query('SELECT * FROM upload_files');
        $result = [];
        $i = 0;
        while($res = $t->fetchArray(SQLITE3_ASSOC)){
              $result[$i]['tmp_filename'] = $res['tmp_filename'];
              $result[$i]['upload_filename'] = $res['upload_filename'];
              $result[$i]['ts'] = $res['ts'];
              $result[$i]['id'] = $res['id'];
              $i++;
          }

        $this['result'] = $result;
        $this['msg'] = $msg;
        return $this;
    }
}
