<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\Utils;
use Psr\Http\Message\ResponseInterface;

require_once 'vendor/autoload.php';

$client = new Client();

function execureSync(Client $client)
{
    echo "Executando sincrono" . PHP_EOL;
    $startTime = microtime(as_float: true);

    $response1 = $client->request('GET', 'http://localhost:8000');
    $response2 = $client->request('GET', 'http://localhost:8001');

    echo $response1->getBody()->getContents() . PHP_EOL;
    echo $response2->getBody()->getContents() . PHP_EOL;

    $endTime = microtime(as_float: true);
    echo "Tempo total: " . ($endTime - $startTime) . PHP_EOL;
}
function execureASync(Client $client)
{
    echo "Executando assincrono" . PHP_EOL;
    $startTime = microtime(as_float: true);

    $responses = Utils::settle([
        $client->requestAsync('GET', 'http://localhost:8000'),
        $client->requestAsync('GET', 'http://localhost:8001'),
    ])->wait();

    echo $responses[0]['value']->getBody()->getContents() . PHP_EOL;
    echo $responses[1]['value']->getBody()->getContents() . PHP_EOL;

    $endTime = microtime(as_float: true);
    echo "Tempo total: " . ($endTime - $startTime) . PHP_EOL;
}

execureSync($client);

echo PHP_EOL;

execureASync($client);