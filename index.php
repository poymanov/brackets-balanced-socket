<?php

require  'vendor/autoload.php';

use React\Socket\ConnectionInterface;
use BracketsBalanced\BracketsBalanced;

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server('127.0.0.1:8080', $loop);

$socket->on('connection', function(ConnectionInterface $connection){
    $connection->write('Hello. Please type string with brackets' . PHP_EOL);
    $connection->on('data', function($data) use ($connection) {
        $data = trim($data);
        if ($data == 'close') {
            $connection->close();
        } else {
            $balanced = new BracketsBalanced($data);
            $checkResult = $balanced->isBalanced();

            if ($checkResult) {
                $message = 'Balanced';
            } else {
                $message = formatErrors($balanced->errors);
            }

            $connection->write($message . PHP_EOL);
        }
    });
});

echo "Listening on {$socket->getAddress()}\n";

$loop->run();

function formatErrors($errors)
{
    $text = '';
    if ($errors) {
        foreach ($errors as $error) {
            $text .= $error;
        }
    } else {
        $text = 'Not balanced';
    }
    return $text;
}