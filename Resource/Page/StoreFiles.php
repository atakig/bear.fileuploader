<?php

namespace FileUpload\Resource\Page;

use BEAR\Resource\AbstractObject;
use BEAR\Resource\ResourceInterface;
use BEAR\Sunday\Inject\ResourceInject;
use BEAR\Package\Module\Database\Dbal\Setter\DbSetterTrait;
use BEAR\Sunday\Annotation\Db;
use PDO;

/**
 * StoreFiles
 *
 * @Db
 */
class StoreFiles extends AbstractObject
{
    use ResourceInject;
    use DbSetterTrait;


    public $body = [
        'value' => ''
    ];

    // Download File
    public function onGet($id)
    {
        $stmt = $this->db->prepare('SELECT tmp_filename, upload_filename FROM upload_files WHERE id = :id');

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();

        header("Content-Type: application/pdf");
        header('Content-Disposition: attachment; filename=' . $result['upload_filename']);
        readfile($data_dir . 'uploadfiles/' . $result['tmp_filename']);
        exit();
    }

    // upload file
    public function onPost()
    {
        $data_dir = dirname(__FILE__) . '/../../data/';
        $tmp_filename = basename($_FILES['selfintro']['tmp_name']);
        $uploadfile = $data_dir . 'uploadfiles/'. $tmp_filename;

        $memo = $_POST['memo'];

        if (move_uploaded_file($_FILES['selfintro']['tmp_name'], $uploadfile)) {
            $stmt = $this->db->prepare('INSERT INTO upload_files(tmp_filename, upload_filename, memo) VALUES (:tmp_filename, :upload_filename, :memo)');
            $stmt->bindValue(':tmp_filename', $tmp_filename, PDO::PARAM_STR);
            $stmt->bindValue(':upload_filename', $_FILES['selfintro']['name'], PDO::PARAM_STR);
            $stmt->bindValue(':memo', $memo, PDO::PARAM_STR);
            $stmt->execute();
            $msg = urlencode("アップロード成功: " . $_FILES['selfintro']['name']);
        } else {
            $msg = urlencode("アップロード失敗");
        }

        // redirect
        $this->headers = ['Location' => '/?msg=' . $msg];
        return $this;

    }


    public function onDelete($id)
    {
        $stmt = $this->db->prepare('SELECT tmp_filename, upload_filename, ts FROM upload_files WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        $tmp_filename = $result['tmp_filename'];
        $upload_filename = $result['upload_filename'];
        $ts = $result['ts'];

        $msg = '';
        // move to backup table
        $stmt = $this->db->prepare('INSERT INTO upload_files_bkup(id, tmp_filename, upload_filename, ts) VALUES (:id, :tmp_filename, :upload_filename, :ts)');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':tmp_filename', $tmp_filename, PDO::PARAM_STR);
        $stmt->bindValue(':upload_filename', $upload_filename, PDO::PARAM_STR);
        $stmt->bindValue(':ts', $ts, PDO::PARAM_STR);

        $result = $stmt->execute();
        if ($result === false){
            $msg = urlencode('バックアップデータの作成に失敗しました');
            $this->headers = ['Location' => '/?msg=Faild-Data-BackUp'];
            return $this;
        }

        // Delete data
        $stmt = $this->db->prepare('DELETE FROM upload_files WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $result = $stmt->execute();
        if ($result === false){
            $msg = urlencode('データの削除に失敗しました');
            $this->headers = ['Location' => "/?msg=$msg"];
            return $this;
        }

        // Delete file
        $data_dir = dirname(__FILE__) . '/../../data/';
        $result = unlink($data_dir . 'uploadfiles/' . $tmp_filename);
        if ($result === false){
            $msg = urlencode('ファイルの削除に失敗しました');
            $this->headers = ['Location' => "/?msg=$msg"];
            return $this;
        }

        // redirect
        $msg = urlencode('削除 成功');
        $this->headers = ['Location' => "/?msg=$msg"];
        return $this;
    }
}
