<?php
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class MyChat implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        echo "[INIT] MyChat WebSocket server initialized...\n";
    }

    public function onOpen(ConnectionInterface $conn) {
        // Simpan koneksi baru dan buat metadata kosong (nanti diisi user_id)
        $this->clients->attach($conn, [
            'user_id' => null,
            'nama' => null
        ]);

        echo "[CONNECT] New connection! Resource ID: {$conn->resourceId}\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        echo "[MESSAGE] Received message from ({$from->resourceId}): $msg\n";

        $data = json_decode($msg, true);
        if (!$data || !isset($data['dari_id'], $data['ke_id'], $data['pesan'])) {
            $from->send(json_encode([
                'type' => 'error',
                'message' => 'Format pesan tidak valid.'
            ]));
            return;
        }

        // Simpan user_id di metadata koneksi jika belum ada
        $meta = $this->clients[$from];
        if (!$meta['user_id']) {
            $meta['user_id'] = $data['dari_id'];
            $meta['nama'] = $data['nama_pengirim'] ?? 'Anonim';
            $this->clients[$from] = $meta;
            echo "[AUTH] Resource {$from->resourceId} mapped to user_id {$meta['user_id']}\n";
        }

        // Kirim pesan ke koneksi tujuan saja
       foreach ($this->clients as $client) {
    $clientMeta = $this->clients[$client];
    echo "➡️  Client: {$client->resourceId}, user_id: {$clientMeta['user_id']}\n";

    if ($clientMeta['user_id'] == $data['ke_id']) {
        $client->send(json_encode([
            'type' => 'chat',
            'dari_id' => $data['dari_id'],
            'ke_id' => $data['ke_id'],
            'nama_pengirim' => $meta['nama'],
            'pesan' => $data['pesan'],
            'timestamp' => date('Y-m-d H:i:s')
        ]));
    }
}


        

        // (Opsional) Kirim kembali ke pengirim sebagai konfirmasi
        $from->send(json_encode([
            'type' => 'sent',
            'pesan' => $data['pesan'],
            'ke_id' => $data['ke_id'],
            'timestamp' => date('Y-m-d H:i:s')
        ]));
    }

    public function onClose(ConnectionInterface $conn) {
        $meta = $this->clients[$conn];
        echo "[DISCONNECT] Connection {$conn->resourceId} (user_id: {$meta['user_id']}) has disconnected\n";
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "[ERROR] Error on connection {$conn->resourceId}: {$e->getMessage()}\n";
        $conn->close();
    }
}
