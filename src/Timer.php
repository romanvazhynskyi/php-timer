<?php

declare(strict_types=1);

namespace Timer;

use Exception;

/**
 * Acts as a static factory for timers and intervals
 */
final class Timer
{
    /**
     * Creates a new Timeout instance
     *
     * @param callable    $callback       to be called after $timeout seconds
     * @param int         $timeout        in seconds
     *
     * @return Timout
     *
     * @throws Exception                  when the pcntl extension is not loaded or the SIGALRM handler
     *                                    has been already set
     */
    public static function setTimeout(callable $callback, int $timeout): Timout
    {
        return new Timout($callback, $timeout);
    }
    
    /**
     * Creates a new Interval instance
     *
     * @param callable    $callback       to be called every $idleTimeout seconds
     * @param int         $idleTimeout    in seconds
     *
     * @return Interval
     *
     * @throws Exception                  when the pcntl extension is not loaded or the SIGALRM handler
     *                                    has been already set
     */
    public static function setInterval(callable $callback, int $idleTimeout): Interval
    {
        return new Interval($callback, $idleTimeout);
    }
}
