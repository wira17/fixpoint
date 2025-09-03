<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>WebSocket Chat</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 20px;
      background: #f7f7f7;
    }
    h2 {
      color: #333;
    }
    #chat {
      border: 1px solid #ccc;
      padding: 15px;
      height: 300px;
      overflow-y: scroll;
      background: #fff;
      margin-bottom: 10px;
    }
    #message {
      width: 80%;
      padding: 8px;
    }
    #send {
      padding: 8px 15px;
      background: #28a745;
      color: #fff;
      border: none;
      cursor: pointer;
    }
    #send:hover {
      background: #218838;
    }
  </style>
</head>
<body>

  <h2>WebSocket Chat</h2>
  <div id="chat"></div>

  <input type="text" id="message" placeholder="Ketik pesan...">
  <button id="send">Kirim</button>

<script>
  const conn = new WebSocket('ws://localhost:8081');
    const chat = document.getElementById('chat');
    const messageInput = document.getElementById('message');
    const sendBtn = document.getElementById('send');

    conn.onopen = function() {
      appendMessage("🟢 Tersambung ke server wira.");
    };

    conn.onmessage = function(e) {
      appendMessage("👤 " + e.data);
    };

    conn.onclose = function() {
      appendMessage("🔴 Koneksi terputus.");
    };

    sendBtn.onclick = function() {
      const msg = messageInput.value;
      if (msg.trim() !== '') {
        conn.send(msg);
        appendMessage("🟣 Saya: " + msg);
        messageInput.value = '';
      }
    };

    function appendMessage(msg) {
      const div = document.createElement('div');
      div.textContent = msg;
      chat.appendChild(div);
      chat.scrollTop = chat.scrollHeight;
    }
  </script>

</body>
</html>
