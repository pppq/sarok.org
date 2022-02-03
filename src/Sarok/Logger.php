<?php namespace Sarok;

use Sarok\Exceptions\LogException;
use InvalidArgumentException;

class Logger {

    private string $logPath;
    private int $logLevel;

    private int $counter;
    private float $startTime;
    private $logFileOpened;
    private $logFile;

    public function __construct(string $logPath, int $logLevel) {
        if (! isset($logPath) || $logPath === '') {
            throw new InvalidArgumentException("Log path may not be null or empty.");
        }

        if (! isset($logLevel) || $logLevel < 1 || $logLevel > 5) {
            throw new InvalidArgumentException("Log level must be an integer between 1 and 5.");
        }

        $this->logPath = $logPath;
        $this->logLevel = $logLevel;

        $this->counter = 0;
        $this->startTime = microtime(true);
        $this->logFileOpened = false;

        $this->debug("Logger initialized");
    }

    private function ensureLogOpen() {
        if (!$this->logFileOpened) {
            // We will try opening the file at most once.
            $this->logFileOpened = true;

            $this->logFile = fopen($this->logPath, "a");
            if ($this->logFile === false) {
                throw new LogException("Couldn't open log file '" . $this->logPath . "' for writing.");
            }

            $this->info("");
            $this->info("---------------------------------------------");
            $this->info("Opened " . $this->logPath . " for writing");
        }
    }

    private function write(int $messageLevel, string $message) {
        if ($this->logLevel > $messageLevel) {
            // Not interested in this message, skip
            return;
        }

        $this->ensureLogOpen();
        
        $currentTime = date("Y-m-d G:i:s");
        $elapsedTime = (microtime(true) - $this->startTime) * 1000.0;
        $entry = sprintf("%s; %s; %6dms; %3d; %s\n", $messageLevel, $currentTime, $elapsedTime, $this->counter++, $message);
        
        if (fwrite($this->logFile, $entry) === false) {
            throw new LogException("Couldn't write log entry to file '" . $this->logPath . "'.");
        }
    }

	public function debug($message) {
		$this->write(1, $message);
	}

	public function info($message) {
		$this->write(2, $message);
	}

	public function warning($message) {
		$this->write(3, $message);
	}

	public function error($message) {
		$this->write(4, $message);
	}
	
	public function critical($message) {
	    $this->write(5, $message);
	}
	
// 	public function mail($message="")
// 	{
// 		global $system_email;
// 		$this->write(7, $message);
// 		$traceList=debug_backtrace();	
// 		$str.="\n\n";
// 		foreach($_COOKIE as $k=>$v)
// 			{
// 				$str.="$k: $v\n";
// 			}
			
// 		mail($system_email,"sarok.org system message: $category",$str);
// 	}


// 	public function security($message) {
// 		global $system_email;
// 		$this->write(7, $message);
// 		$traceList=debug_backtrace();	
// 		$str.="\n\n";
// 			for($i=sizeof($traceList)-1;$i>=0;$i--)
// 		{
// 			$trace=$traceList[$i];
// 			$file=explode("\\",$trace["file"]);
// 			$filename=$file[sizeof($file)-1];
// 			$filename=str_replace(".php","",$filename);
// 			$filename=str_replace(".class","",$filename);
// 			$str.=$filename.":".$trace["function"].":".$trace["line"]." -->\n";
// 		}
			
// 			foreach($_SERVER as $k=>$v)
// 			{
// 				$str.="$k: $v\n";
// 			}
	
// 			foreach($_POST as $k=>$v)
// 			{
// 				$str.="$k: $v\n";
// 			}
// 			foreach($_GET as $k=>$v)
// 			{
// 				$str.="$k: $v\n";
// 			}
// 			foreach($_COOKIE as $k=>$v)
// 			{
// 				$str.="$k: $v\n";
// 			}
// 		mail($system_email,"Security alert from sarok.org",$str);
// 	}

	public function __destruct() {
	    if ($this->logFileOpened && $this->logFile !== false) {
	        $this->debug("Closing log file");
	        fclose($this->logFile);
	    }
	}
}
