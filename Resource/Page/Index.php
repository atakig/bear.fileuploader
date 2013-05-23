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
     * sqlite> create table upload_files(
   ...> id INTEGER PRIMARY KEY,
   ...> tmp_filename TEXT,
   ...> upload_filename TEXT,
   ...> ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   ...> );
sqlite> select * from upload_files;
1|/private/var/tmp/phpMpnHrD|atakig.pdf|2013-05-23 15:29:54
     */

    public function onGet($name = 'BEAR.Sunday')
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
              $i++;
          }

        $this['result'] = $result;
        return $this;
    }
}
