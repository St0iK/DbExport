<?php

namespace St0iK\DbExport\Databases;

use St0iK\DbExport\DbExport;
use Symfony\Component\Process\Process;
use St0iK\DbExport\Exceptions\CannotStartDump;


class MySql extends DbExport
{
	protected $dbName;
	protected $host = 'localhost';
	protected $userName;
	protected $password;
	protected $port = 3306;
	protected $socket;
	protected $dumpBinaryPath = '';
	protected $useExtendedInserts = true;
	protected $tables;
	protected $gzip = false;

	/**
	 * @return string database name
	 */
	public function getDbName()
	{
		return $this->dbName;
	}

	/**
	 * @param string $dbName
	 * 
	 * @return \St0iK\DbExport\Databases\Mysq;
	 */
	public function setDbName($dbName)
	{
		$this->dbName = $dbName;
		return $this;
	}

	/**
	 * @param string $host
	 * 
	 * @return \St0iK\DbExport\Databases\Mysq;
	 */
	public function setHost($host)
	{
		$this->host = $host;
		return $this;
	}

	/**
	 * @param string $userName
	 * 
	 * @return \St0iK\DbExport\Databases\Mysq;
	 */
	public function setUserName($userName)
	{
		$this->userName = $userName;
		return $this;
	}

	/**
	 * @param string $password
	 * 
	 * @return \St0iK\DbExport\Databases\Mysq;
	 */
	public function setPassword($password)
	{
		$this->password = $password;
		return $this;
	}

	/**
	 * @param string $port
	 * 
	 * @return \St0iK\DbExport\Databases\Mysq;
	 */
	public function setPort($port)
	{
		$this->port = $port;
		return $this;
	}

	/**
	 * @param string $socket
	 * 
	 * @return \St0iK\DbExport\Databases\Mysq;
	 */
	public function setSocket($socket)
	{
		$this->socket = $socket;
		return $this;
	}

	/**
	 * @param string $socket
	 * 
	 * @return \St0iK\DbExport\Databases\Mysq;
	 */
	public function setTables($tables)
	{
		$this->tables = $tables;
		return $this;
	}

	/**
	 * @return \St0iK\DbExport\Databases\MySql
	 */
	public function useGzip()
	{
		$this->gzip = true;
		return $this;
	}

	/**
	 * @return \St0iK\DbExport\Databases\MySql
	 */
	public function useExtendedInserts()
	{
	    $this->useExtendedInserts = true;

	    return $this;
	}

	/**
	 * @return \St0iK\DbExport\Databases\MySql
	 */
	public function dontUseExtendedInserts()
	{
	    $this->useExtendedInserts = false;

	    return $this;
	}

	/**
	 * Dump the contents of the database to the given file.
	 *
	 * @param string $filename
	 *
	 * @throws \St0iK\DbExport\Exceptions\CannotStartDump
	 * @throws \St0iK\DbExport\Exceptions\DumpFailed
	 */
	public function dump($filename)
	{
		$this->checkCredentials();
		
		$temporaryFileHandle = tmpfile();
		fwrite($temporaryFileHandle, $this->prepareCredentialsForFile());
		$temporaryCredentialsFile = stream_get_meta_data($temporaryFileHandle)['uri'];
		$command = $this->generateMysqlDumpCommand($filename, $temporaryCredentialsFile);
		
		$process = new Process($command);
		$process->run();

		$this->checkIfDumpWasSuccessFul($process, $filename);
	}

	/**
	 * Checks the minimum credentials are set
	 * @throws \St0iK\DbExport\Exceptions\CannotStartDump
	 */
	protected function checkCredentials()
	{
		foreach (['userName', 'dbName', 'host'] as $requiredProperty) {
		    if (strlen($this->$requiredProperty) === 0) {
		        throw CannotStartDump::emptyParameter($requiredProperty);
		    }
		}
		
	}

	/**
	 * @return string
	 */
	public function prepareCredentialsForFile()
	{
	    $contents = [
	        '[client]',
	        "user = '{$this->userName}'",
	        "password = '{$this->password}'",
	        "host = '{$this->host}'",
	        "port = '{$this->port}'",
	    ];

	    return implode(PHP_EOL, $contents);
	}

	/**
	 * @param string $dumpBinaryPath
	 *
	 * @return \St0iK\DbExport\Databases\MySql
	 */
	public function setDumpBinaryPath($dumpBinaryPath)
	{
	    if ($dumpBinaryPath !== '' && substr($dumpBinaryPath, -1) !== '/') {
	        $dumpBinaryPath .= '/';
	    }

	    $this->dumpBinaryPath = $dumpBinaryPath;

	    return $this;
	}

	public function getTables()
	{	
		if(is_array($this->tables)){
			return implode(' ', $this->tables);
		}
		return $this->tables;
	}

	/**
	 * Get the command that should be performed to dump the database.
	 *
	 * @param string $filename
	 * @param string $temporaryCredentialsFile
	 *
	 * @return string
	 */
	public function generateMysqlDumpCommand($filename, $temporaryCredentialsFile)
	{
		 $command = [
            "{$this->dumpBinaryPath}mysqldump",
            "--defaults-extra-file={$temporaryCredentialsFile}",
            '--skip-comments',
            $this->useExtendedInserts ? '--extended-insert' : '--skip-extended-insert',
        ];

        if ($this->socket != '') {
            $command[] = "--socket={$this->socket}";
        }

        $tables = $this->getTables();
        $command[] = "{$this->dbName}";
        
        // Specific tables
        if($tables)
        {
        	$command[] = "{$tables}";
        }
        
        // Gzip compression
        if($this->gzip)
        {
        	$command[] = "| gzip";
        }

        $command[] = "> {$filename}";

        return implode(' ', $command);
	}

}