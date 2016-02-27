<?php
 
namespace St0iK\DbExport\Test;

use PHPUnit_Framework_TestCase;
use St0iK\DbExport\Databases\MySql;
use St0iK\DbExport\Exceptions\CannotStartDump;

class DbExportTest extends PHPUnit_Framework_TestCase {
 
  	/** @test */
  	    public function it_provides_a_factory_method()
  	    {
  	        $this->assertInstanceOf(MySql::class, MySql::create());
  	    }

  	    /** @test */
  	    public function it_will_throw_an_exception_when_no_credentials_are_set()
  	    {
  	        $this->setExpectedException(CannotStartDump::class);

  	        MySql::create()->dump('test.sql');
  	    }

  	    /** @test */
  	    public function it_can_generate_a_dump_command()
  	    {
  	        $dumpCommand = MySql::create()
  	            ->setDbName('dbname')
  	            ->setUserName('username')
  	            ->setPassword('password')
  	            ->generateMysqlDumpCommand('dump.sql', 'credentials.txt');

  	        $this->assertSame('mysqldump --defaults-extra-file=credentials.txt --skip-comments --extended-insert dbname > dump.sql', $dumpCommand);
  	    }

  	   	/** @test */
  	   	public function it_can_generate_a_dump_command_with_one_table()
  	   	{
  	   	    $dumpCommand = MySql::create()
  	   	        ->setDbName('dbname')
  	   	        ->setUserName('username')
  	   	        ->setPassword('password')
  	   	        ->setTables('tbl')
  	   	        ->generateMysqlDumpCommand('dump.sql', 'credentials.txt');

  	   	    $this->assertSame('mysqldump --defaults-extra-file=credentials.txt --skip-comments --extended-insert dbname tbl > dump.sql', $dumpCommand);
  	   	}

  	   	/** @test */
  	   	public function it_can_generate_a_dump_command_with_multiple_tables()
  	   	{
  	   	    $dumpCommand = MySql::create()
  	   	        ->setDbName('dbname')
  	   	        ->setUserName('username')
  	   	        ->setPassword('password')
  	   	        ->setTables(['tbl1','tbl2','tbl3','tbl4','tbl5'])
  	   	        ->generateMysqlDumpCommand('dump.sql', 'credentials.txt');

  	   	    $this->assertSame('mysqldump --defaults-extra-file=credentials.txt --skip-comments --extended-insert dbname tbl1 tbl2 tbl3 tbl4 tbl5 > dump.sql', $dumpCommand);
  	   	}

  	    /** @test */
  	    public function it_can_generate_a_dump_command_without_using_extended_insterts()
  	    {
  	        $dumpCommand = MySql::create()
  	            ->setDbName('dbname')
  	            ->setUserName('username')
  	            ->setPassword('password')
  	            ->dontUseExtendedInserts()
  	            ->generateMysqlDumpCommand('dump.sql', 'credentials.txt');

  	        $this->assertSame('mysqldump --defaults-extra-file=credentials.txt --skip-comments --skip-extended-insert dbname > dump.sql', $dumpCommand);
  	    }

  	    /** @test */
  	    public function it_can_generate_a_dump_with_gzip()
  	    {
  	        $dumpCommand = MySql::create()
  	            ->setDbName('dbname')
  	            ->setUserName('username')
  	            ->setPassword('password')
  	            ->dontUseExtendedInserts()
  	            ->useGzip()
  	            ->generateMysqlDumpCommand('dump.sql', 'credentials.txt');

  	        $this->assertSame('mysqldump --defaults-extra-file=credentials.txt --skip-comments --skip-extended-insert dbname | gzip > dump.sql', $dumpCommand);
  	    }
  	    /** @test */
  	    public function it_can_generate_a_dump_command_with_custom_binary_path()
  	    {
  	        $dumpCommand = MySql::create()
  	            ->setDbName('dbname')
  	            ->setUserName('username')
  	            ->setPassword('password')
  	            ->setDumpBinaryPath('/custom/directory')
  	            ->generateMysqlDumpCommand('dump.sql', 'credentials.txt');

  	        $this->assertSame('/custom/directory/mysqldump --defaults-extra-file=credentials.txt --skip-comments --extended-insert dbname > dump.sql', $dumpCommand);
  	    }

  	    /** @test */
  	    public function it_can_generate_a_dump_command_without_using_extending_inserts()
  	    {
  	        $dumpCommand = MySql::create()
  	            ->setDbName('dbname')
  	            ->setUserName('username')
  	            ->setPassword('password')
  	            ->dontUseExtendedInserts()
  	            ->generateMysqlDumpCommand('dump.sql', 'credentials.txt');

  	        $this->assertSame('mysqldump --defaults-extra-file=credentials.txt --skip-comments --skip-extended-insert dbname > dump.sql', $dumpCommand);
  	    }

  	    /** @test */
  	    public function it_can_generate_a_dump_command_with_a_custom_socket()
  	    {
  	        $dumpCommand = MySql::create()
  	            ->setDbName('dbname')
  	            ->setUserName('username')
  	            ->setPassword('password')
  	            ->setSocket(1234)
  	            ->generateMysqlDumpCommand('dump.sql', 'credentials.txt');

  	        $this->assertSame('mysqldump --defaults-extra-file=credentials.txt --skip-comments --extended-insert --socket=1234 dbname > dump.sql', $dumpCommand);
  	    }

  	    /** @test */
  	    public function it_can_generate_the_contents_of_a_credentials_file()
  	    {
  	        $credentialsFileContent = MySql::create()
  	            ->setDbName('dbname')
  	            ->setUserName('username')
  	            ->setPassword('password')
  	            ->setHost('hostname')
  	            ->setSocket(1234)
  	            ->prepareCredentialsForFile();

  	        $this->assertSame(
  	            '[client]'.PHP_EOL."user = 'username'".PHP_EOL."password = 'password'".PHP_EOL."host = 'hostname'".PHP_EOL."port = '3306'",
  	            $credentialsFileContent);
  	    }

  	    /** @test */
  	    public function it_can_get_the_name_of_the_db()
  	    {
  	        $dbName = 'testName';

  	        $dbDumper = MySql::create()->setDbName($dbName);

  	        $this->assertEquals($dbName, $dbDumper->getDbName());
  	    }



}