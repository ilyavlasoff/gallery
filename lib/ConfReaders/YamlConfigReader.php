<?php

namespace App\lib\ConfReaders;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Yaml\Parser;

class YamlConfigReader
{

    private $dir = root . '/config/';
    private $data;

    public function __construct(string $filename, string $dirname = '')
    {
        if ($dirname) {
            $this->dir = $dirname;
        }
        $yaml = new Parser();
        $filepath = $this->dir . $filename;
        if (!file_exists($filepath)) {
            throw new Exception("File can not be load $filepath");
        }
        $content = file_get_contents($filepath);
        $this->data = $yaml->parse($content);
    }

    public function get(string $property)
    {
        return $this->data[$property];
    }
}