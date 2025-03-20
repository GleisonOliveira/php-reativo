<?php

$assync = false;
$streams = [];

function openStreams(array &$streams)
{
    // open the streams used
    $streams = [
        stream_socket_client('tcp://localhost:8000/'),
        stream_socket_client('tcp://localhost:8001/'),
        fopen('file1.txt', 'r'),
        fopen('file2.txt', 'r'),
    ];
}

// create the manual http stream for socket
function createHttpStream(array &$streams)
{
    fwrite($streams[0], 'GET /http-server.php HTTP/1.1' . PHP_EOL . PHP_EOL);
}

function printContent($stream)
{
    // obtem o conteudo do stream
    $content = stream_get_contents($stream);

    // verifica se existe dois enters consecultivos indicando uma requisicao do protocolo http
    $end = strpos($content, PHP_EOL . PHP_EOL);
    $data = $content;

    //se ele encontrar a request http
    if ($end !== false) {
        //remove o cabecalho http e pega apenas o corpo
        $data = substr($content, $end + 4);

        echo $data . PHP_EOL;

        return;
    }

    echo $data . PHP_EOL;
}

function executeSync(array $streams)
{
    echo "Executando sincrono" . PHP_EOL;
    $startTime = microtime(true);

    // para cada arquivo, leia o conteudo
    foreach ($streams as $stream) {
        printContent($stream);
    }

    $endTime = microtime(true);
    echo "Tempo total: " . ($endTime - $startTime) . PHP_EOL;
}
function executeAsync(array $streams)
{
    // itera sobre os resources e seta eles como não bloqueantes
    foreach ($streams as $stream) {
        stream_set_blocking($stream, false);
    }

    echo "Executando assincrono" . PHP_EOL;
    $startTime = microtime(true);

    // enquanto houver arquivos para ler
    do {
        $readToRead = $streams;

        // verifica se há arquivos para ler
        $streamsList = stream_select($readToRead, $write, $except, 0, 200000);

        //se nao houver, continua
        if ($streamsList === 0) {
            continue;
        }

        // para cada arquivo, leia o conteudo
        foreach ($readToRead as $key => $stream) {
            printContent($stream);

            unset($streams[$key]);
        }
    }
    while (!empty($streams));

    $endTime = microtime(true);
    echo "Tempo total: " . ($endTime - $startTime) . PHP_EOL;
}

// fecha cada um dos streans
function closeStreams(array &$streams)
{
    foreach ($streams as $file) {
        fclose($file);
    }
}

if (!$assync) {
    openStreams($streams);
    createHttpStream($streams);
    executeSync($streams);
    closeStreams($streams);
    exit;
}

openStreams($streams);
createHttpStream($streams);
executeAsync($streams);
closeStreams($streams);