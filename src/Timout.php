<?php

declare(strict_types=1);

namespace Timer;

use Exception;

/**
 * Executes a callback after a number of seconds
 */
final class Timout
{
    /**
     * @var bool
     */
    private $asyncHandlingPreviouslySet = true;
    
    /**
     * @param callable    $callback       to be called after $timeout seconds are passed
     * @param int         $timeout        in seconds
     *
     * @throws Exception                  when the pcntl extension is not loaded or the SIGALRM handler
     *                                    has been already set
     */
    public function __construct(callable $callback, int $timeout)
    {
        if (!function_exists('pcntl_alarm')) {
            throw new Exception('pcntl extension is not loaded');
        }
    
        if (is_callable(pcntl_signal_get_handler(SIGALRM))) {
            throw new Exception('SIGARLM handler has been already set');
        }
    
        if (!pcntl_async_signals()) {
            $this->asyncHandlingPreviouslySet = false;
            pcntl_async_signals(true);
        }
    
        pcntl_signal(SIGALRM, function () use ($callback): void {
            $this->clearHandler();
            $callback();
            $this->fulfilled = true;
        });
        pcntl_alarm($timeout);
    }
    
    public function __destruct()
    {
        $this->clearTimeout();
    }
    
    /**
     * Stops the timer and prevents the callback from further calling
     *
     * @return void
     */
    public function clearTimeout(): void
    {
        pcntl_alarm(0);
        $this->clearHandler();
    }
    
    private function clearHandler(): void
    {
        pcntl_signal(SIGALRM, SIG_DFL);
        if (!$this->asyncHandlingPreviouslySet) {
            pcntl_async_signals(false);
        }
    }
}
