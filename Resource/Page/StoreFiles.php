<?php

namespace FileUpload\Resource\Page;

use BEAR\Resource\AbstractObject;
use BEAR\Sunday\Inject\ResourceInject;
use Ray\Di\Di\Inject;

/**
 * Untitled
 */
class StoreFiles extends AbstractObject
{
    use ResourceInject;

    public $body = [
        'value' => ''
    ];

    // Download File
    public function onGet($id)
    {
        $data_dir = dirname(__FILE__) . '/../../data/';
        $db = new \SQLite3($data_dir . 'uploadFiles.sqlite');
        $stmt = $db->prepare('SELECT tmp_filename, upload_filename FROM upload_files WHERE id = :id');
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

        if (move_uploaded_file($_FILES['selfintro']['tmp_name'], $uploadfile)) {
            $db = new \SQLite3($data_dir . 'uploadFiles.sqlite');
            $stmt = $db->prepare('INSERT INTO upload_files(tmp_filename, upload_filename) VALUES (:tmp_filename, :upload_filename)');
            $stmt->bindValue(':tmp_filename', $tmp_filename, SQLITE3_TEXT);
            $stmt->bindValue(':upload_filename', $_FILES['selfintro']['name'], SQLITE3_TEXT);
            $result = $stmt->execute();
            $this->body['value'] = "File is valid, and was successfully uploaded.\n" . var_dump($result);
        } else {
            $this->body['value'] = "Possible file upload attack!\n";
        }

        return $this;
    }


    public function onDelete($id)
    {
        $data_dir = dirname(__FILE__) . '/../../data/';

        $db = new \SQLite3($data_dir . 'uploadFiles.sqlite');
        $stmt = $db->prepare('SELECT tmp_filename, upload_filename, ts FROM upload_files WHERE id = :id');
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $t = $stmt->execute();
        while($res = $t->fetchArray(SQLITE3_ASSOC)){
            $tmp_filename = $res['tmp_filename'];
            $upload_filename = $res['upload_filename'];
            $ts = $res['ts'];
            break;
        }
        // move to backup table
        $stmt = $db->prepare('INSERT INTO upload_files_bkup(id, tmp_filename, upload_filename, ts) VALUES (:id, :tmp_filename, :upload_filename, :ts)');
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $stmt->bindValue(':tmp_filename', $tmp_filename, SQLITE3_TEXT);
        $stmt->bindValue(':upload_filename', $upload_filename, SQLITE3_TEXT);
        $stmt->bindValue(':ts', $ts, SQLITE3_TEXT);

        $result = $stmt->execute();
        if ($result === false){
            $this->body['value'] = "Faild backup delete data";
            return $this;
        }

        // Delete data
        $stmt = $db->prepare('DELETE FROM upload_files WHERE id = :id');
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        if ($result === false){
            $this->body['value'] = "Faild Delete Data";
            return $this;
        }

        // Delete file
        $result = unlink($data_dir . 'uploadfiles/' . $tmp_filename);
        if ($result === false){
            $this->body['value'] = "Faild Delete File";
            return $this;
        }

        $this->body['value'] = 'Success Delete';
        return $this;
    }

}
