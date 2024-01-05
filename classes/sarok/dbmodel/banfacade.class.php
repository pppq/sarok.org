<?php

class banfacade 
{
    private const BANNED_IPS_FILE = '../cache/bannedip.txt';
    private const SEPARATOR = ';';

	private log $log;
    private mysql $db;
	private $bannedIPs = array();

	public function __construct() 
    {
		$this->log = singletonloader::getInstance('log');
		$this->loadBannedIPs();
		$this->log->info('banfacade initialized');
    }

	private function loadBannedIPs() : void
	{
		$this->log->info('Loading banned IPs');

		// format is "IP;reason", one per line
		$bannedLines = file(self::BANNED_IPS_FILE);
        if ($bannedLines === false) {
            $this->log->warning('Failed to load banned IPs file');
            return;
        }

		foreach ($bannedLines as $line) {
			$line = trim($line);
			$row = explode(self::SEPARATOR, $line, 2);
			$this->bannedIPs[$row[0]] = $row[1];
		}

        $numBanned = count($this->bannedIPs);
		$this->log->debug("Loaded {$numBanned} banned IP(s)");
	}
	
	private function saveBannedIPs() : void
	{
		$this->log->info('Saving banned IPs');

        $contents = '';
		foreach ($this->bannedIPs as $ip => $reason) {
			$contents .= "{$ip};{$reason}\n";
		}
		
        $result = file_put_contents(self::BANNED_IPS_FILE, $contents);
        if ($result === false) {
            $this->log->warning('Failed to save banned IPs file');
        }
	}
	
	public function getBanReason(string $ip) : string
	{
		$this->log->info("Checking IP {$ip} for being banned");
		
		if (array_key_exists($ip, $this->bannedIPs)) {
			return $this->bannedIPs[$ip];
		} else {
            return '';
        }
	}
	
    private function getDb() : mysql
    {
        if (!isset($this->db)) {
            $this->db = singletonloader::getInstance("mysql");
        }

        return $this->db;
    }

	public function banIP(string $ip, string $reason, bool $store = true) : void
	{
        $existingReason = $this->getBanReason($ip);
		$reason = strtr($reason, "\n\r\t;", '   ');
        $currentDate = date('Y-m-d G:i:s');
		
        $this->log->info("Banning {$ip}. Reason: {$reason}");
		$this->bannedIPs[$ip] = "{$existingReason} {$reason}@{$currentDate}";
		
		$q = "DELETE FROM `sessions` where `IP` = '{$ip}'";
		$this->getDb()->mquery($q);

		if ($store) {
			$this->saveBannedIPs();
        }
	}
}
