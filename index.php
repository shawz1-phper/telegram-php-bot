<?php
$token = '8416879937:AAGaScKAEj5OIQxDy_Yfgi1TOngHpWVq678';
$update = json_decode(file_get_contents('php://input'), true);
$text = $update['message']['text'] ?? '';
$chat_id = $update['message']['chat']['id'] ?? '';
$data = $update['callback_query']['data'] ?? '';
if ($text == '/start') {
    sendMessage($chat_id, "أهلاً بك في بوت PHP على Railway!");
}

function sendMessage($chat_id, $text) {
    $url = "https://api.telegram.org/bot$token/sendMessage";
    $data = ['chat_id' => $chat_id, 'text' => $text];
    file_get_contents($url . '?' . http_build_query($data));
}
