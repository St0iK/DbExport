# All Credits on idea and implementation go to Freek Van der Herten
Re-created for training purposes and added some extra functionality (exporting gzipped dumps and specific tables)
- [DbDumper](https://github.com/spatie/db-dumper)
- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

# Dump the contents of a database

This repo contains an easy to use class to dump a database using PHP. Currently only MySQL is supported. Behind
the scences `mysqldump` is used.

Here a simple example of how to create a dump.

```php
St0iK\DbExport\Databases\MySql::create()
    ->setDbName($databaseName)
    ->setUserName($userName)
    ->setPassword($password)
    ->dump('dump.sql');
```
## Requirements
`mysqldump` should be installed.

## Usage

This is the simplest way to create dump of the db:

```php
St0iK\DbExport\Databases\MySql::create()
    ->setDbName($databaseName)
    ->setUserName($userName)
    ->setPassword($password)
    ->setPassword($password)
    ->dump('dump.sql');
```
Export specific tables only:

```php
St0iK\DbExport\Databases\MySql::create()
    ->setDbName($databaseName)
    ->setUserName($userName)
    ->setPassword($password)
    ->setPassword($password)
    ->setTables(['tbl1','tbl2','tbl3'])
    ->dump('dump.sql');
```

Create gzipped dump:

```php
St0iK\DbExport\Databases\MySql::create()
    ->setDbName($databaseName)
    ->setUserName($userName)
    ->setPassword($password)
    ->setPassword($password)
    ->useGzip()
    ->dump('dump.sql');
```

## Testing

``` bash
$ composer test
```

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
