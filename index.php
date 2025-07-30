<?php
$update = json_decode(file_get_contents('php://input'), true);
$message = $update['message']['text'] ?? '';
$chat_id = $update['message']['chat']['id'] ?? '';

if ($message == '/start') {
    sendMessage($chat_id, "أهلاً بك في بوت PHP على Railway!");
}

function sendMessage($chat_id, $text) {
    $token = getenv('TELEGRAM_BOT_TOKEN');
    $url = "https://api.telegram.org/bot$token/sendMessage";
    $data = ['chat_id' => $chat_id, 'text' => $text];
    file_get_contents($url . '?' . http_build_query($data));
}
?>