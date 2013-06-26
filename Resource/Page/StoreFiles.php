<?php

namespace FileUpload\Resource\Page;

use BEAR\Resource\AbstractObject;
use BEAR\Resource\ResourceInterface;
use BEAR\Sunday\Inject\ResourceInject;
use Ray\Di\Di\Inject;
use Ray\Di\Di\Named;

/**
 * Untitled
 */
class StoreFiles extends AbstractObject
{
    use ResourceInject;

    private $db = null;

    public $body = [
        'value' => ''
    ];

    /**
     * @Inject
     * @Named("database")
     */
    public function __construct($database) {
        $this->db = $database;
    }

    // Download File
    public function onGet($id)
    {
//        $data_dir = dirname(__FILE__) . '/../../data/';
//        $db = new \SQLite3($data_dir . 'uploadFiles.sqlite');
//        $stmt = $db->prepare('SELECT tmp_filename, upload_filename FROM upload_files WHERE id = :id');
        $stmt = $this->db->prepare('SELECT tmp_filename, upload_filename FROM upload_files WHERE id = :id');

        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $t = $stmt->execute();
        while($res = $t->fetchArray(SQLITE3_ASSOC)){
            $read_file = $res['tmp_filename'];
            $download_file = $res['upload_filename'];
            break;
        }
        header("Content-Type: application/pdf");
        header('Content-Disposition: attachment; filename=' . $download_file);
        readfile($data_dir . 'uploadfiles/' . $read_file);
        exit();
    }

    // upload file
    public function onPost()
    {
        $data_dir = dirname(__FILE__) . '/../../data/';
        $tmp_filename = basename($_FILES['selfintro']['tmp_name']);
        $uploadfile = $data_dir . 'uploadfiles/'. $tmp_filename;

        $memo = $_POST['memo'];

        $msg = '';
        if (move_uploaded_file($_FILES['selfintro']['tmp_name'], $uploadfile)) {
            //$db = new \SQLite3($data_dir . 'uploadFiles.sqlite');

            $stmt = $this->db->prepare('INSERT INTO upload_files(tmp_filename, upload_filename, memo) VALUES (:tmp_filename, :upload_filename, :memo)');
            $stmt->bindValue(':tmp_filename', $tmp_filename, SQLITE3_TEXT);
            $stmt->bindValue(':upload_filename', $_FILES['selfintro']['name'], SQLITE3_TEXT);
            $stmt->bindValue(':memo', $memo, SQLITE3_TEXT);
            $result = $stmt->execute();
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
        $data_dir = dirname(__FILE__) . '/../../data/';

//        $db = new \SQLite3($data_dir . 'uploadFiles.sqlite');
        $stmt = $this->db->prepare('SELECT tmp_filename, upload_filename, ts FROM upload_files WHERE id = :id');
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $t = $stmt->execute();
        while($res = $t->fetchArray(SQLITE3_ASSOC)){
            $tmp_filename = $res['tmp_filename'];
            $upload_filename = $res['upload_filename'];
            $ts = $res['ts'];
            break;
        }
        $msg = '';
        // move to backup table
        $stmt = $this->db->prepare('INSERT INTO upload_files_bkup(id, tmp_filename, upload_filename, ts) VALUES (:id, :tmp_filename, :upload_filename, :ts)');
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $stmt->bindValue(':tmp_filename', $tmp_filename, SQLITE3_TEXT);
        $stmt->bindValue(':upload_filename', $upload_filename, SQLITE3_TEXT);
        $stmt->bindValue(':ts', $ts, SQLITE3_TEXT);

        $result = $stmt->execute();
        if ($result === false){
            $msg = urlencode('バックアップデータの作成に失敗しました');
            $this->headers = ['Location' => '/?msg=Faild-Data-BackUp'];
            return $this;
        }

        // Delete data
        $stmt = $this->db->prepare('DELETE FROM upload_files WHERE id = :id');
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        if ($result === false){
            $msg = urlencode('データの削除に失敗しました');
            $this->headers = ['Location' => "/?msg=$msg"];
            return $this;
        }

        // Delete file
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
