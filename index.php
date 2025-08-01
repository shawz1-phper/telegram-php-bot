<?php
require_once 'functions.php';
$token = getenv("TELEGRAM_BOT_TOKEN");
define("API_KEY", $token);
$update = json_decode(file_get_contents('php://input'), true);

if (isset($update['message'])) {
    $chat_id = $update['message']['chat']['id'];
    $text = $update['message']['text'];

    if ($text === '/start') {
        sendTelegramMessage($token, [
            'chat_id' => $chat_id,
            'text' => "📆 اختر الشهر:",
            'reply_markup' => json_encode([
                'inline_keyboard' => generateMonthButtons()
            ], JSON_UNESCAPED_UNICODE)
        ]);
    }

} elseif (isset($update['callback_query'])) {
    $data = $update['callback_query']['data'];
    $chat_id = $update['callback_query']['message']['chat']['id'];
    $message_id = $update['callback_query']['message']['message_id'];

    if (strpos($data, 'month_') === 0) {
        $month = str_replace('month_', '', $data);
        $page = 1;

        $keyboard = array_merge(
            generateExtraButtons(),
            generateDayButtons($month, $page)
        );

        editMessage($token, $chat_id, $message_id, "📅 اختر اليوم من $month:", $keyboard);

    } elseif (strpos($data, 'daypage_') === 0) {
        list(, $month, $page) = explode('_', $data);

        $keyboard = array_merge(
            generateExtraButtons(),
            generateDayButtons($month, $page)
        );

        editMessage($token, $chat_id, $message_id, "📅 اختر اليوم من $month (صفحة $page):", $keyboard);

    } elseif (strpos($data, 'day_') === 0) {
        list(, $month, $day) = explode('_', $data);
        answerCallback($token, $update['callback_query']['id'], "✅ تم اختيار يوم $day من $month");
    }
}
if (strpos($data, 'month_') === 0) {
    $month = str_replace('month_', '', $data);
    $page = 1;

    // حفظ الشهر للمستخدم
    saveUserData($chat_id, ['month' => $month]);

    $keyboard = array_merge(
        generateExtraButtons(),
        generateDayButtons($month, $page)
    );

    editMessage($token, $chat_id, $message_id, "📅 اختر اليوم من $month:", $keyboard);
}
elseif (strpos($data, 'day_') === 0) {
    list(, $month, $day) = explode('_', $data);

    // حفظ اليوم للمستخدم
    saveUserData($chat_id, ['day' => $day]);

    answerCallback($token, $update['callback_query']['id'], "✅ تم اختيار يوم $day من $month");
}
if ($text === '/start') {
    $user = getUserData($chat_id);
    $info = isset($user['month']) && isset($user['day']) 
        ? "📝 آخر اختيار لك: {$user['day']} من {$user['month']}" 
        : "👋 مرحبًا بك!";

    sendTelegramMessage($token, [
        'chat_id' => $chat_id,
        'text' => "$info\n\n📆 اختر الشهر:",
        'reply_markup' => json_encode([
            'inline_keyboard' => generateMonthButtons()
        ], JSON_UNESCAPED_UNICODE)
    ]);
}
