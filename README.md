# php-process-watcher
Watches and manages processes in cli

# Installation

```
composer require rgen3/php-process-watcher
```

Usage example
---

### Create task object
```
use rgen3\watcher\commands\AbstractProcess;

class Sync extends AbstractProcess
{

    public static $descriptor;

    public function beforeStart()
    {
        echo "Sync is ready to start";
    }

    public function beforeStop()
    {
        // Some actions before process will be killed
        echo "Sync will be stopped";
    }

    // Commnad to be executed
    public static function getCmd(): string
    {
        return '/usr/bin/php -r "while (true) {echo 1; sleep(2);}"';
    }

    // Process descriptor settings
    public static function getDescriptor(): array
    {
        return [
            0 => array("pipe", "r"),
            1 => array("pipe", "w"),
            2 => array("pipe", "a")
        ];
    }

}
```

### How to run watcher

```
// Creates watcher instance
$watcher = new \rgen3\watcher\Watcher();

// Add the task to watcher
$watcher->pushProcess(new Sync());

//starts watcher
$watcher->run();

while (true) {
    //some app logic
    sleep(2);
    
    // Checks if processes is active
    // If down proccess will automatically be restarted
    $watcher->monitor();
}
```
