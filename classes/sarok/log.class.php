<?php


//require_once()
class log {
	private $logfile;
	private $general_logfile;
	private $counter;
	private $debuglevel;
	public $microtime,$ms;
	private $is_open;
	private $file;
	private $file2;

	public function __construct($level = false) {
		global $logfile, $general_logfile,$log_level;
		if($level==false) $level=$log_level;
		$this->logfile = $logfile;
		$this->general_logfile = $general_logfile;
		$this->is_open = false;
		$this->counter = 0;
		$this->debuglevel = $level;
		$this->microtime = $this->getmicrotime();
		//echo $this->microtime;
		$this->debug2("START");
	}

	public function log_open() {
		if ($this->is_open == false) {
			$this->file2 = fopen($this->general_logfile, "a");
			if ($this->file2 === FALSE) {
				echo "error: could not open {$this->general_logfile}";
				return;
			}
			$this->is_open = true;
			$this->info("");
			$this->info("START---------------------------------------------");
			$this->info("opened ".$this->logfile." for log");
			$this->info("opened ".$this->general_logfile." for secondary log");
		}
	}

	public function getmicrotime() {
		list ($usec, $sec) = explode(" ", microtime());
		//print_r(list)
		//echo "$usec, $sec\n";
		return (float) ((float)$usec + (float)$sec);
		//return (float) (10000 * ((float)$usec + (float)$sec));
		//return (int) (10000 * $usec);// + (float) $sec));
		//return (int) (1000 * ((float) $usec));// + (float) $sec));
	}

	public function __destruct() {
		$this->debug2("Closing log. \n");
		if ($this->is_open == true) {
			fclose($this->file2);
		}
	}

	public function getlogpattern($level) {
		$out = "";
		for ($i = 0; $i < $level; $i ++)
			$out .= "X";
		while ($i ++ < 6)
			$out .= "_";
		return ($out);
	}

	private function write($level, $str, $category = "") {
			//	 global $user;
	if ($this->debuglevel <= $level) {
			$this->log_open();
			$this->ms=(int)(($this->getmicrotime() - $this->microtime)*1000);
			$ms = sprintf("%6d", $this->ms);

			//$ms=sprintf ("%6.3fs", $this->getmicrotime() - $this->microtime );   ---this one!
			//$ms=sprintf ("%6.4fs %f %f ", (($this->getmicrotime() - $this->microtime)),$this->getmicrotime(), $this->microtime );
			//$ms=($this->getmicrotime() - $this->microtime);
			//$str = $this->getlogpattern($level)."; ".date("Y-m-d G:i:s:").sprintf("%6d", $ms)."ms; ".sprintf("%3d", $this->counter)."; ".$str."\n\r";
			//$str = sprintf("%s; %s; %6dms; %s; %3d; %s\n", $level, date("Y-m-d G:i:s"), $ms,memory_get_usage(), $this->counter, $str);
			$str = sprintf("%s; %s; %sms; %3d; %s\n", $level, date("Y-m-d G:i:s"), $ms, $this->counter, $str);
			fwrite($this->file2, $str);
			$this->counter++;
		}
	}

	public function debug($str, $category = "") {
		$this->write(1, $str, $category);
	}

	public function debug2($str, $category = "") {
		$this->write(2, $str, $category);
	}

	public function info($str, $category = "") {
		$this->write(3, $str, $category);
	}

	public function warning($str, $category = "") {
		$this->write(4, $str, $category);
	}

	public function error($str, $category = "") {
		$this->write(5, $str, $category);
	}
	
	public function mail($str, $category="")
	{
		global $system_email;
		$this->write(7, $str, $category);
		$traceList=debug_backtrace();	
		$str.="\n\n";
		foreach($_COOKIE as $k=>$v)
			{
				$str.="$k: $v\n";
			}
			
		mail($system_email,"sarok.org system message: $category",$str);
	}


	public function security($str, $category = "") {
		global $system_email;
		$this->write(7, $str, $category);
		$traceList=debug_backtrace();	
		$str.="\n\n";
			for($i=sizeof($traceList)-1;$i>=0;$i--)
		{
			$trace=$traceList[$i];
			$file=explode("\\",$trace["file"]);
			$filename=$file[sizeof($file)-1];
			$filename=str_replace(".php","",$filename);
			$filename=str_replace(".class","",$filename);
			$str.=$filename.":".$trace["function"].":".$trace["line"]." -->\n";
		}
			
			foreach($_SERVER as $k=>$v)
			{
				$str.="$k: $v\n";
			}
	
			foreach($_POST as $k=>$v)
			{
				$str.="$k: $v\n";
			}
			foreach($_GET as $k=>$v)
			{
				$str.="$k: $v\n";
			}
			foreach($_COOKIE as $k=>$v)
			{
				$str.="$k: $v\n";
			}
		mail($system_email,"Security alert from sarok.org",$str);
	}

	public function halt($str, $category = "") {
		$this->write(7, $str, $category);
	}

}
?>