<?php
namespace HelloThrift\php;

error_reporting(E_ALL);

require_once __DIR__.'/lib/Thrift/ClassLoader/ThriftClassLoader.php';
use Thrift\ClassLoader\ThriftClassLoader;

$GEN_DIR = realpath(dirname(__FILE__)).'/../gen-php';
$loader = new ThriftClassLoader();
$loader->registerNamespace('Thrift',__DIR__.'/lib');
$loader->registerDefinition('HelloThrift',$GEN_DIR);
$loader->register();

if (php_sapi_name() == 'cli') {
    ini_set('display_errors',"stderr");
}

use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TPhpStream;
use Thrift\Transport\TBufferedTransport;

class HelloHandler implements \HelloThrift\HelloServiceIf {

    public function sayHello($username)
    {
        return "Hello ".$username;
    }
}

header('Content-Type','application/x-thrift');
if (php_sapi_name() == 'cli') {
    echo PHP_EOL;
}

$handler = new HelloHandler();
$processor = new \HelloThrift\HelloServiceProcessor($handler);

$transport = new TBufferedTransport(new TPhpStream(TPhpStream::MODE_R | TPhpStream::MODE_W));
$protocol = new TBinaryProtocol($transport,true,true);

$transport->open();
$processor->process($protocol,$protocol);
$transport->close();
