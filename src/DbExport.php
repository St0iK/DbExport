<?php

namespace St0iK\DbExport;

use Symfony\Component\Process\Process;
use St0iK\DbExport\Exceptions\DumpFailed;

abstract class DbExport
{

	public static function create()
	{
		return new static();
	}

	/**
	 * @param \Symfony\Component\Process\Process $process
	 * @param string                             $outputFile
	 *
	 * @return bool
	 *
	 * @throws \Spatie\DbDumper\Exceptions\DumpFailed
	 */
	
	protected function checkIfDumpWasSuccessFul(Process $process, $outputFile)
	{
	    if (!$process->isSuccessful()) {
	        throw DumpFailed::processDidNotEndSuccessfully($process);
	    }

	    if (!file_exists($outputFile)) {
	        throw DumpFailed::dumpfileWasNotCreated();
	    }

	    if (filesize($outputFile) === 0) {
	        throw DumpFailed::dumpfileWasEmpty();
	    }

	    return true;
	}
}
