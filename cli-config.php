<?php

use asbamboo\database\Factory;
use asbamboo\database\Connection;

// replace with mechanism to retrieve EntityManager in your app
$DbFactory          = new Factory();
$sqpath             = __DIR__ . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'db.sqlite';
$sqmetadata         = __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database';
$sqmetadata_type    = Connection::MATADATA_YAML;
$sqdir              = dirname($sqpath);

if(!is_file($sqpath)){
    @mkdir($sqdir, 0700, true);
    @file_put_contents($sqpath, '');
}
$DbFactory->addConnection(Connection::create([
    'driver'    => 'pdo_sqlite',
    'path'      => $sqpath
], $sqmetadata, $sqmetadata_type));

$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
    'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($DbFactory->getManager()->getConnection()),
    'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($DbFactory->getManager())
));
return $helperSet;