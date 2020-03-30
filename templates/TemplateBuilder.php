<?php

namespace App\templates;

class TemplateBuilder {
    private $params = [];
    private $filename = '';
    public function __construct(string $file, array $addParams = [])
    {
        $this->filename = $file;
        $this->addParamRange($addParams);
    }
    public function addParam($key, $value) {
        $this->params[$key] = $value;
    }
    public function addParamRange(array $addParams) {
        $this->params = array_merge($this->params, $addParams);
    }
    public function __toString()
    {
        extract($this->params, EXTR_OVERWRITE);
        ob_start();
        require $this->filename;
        $page = ob_get_clean();
        return $page;
    }
}