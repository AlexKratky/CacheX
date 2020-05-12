# Cache

Class to work with cache.

### Installation

`composer require alexkratky/cachex`

### Usage

```php
require 'vendor/autoload.php';

use AlexKratky\Cache;
use AlexKratky\Logger;

Cache::setDirectory(__DIR__ . '/cache/');
Logger::setDirectory(__DIR__ . '/cache/');

Cache::save("test.json", array(
    "name" => "Alex"
));
```

### Working with Cache

Working with Cache in panx framework is quite easy. If you need to save some variable to cache, you can do it by calling `Cache::save($name, $data) `, where `$name` is the name of the variable and `$data` is its value.

After you saved data, you can retrieve them by calling `Cache::get($name, $cacheTime)`, where `$name` is the name of the variable and `$cacheTime` is time in seconds. If the stored data in cache is older then this limit, it will return `false`. Second parameter is optional. If you do not pass any value as second parameter, Cache class will use  the default value (10 seconds).

`Cache::get()` will return the data or `false` if the variable is not stored in cache or it is too old.

```php
require 'vendor/autoload.php';

use AlexKratky\Cache;
use AlexKratky\Logger;

Cache::setDirectory(__DIR__ . '/cache/');
Logger::setDirectory(__DIR__ . '/cache/');

$c = Cache::get("user", 30);
if($c !== false) {
    var_dump($c);
}
$c_arr = array(
    "ID" => 1,
    "name" => "Alex",
    "email" => "example@example.com",
    "age" => 19,
    "admin" => true
);
Cache::save("user", $c_arr);
```



* `Cache::destroy(string $name): bool` - Deletes specified cache file.
* `Cache::clearUnused($dir = null, $time = 86400)` - Deletes unused cache files (Older then $time). The $dir parameter must be specified only from terminal (\__DIR__).
* `Cache::clearAll($dir = null)` - Deletes whole cache directory. The $dir parameter must be specified only from terminal (\__DIR__).