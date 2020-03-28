<?php

namespace App\lib\webscripts;

use Symfony\Component\HttpFoundation\Response;

class Imageloader {

    private static $loadingPath = root . '/uploads/';
    private static $defaultPath = root . '/public/media/';

    public static function getImage(string $username, string $filename, string $size): Response {
        if ($username === '0') {
            return self::getDefaultImage($filename);
        }
        $fullname = self::$loadingPath . "$username/$filename";
        if (!file_exists($fullname)) {
            return self::getDefaultImage('fileNotFound.png');
        }
        $info   = getimagesize($fullname);
        $width = $info[0];
        $height = $info[1];
        $type = $info[2];
        $mime = $info['mime'];
        switch ($type) {
            case 1:
                $img = imageCreateFromGif($fullname);
                imageSaveAlpha($img, true);
                break;
            case 2:
                $img = imageCreateFromJpeg($fullname);
                break;
            case 3:
                $img = imageCreateFromPng($fullname);
                imageSaveAlpha($img, true);
                break;
        }
        if ($size === 'orig') {
            ob_start();
            readfile($fullname);
            $file = ob_get_contents();
            return new Response(
                $file,
                Response::HTTP_OK,
                [
                    'Content-type' => $mime,
                    'Content-length' => filesize($fullname)
                ]
            );
        }
        list($w, $h) = explode('*', $size);
        if (!is_numeric($w) && !is_numeric($h)) {
            return new Response("", Response::HTTP_BAD_REQUEST);
        }
        if (empty($w)) {
            $w = ceil($h / ($height / $width));
        }
        if (empty($h)) {
            $h = ceil($w / ($width / $height));
        }
        $tmp = imageCreateTrueColor($w, $h);

        if ($type == 1 || $type == 3) {
            imagealphablending($tmp, true);
            imageSaveAlpha($tmp, true);
            $transparent = imagecolorallocatealpha($tmp, 0, 0, 0, 127);
            imagefill($tmp, 0, 0, $transparent);
            imagecolortransparent($tmp, $transparent);
        }

        $tw = ceil($h / ($height / $width));
        $th = ceil($w / ($width / $height));

        if ($tw < $w) {
            imageCopyResampled($tmp, $img, ceil(($w - $tw) / 2), 0, 0, 0, $tw, $h, $width, $height);
        }
        else {
            imageCopyResampled($tmp, $img, 0, ceil(($h - $th) / 2), 0, 0, $w, $th, $width, $height);
        }
        $img = $tmp;
        ob_start();
        imagepng($img);
        $file = ob_get_contents();
        return new Response(
            $file,
            Response::HTTP_OK,
            [
                'Content-type' => $mime,
                'Content-length' => filesize($fullname)
            ]
        );
    }

    public static function getDefaultImage(string $filename): Response {
        $fullname = self::$defaultPath . $filename;
        if (!file_exists($fullname)) {
            return new Response("", Response::HTTP_BAD_REQUEST);
        }
        $info = getimagesize($fullname);
        $mime = $info['mime'];
        ob_start();
        readfile($fullname);
        $file = ob_get_contents();
        return new Response(
            $file,
            Response::HTTP_OK,
            [
                'Content-type' => $mime,
                'Content-length' => filesize($fullname)
            ]
        );
    }
}