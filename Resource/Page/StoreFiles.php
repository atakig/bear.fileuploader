<?php

namespace FileUpload\Resource\Page;

use BEAR\Resource\AbstractObject;
use BEAR\Resource\ResourceInterface;
use BEAR\Sunday\Inject\ResourceInject;
use BEAR\Package\Module\Database\Dbal\Setter\DbSetterTrait;
use BEAR\Sunday\Annotation\Db;
use PDO;
use Ray\Di\Di\Inject;
use Ray\Di\Di\Named;

/**
 * StoreFiles
 *
 * @Db
 */
class StoreFiles extends AbstractObject
{
    use ResourceInject;
    use DbSetterTrait;

    private $img_real_path = '';
    private $img_tmp_dir = '';

    /**
     *
     * @Inject
     * @Named("img_real_path")
     * @Named("img_tmp_dir")
     *
     */
    public function __construct($img_real_path, $img_tmp_dir){
        $this->img_real_path = $img_real_path;
        $this->img_tmp_dir = $img_tmp_dir;
    }

    public $body = [
        'value' => ''
    ];

    // Download File
    public function onGet($id)
    {
        $stmt = $this->db->prepare('SELECT tmp_filename, upload_filename, mime, ext FROM upload_files WHERE id = :id');

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();

//        $data_dir = dirname(__FILE__) . '/../../data/';
        echo $this->img_real_path . $result['tmp_filename'] . '<br />';
        echo $this->img_tmp_dir;
        exit;
        copy($this->img_real_path . $result['tmp_filename'], $this->img_tmp_dir);

//        header("Content-Type: application/pdf");
        header("Content-Type: " . $result['mime']);

        header('Content-Disposition: attachment; filename=' . $result['upload_filename']);
        // readfile($data_dir . 'uploadfiles/' . $result['tmp_filename']);
        readfile($this->img_tmp_dir . $result['tmp_filename']);
        exit();
    }

    // upload file
    public function onPost()
    {
        $tmp_name = $_FILES['selfintro']['tmp_name'];
        $mime = $_FILES['selfintro']['type'];
        $ext = pathinfo($tmp_name, PATHINFO_EXTENSION);
        $tmp_filename = crypt(basename($tmp_name), uniqid());

        $uploadfile = $this->img_real_path . $tmp_filename;

        $memo = $_POST['memo'];

        echo $uploadfile . PHP_EOL;
        echo $tmp_name . PHP_EOL;

        if (move_uploaded_file($tmp_name, $uploadfile)) {
            $sql = <<<SQL
INSERT INTO upload_files(tmp_filename, upload_filename, mime, ext, memo) VALUES (:tmp_filename, :upload_filename, :mime, :ext, :memo)
SQL;
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':tmp_filename', $tmp_filename, PDO::PARAM_STR);
            $stmt->bindValue(':upload_filename', $_FILES['selfintro']['name'], PDO::PARAM_STR);
            $stmt->bindValue(':mime', $mime, PDO::PARAM_STR);
            $stmt->bindValue(':ext', $ext, PDO::PARAM_STR);
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
//        $data_dir = dirname(__FILE__) . '/../../data/';
//        $result = unlink($data_dir . 'uploadfiles/' . $tmp_filename);
        $result = unlink($this->img_real_path . $tmp_filename);

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
