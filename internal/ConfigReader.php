<?php
    class ConfigReader {
        private static $configPath="../config/dbconf.json";
        private $configArr;

        public function __construct(string $customPath = "") {
            if ($customPath !== "") {
                self::$configPath = $customPath;
            }
            if (preg_match("/.ini/", self::$configPath)) {
                $this->configArr = parse_ini_file(file_get_contents(self::$configPath));
            }
            elseif (preg_match("/.json/", self::$configPath))
            {
                $this->configArr = json_decode(file_get_contents(self::$configPath), true);
            }
        }

        public function getValue(string $name) : string{
            if (isset($this->configArr[$name])) {
                return $this->configArr[$name];
            }
            else {
                throw new Exception('Field not exists');
            }
        }

    }
?>