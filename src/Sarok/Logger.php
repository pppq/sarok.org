<?php declare(strict_types=1);

namespace Sarok;

use Sarok\Exceptions\LogException;
use InvalidArgumentException;

final class Logger 
{
    private string $logPath;
    private int $logLevel;

    private int $counter;
    private float $startTime;
    private bool $logFileOpened;

    /** @var resource|false */
    private $logFile;

    public function __construct(string $logPath, int $logLevel) 
    {
        if ($logPath === '') {
            throw new InvalidArgumentException('Log path may not be empty.');
        }

        if ($logLevel < 1 || $logLevel > 5) {
            throw new InvalidArgumentException('Log level must be an integer between 1 and 5.');
        }

        $this->logPath = $logPath;
        $this->logLevel = $logLevel;

        $this->counter = 0;
        $this->startTime = microtime(true);
        $this->logFileOpened = false;

        $this->debug("Logger initialized");
    }

    private function ensureLogOpen() : void
    {
        if ($this->logFileOpened === false) {
            /* 
             * We will try opening the file at most once. This is stored in a flag independent 
             * of the contents of $this->logFile (uninitialized / resource / false).
             */
            $this->logFileOpened = true;
            
            $logPath = $this->logPath;
            $this->logFile = fopen($logPath, 'a');
            if ($this->logFile === false) {
                throw new LogException("Couldn't open log file '${logPath}' for writing.");
            }

            $this->info("");
            $this->info("---------------------------------------------");
            $this->info("Opened log file '${logPath}' for writing");
        }
    }

    private function write(int $messageLevel, string $message) : void
    {
        if ($this->logLevel > $messageLevel) {
            // Not interested in this message, skip
            return;
        }

        $this->ensureLogOpen();
        if ($this->logFile === false) {
            // The log is already closed and some writes are arriving late, skip
            return;
        }
        
        $currentTime = date("Y-m-d G:i:s");
        $elapsedTime = (microtime(true) - $this->startTime) * 1000.0;
        $entry = sprintf("%s; %s; %6dms; %3d; %s\n", $messageLevel, $currentTime, $elapsedTime, $this->counter++, $message);
        
        if (fwrite($this->logFile, $entry) === false) {
            $logPath = $this->logPath;
            throw new LogException("Couldn't write log entry to file '${logPath}'.");
        }
    }

	public function debug(string $message) : void
    {
		$this->write(1, $message);
	}

	public function info(string $message) : void
    {
		$this->write(2, $message);
	}

	public function warning(string $message) : void
    {
		$this->write(3, $message);
	}

	public function error(string $message) : void
    {
		$this->write(4, $message);
	}
	
	public function critical(string $message) : void
    {
	    $this->write(5, $message);
	}

	public function __destruct() {
	    if ($this->logFileOpened === true && is_resource($this->logFile)) {
            $logPath = $this->logPath;
	        $this->debug("Closing log file '${logPath}'");
	        fclose($this->logFile);
            $this->logFile = false;
	    }
	}
}
