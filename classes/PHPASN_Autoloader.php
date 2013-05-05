<?php
/*
 * This file is part of PHPASN1 written by Friedrich Große.
 * 
 * Copyright © Friedrich Große, Berlin 2012-2013
 * 
 * PHPASN1 is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PHPASN1 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PHPASN1.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace PHPASN1;
 
class PHPASN_Autoloader {

    const CLASSFILE_PATTERN = '/([[:alnum:]_]+)\.class\.php/';

    private static $cacheDirectoryPath;

    private $index = array();

    public static function getInstance() {
        static $instance;

        if($instance === null) {
                $instance = new self();
        }

        return $instance;
    }

    private function __construct() {
        $this->loadIndexFromCacheFile();
    }

    private function loadIndexFromCacheFile() {
        $cacheFile = $this->getCacheFilePath();        

        if(file_exists($cacheFile) == false) {
            return;
        }

        include $cacheFile;

        if(!isset($dataset['classes'])) {
            return;
        }

        $this->index = $dataset['classes'];
    }

    protected function getCacheFilePath() {
        return self::$cacheDirectoryPath . 'phpasn1-autoloader.cache';
    }

    public static function register($cacheDirectoryPath=null) {
        if(!isset($cacheDirectoryPath)) {
            $cacheDirectoryPath = __DIR__ . '/cache/';
        }
        self::$cacheDirectoryPath = $cacheDirectoryPath;

        spl_autoload_register('\PHPASN1\PHPASN_Autoloader::autoload');
    }

    public static function autoload($className) {
        $simpleClassName = substr($className, strrpos($className, '\\') + 1);
        $instance = self::getInstance();
        require_once $instance->getPathOfClass($simpleClassName);
    }

    private function getPathOfClass($className) {
        if( !array_key_exists($className, $this->index) || file_exists($this->index[$className]) == false) {
            $this->generateIndex();
            $this->saveIndexToCacheFile();
            
            if(!array_key_exists($className, $this->index)) {
                throw new \Exception("Unable to locate file containing class \"{$className}\".");
            }
        }

        return $this->index[$className];
    }

    public function generateIndex() {
        $this->index = array();
        $this->recursiveSearch();
    }

    private function recursiveSearch($path = null) {
        if($path == null) {
            $path = __DIR__;
        }

        $directoryHandle = @opendir($path);

        while(($fileName = readdir($directoryHandle)) !== false) {
            if($fileName[0] == '.') {
                continue;
            }

            $currentFile = $path . DIRECTORY_SEPARATOR . $fileName;

            if(is_dir($currentFile)) {
                $this->recursiveSearch($currentFile);
            } else {
                if(preg_match(self::CLASSFILE_PATTERN, $currentFile, $matches) == 1) {
                        $className = $matches[1];
                        $this->index[$className] = $currentFile;
                    }
            }
        }

        closedir($directoryHandle);
    }

    private function saveIndexToCacheFile() {
        if(file_exists(self::$cacheDirectoryPath) == false) {
            if(mkdir(self::$cacheDirectoryPath) == false) {
                throw new \Exception("Could not create Autoloader cache directory ({self::$cacheDirectoryPath})");
            }
        }
        
        $newDataset = array(
            'content' => 'autoloader-classes',
            'timeCreated' => time(),
            'classes' => $this->index,
        );

        $fileContent = '<?php $dataset = ' . var_export($newDataset, true) . '; ?>';
        return file_put_contents($this->getCacheFilePath(), $fileContent);
    }

}

?>