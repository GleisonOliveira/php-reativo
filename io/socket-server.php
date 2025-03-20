<?php

$timeToSleep = 5;

// cria um servidor socket
$socket = stream_socket_server('tcp://localhost:8001');

sleep($timeToSleep);

//cria uma conexao que fica aguardando até alguem se conectar
$connection = stream_socket_accept($socket);


// escreve os dados no stream
fwrite($connection, "Resposta do servidor socket após $timeToSleep segundos");

fclose($socket);