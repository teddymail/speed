<?php

namespace supjos;

class App
{

    public static $aliases = [
        '@supjos' => __DIR__
    ];

    /**
     * The autoload function to load the needed class
     * @param type $className
     */
    public static function autoload($className)
    {
        if (!empty($className)) {

            $parentPath = '';

            $slashPos = strpos($className, '\\');
            if ($slashPos === FALSE) {
                $nameAlias = $className;
            } else {
                $nameAlias = substr($className, 0, $slashPos);
            }
            foreach (static::$aliases as $name => $dir) {
                if (strcmp($name, '@' . $nameAlias) == 0) {
                    $parentPath = $dir;
                    break;
                }
            }
            $filePath = strtr($parentPath . substr($className, $slashPos), '\\', '/') . '.php';
            if (file_exists($filePath)) {
                require_once($filePath);
            } else {
                exit("Class File {$filePath} Not Found");
            }
        }
    }

}

spl_autoload_register(['supjos\App', 'autoload']);
