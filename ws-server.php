<?php
// ws-server.php
require __DIR__ . '/vendor/autoload.php';

error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED); // hide dynamic property notices

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Server\IoServer;
use React\EventLoop\Factory as LoopFactory;
use React\Socket\Server as ReactServer;
use React\Socket\ConnectionInterface as ReactConnection;

/**
 * Main WebSocket app: keeps track of connected browsers (teller, kitchen, claim, etc.)
 * and broadcasts JSON messages to all of them.
 */
class OrderServer implements MessageComponentInterface
{
    /** @var \SplObjectStorage|ConnectionInterface[] */
    protected $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage();
        echo "OrderServer instance created\n";
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "New WS connection ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        // You can log or act on client messages if you want; for now just ignore or echo.
        echo "WS message from {$from->resourceId}: {$msg}\n";
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        echo "WS connection {$conn->resourceId} closed\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "WS error on {$conn->resourceId}: {$e->getMessage()}\n";
        $conn->close();
    }

    /**
     * Broadcast a JSON payload to all connected WS clients.
     */
    public function pushJson(array $payload): void
    {
        if (!$this->clients->count()) {
            return;
        }
        $msg = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        foreach ($this->clients as $client) {
            $client->send($msg);
        }
        echo "Pushed to WS clients: {$msg}\n";
    }
}

// ----------------- BOOTSTRAP -----------------

$loop = LoopFactory::create();
$orderServer = new OrderServer();

// WebSocket server on :8080, with proper HTTP handshake
$webSock = new ReactServer('0.0.0.0:8080', $loop);

$wsApp = new HttpServer(
    new WsServer($orderServer)
);

$wsServer = new IoServer($wsApp, $webSock, $loop);

// TCP push bridge on 127.0.0.1:9001
$tcpServer = new ReactServer('127.0.0.1:9001', $loop);

$tcpServer->on('connection', function (ReactConnection $conn) use ($orderServer) {
    echo "TCP push client connected\n";
    $buffer = '';

    $conn->on('data', function ($data) use (&$buffer, $conn, $orderServer) {
        $buffer .= $data;

        // For simplicity, assume each write is a full JSON payload, then close.
        $trim = trim($buffer);
        if ($trim === '') {
            return;
        }

        $buffer = ''; // reset for next connection
        $payload = json_decode($trim, true);

        if (is_array($payload)) {
            $orderServer->pushJson($payload);
        } else {
            echo "Invalid JSON from TCP push: {$trim}\n";
        }

        $conn->end();
    });

    $conn->on('error', function (\Exception $e) {
        echo "TCP push error: {$e->getMessage()}\n";
    });

    $conn->on('close', function () {
        echo "TCP push client disconnected\n";
    });
});

echo "OrderServer started\n";
echo "WebSocket server listening on ws://0.0.0.0:8080\n";
echo "TCP push bridge listening on 127.0.0.1:9001\n";

$loop->run();
