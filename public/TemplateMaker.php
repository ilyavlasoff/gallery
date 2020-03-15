<?php

class Template {
    private static $PathValues = [];

    public static function AddPathValueArr(array $newVals) {
        self::$PathValues = $newVals;
    }

    public static function AddPathValue(string $key, string $value) {
        self::$PathValues[$key] = $value;
    }

    private $FileName = '';
    private $Replacers = [];

    public function __construct(string $fileTitle, array $argval = [])
    {
        if (isset(self::$PathValues[$fileTitle])) {
            $this->FileName= self::$PathValues[$fileTitle];
        }
        else {
            throw new Exception('Path to file was not specified');
        }
        if (!file_exists($this->FileName)) {
            throw new Exception('File not exists');
        }
        $this->Replacers = $argval;
    }

    private function ReplaceTemplate(): string {
        if (!file_exists($this->FileName)) {
            throw new Exception('File not exists');
        }
        $templateText = file_get_contents($this->FileName);
        foreach ($this->Replacers as $template=>$replacer) {
            $templateText = str_replace('{'.strtoupper($template).'}', $replacer, $templateText);
        }
        $templateText = preg_replace('/{([A-Z0-9_]+)}/', '', $templateText);
        return $templateText;
    }

    public function AddStringReplacer($name, $rep) {
        $this->Replacers[$name] = strval($rep);
    }

    public function __toString()
    {
        return $this->ReplaceTemplate();
    }
}
?>