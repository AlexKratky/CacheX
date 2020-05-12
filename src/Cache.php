<?php
/**
 * @name Cache.php
 * @link https://alexkratky.com                         Author website
 * @link https://panx.eu/docs/                          Documentation
 * @link https://github.com/AlexKratky/CacheX/          Github Repository
 * @author Alex Kratky <alex@panx.dev>
 * @copyright Copyright (c) 2020 Alex Kratky
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @description Class to work with cache. Part of panx-framework.
 */

declare (strict_types = 1);

namespace AlexKratky;

use AlexKratky\Logger;
use AlexKratky\FileStream;

class Cache {

    /**
     * @var int Default cache live in seconds.
     */
    const CACHE_TIME = 10;

    private static $directory = null;

    /**
     * Creates /cache/ folder.
     */
    public static function init(): void {
        self::$directory = self::$directory ?? $_SERVER['DOCUMENT_ROOT'] . "/../cache/";
        if(!file_exists('panx://' . self::$directory)) {
            if(!mkdir('panx://' . self::$directory))
                Logger::log("Failed to create cache folder");
        }
    }

    /**
     * Saves data to cache file.
     * @param string $name The name of variable.
     * @param mixed $data The data that will be saved. Data will be edited by json_encode()
    */
    public static function save(string $name, $data): void {
        self::$directory = self::$directory ?? $_SERVER['DOCUMENT_ROOT'] . "/../cache/";
        file_put_contents('panx://' . self::$directory . $name, json_encode($data));
    }

    /**
     * Obtain the data of specified variable.
     * @param string $name The name of variable.
     * @param int $cacheTime The cache live in seconds, if you pass null, then the default time will be used.
     * @return mixed|false Returns false if there is no cache with that variable name or when the cache is expired, else returns decoded data using json_decode()
    */
    public static function get(string $name, ?int $cacheTime = null) {
        self::$directory = self::$directory ?? $_SERVER['DOCUMENT_ROOT'] . "/../cache/";
        if(file_exists('panx://' . self::$directory . $name) && filectime('panx://' . self::$directory . $name) + ($cacheTime == null ? self::CACHE_TIME : $cacheTime) > time()) {
            return json_decode(file_get_contents('panx://' . self::$directory . $name), true);
        } else {
            return false;
        }
    }

    /**
     * Destroy specified cache.
     * @param string $name The name of variable.
     */
    public static function destroy(string $name): bool {
        self::$directory = self::$directory ?? $_SERVER['DOCUMENT_ROOT'] . "/../cache/";
        if(file_exists('panx://' . self::$directory . $name))
            return unlink('panx://' . self::$directory . $name);
        else return false;
    }

    /**
     * Clear all cache files older then $time.
     * Can be called using php panx-worker clear cache old.
     * @param string $dir The basedir, used when called from terminal.
     * @param int $time The time in seconds, default value is 86400 (1 day).
     */
    public static function clearUnused($dir = null, $time = 86400) {
        if($dir === null) {
            $dir = self::$directory ?? $_SERVER['DOCUMENT_ROOT'] . "/../cache/";
        }
        $c = scandir('panx://' . $dir);
        foreach ($c as $f) {
            if($f == "." || $f == "..") continue;
            if(filemtime('panx://' . $dir . $f) + $time < time()) {
                unlink('panx://' . $dir . $f);
            }
        }
    }

    /**
     * Clear all cache files. Used in updates.
     * Can be called using php panx-worker clear cache
     * @param string $dir The basedir, used when called from terminal.
     */
    public static function clearAll($dir = null) {
        if($dir === null) {
            $dir = self::$directory ?? $_SERVER['DOCUMENT_ROOT'] . "/../cache/";
        }
        $c = scandir('panx://' . $dir);
        foreach ($c as $f) {
            if($f == "." || $f == "..") continue;
            unlink('panx://' . $dir . $f);
        }
        //Logger::log("Cache was cleared.", "main.log", $dir);
    }

    public static function setDirectory($directory) {
        self::$directory = $directory;
    }

}
