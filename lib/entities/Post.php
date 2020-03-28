<?php

namespace App\lib\entities;

use App\lib\db\DBExecutor;
use App\lib\validators\PostValidator;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class Post {
    private $id;
    private $pathOrig;
    private $comment;
    private $owner;
    private $time;

    public function __construct(string $_id, string $_pathOrig, string $_comment, string $_ownerlogin, string $_time)
    {
        $this->id = $_id;
        $this->pathOrig = $_pathOrig;
        $this->comment = $_comment;
        $this->owner = $_ownerlogin;
        $this->time = \DateTime::createFromFormat('Y-m-d H:i:s+', $_time);
    }

    public static function CreateNewPost(UploadedFile $file, string $name, string $dir, string $comment, string $login): Post {
        $errno = $file->getError();
        if($errno !== UPLOAD_ERR_OK) {
            throw new Exception("Error: ". $file->getErrorMessage());
        }
        if (!is_dir($dir)) {
            throw new Exception("Error. User's storage not exists");
        }
        $file->move($dir, $name);
        try {
            $curTime = new \DateTime();
            $phId = DBExecutor::AddPhoto($login, $name, $comment, $curTime);
            $post = new Post($phId, $name, $comment, $login, $curTime->format('Y.m.d h:i:s'));
            return $post;
        }
        catch (Exception $ex) {
            throw new Exception('Can not add file');
        }
    }


    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }
        else {
            throw new Exception("Field $name is not defined");
        }
    }

}