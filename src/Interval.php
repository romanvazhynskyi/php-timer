<?php

declare(strict_types=1);

namespace Timer;

use Exception;

/**
 * Executes a callback at specified interval in seconds
 */
final class Interval
{
    /**
     * @var bool
     */
    private $asyncHandlingPreviouslySet = true;
    /**
     * @var int
     */
    private $ticks = 0;
    
    /**
     * @param callable     $callback       to be called every $idleTimeout seconds
     * @param int          $idleTimeout    in seconds
     *
     * @throws Exception                   when the pcntl extension is not loaded or the SIGALRM handler
     *                                     has been previously set
     */
    public function __construct(callable $callback, int $idleTimeout)
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
        
        pcntl_signal(SIGALRM, function () use ($idleTimeout, $callback): void {
            $this->ticks++;
            pcntl_alarm($idleTimeout);
            $callback($this);
        });
        pcntl_alarm($idleTimeout);
    }
    
    public function __destruct()
    {
        $this->clearInterval();
    }
    
    /**
     * Prevents the callback from further callings
     */
    public function clearInterval(): void
    {
        pcntl_alarm(0);
        pcntl_signal(SIGALRM, SIG_DFL);
        if (!$this->asyncHandlingPreviouslySet) {
            pcntl_async_signals(false);
        }
    }
    
    /**
     * Returns the number of callback calls
     *
     * @return int the number of callback calls
     */
    public function getTicks(): int
    {
        return $this->ticks;
    }
}
