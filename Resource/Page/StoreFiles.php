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

    public function onGet()
    {
        return $this;
    }


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

}
