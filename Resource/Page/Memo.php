<?php

namespace FileUpload\Resource\Page;

use BEAR\Resource\AbstractObject;
use BEAR\Resource\ResourceInterface;
use BEAR\Sunday\Inject\ResourceInject;
use BEAR\Package\Module\Database\Dbal\Setter\DbSetterTrait;
use BEAR\Sunday\Annotation\Db;
use PDO;

/**
 * Memo
 *
 * @Db
 */
class Memo extends AbstractObject
{
    use ResourceInject;
    use DbSetterTrait;

    public $body = [
        'name' => ''
    ];

    public function onGet()
    {
        return $this;
    }

    public function onPost()
    {
        $stmt = $this->db->prepare('UPDATE upload_files SET memo = :memo WHERE id = :id');
        $stmt->bindValue(':memo', $_POST['memo'], PDO::PARAM_STR);
        $stmt->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
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
