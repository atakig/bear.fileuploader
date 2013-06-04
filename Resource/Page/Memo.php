<?php

namespace FileUpload\Resource\Page;

use BEAR\Resource\AbstractObject;
use BEAR\Sunday\Inject\ResourceInject;
use Ray\Di\Di\Inject;

/**
 * Untitled
 */
class Memo extends AbstractObject
{
    use ResourceInject;

    public $body = [
        'name' => ''
    ];

    public function onGet()
    {
        return $this;
    }

    public function onPost()
    {
        $data_dir = dirname(__FILE__) . '/../../data/';
        $db = new \SQLite3($data_dir . 'uploadFiles.sqlite');
        $stmt = $db->prepare('UPDATE upload_files SET memo = :memo WHERE id = :id');
        $stmt->bindValue(':memo', $_POST['memo'], SQLITE3_TEXT);
        $stmt->bindValue(':id', $_POST['id'], SQLITE3_INTEGER);
        $result = $stmt->execute();
        if ($result === false) {
            $msg = urlencode('メモの更新に失敗しました');
            $this->headers = ['Location' => "/?msg=$msg"];
            return $this;
        } else {
            $msg = urlencode('メモを登録しました');
            $this->headers = ['Location' => "/?msg=$msg"];
            return $this;
        }
    }

}
