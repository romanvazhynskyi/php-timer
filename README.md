<p align="center">
    <h1 align="center">PHP timers</h1>
</p>

The package provides a PHP implementation of timers, which execute a function
once after a number of seconds or repeatedly at a specified interval in seconds.

## Requirements
- PHP 7.1 or higher
- pcntl extension

## Installation
```shell
composer require romanvazhynskyi/php-timer
```

## General usage

### Executing a callback once
```php
use Timer\Timer;

// Sets an asynchronous callback that will print 'Hello from the timeout' after 1 second.
Timer::setTimeout(
    function () {
        echo 'Hello from timeout';
    },
    1
);

// Please note that if the script exits before the timeout, the callback will not be called.
// We use an infinite loop to mimic a time-consuming task.
while (true) {
    // Used to prevent CPU overload, it does not affect the timer.
    sleep(5);
}
```

### Executing a callback repeatedly
```php
use Timer\Timer;
use Timer\Interval;

// Sets an asynchronous callback that will be printing 'Hello from the interval' 5 times at 1 second interval
Timer::setInterval(
    function (Interval $interval) {
        if ($interval->getTicks() === 5) {
            $interval->clearInterval();
        }
        echo 'Hello from interval' . PHP_EOL;
    },
    1
);

while (true) {
    sleep(5);
}
```

Please note that attempting to set more than one interval or timeout (or both) in the same process
will lead to an error as, under the hood, they use a SIGALRM signal handler that should be set only once.

Due to the same reason, please do not set any SIGALRM signal handlers after setting timeout or interval.

The code above will throw an exception:
```php
use Timer\Timer;

Timer::setTimeout(
    function () {
        echo 'Hello from timeout';
    },
    1
);

Timer::setTimeout(
    function () {
        echo 'Hello from timeout';
    },
    1
);

```
This code will also throw the exception:
```php
use Timer\Timer;

Timer::setInterval(
    function () {
        echo 'Hello from the interval';
    },
    1
);

Timer::setTimeout(
    function () {
        echo 'Hello from the timeout';
    },
    1
);
```

## License
This library is released under the [MIT license](LICENSE).
