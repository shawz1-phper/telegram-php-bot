<?php
$botToken = "8416879937:AAGaScKAEj5OIQxDy_Yfgi1TOngHpWVq678";
$content = file_get_contents("php://input");
$update = json_decode($content, true);

$chatId = $update["message"]["chat"]["id"] ?? null;
$text = $update["message"]["text"] ?? "";

if ($text == "/start") {
    $msg = "مرحبًا! تم تشغيل البوت بنجاح 🎉";
    file_get_contents("https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&text=" . urlencode($msg));
}
