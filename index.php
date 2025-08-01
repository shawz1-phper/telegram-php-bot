<?php
$token = getenv("TELEGRAM_BOT_TOKEN");
define("API_KEY", $token);

function bot($method, $datas = []) {
    $url = "https://api.telegram.org/bot" . API_KEY . "/" . $method;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
    $res = curl_exec($ch);
    if (curl_error($ch)) {
        var_dump(curl_error($ch));
    } else {
        return json_decode($res);
    }
}
$update = json_decode(file_get_contents('php://input'), true);
$text = $update['message']['text'] ?? '';
$chat_id = $update['message']['chat']['id'] ?? '';
$data = $update['callback_query']['data'] ?? '';
$callback_id = $update['callback_query']['id'] ?? '';
$callback_chat_id = $update['callback_query']['message']['chat']['id'] ?? '';
$message_id = $update['callback_query']['message']['message_id'] ?? '';
$user_id = $update['callback_query']['from']['id'] ?? '';

if ($text == "/info") {
	$keyboard = [];
    $keyboard[] = [
        ['text' => 'تعليق ', 'callback_data' => 'comm'],
        ['text' => 'رد', 'callback_data' => 'rep']
    ];
    $keyboard[] = [
        ['text' => 'تفاعل', 'callback_data' => 're']
    ];
    $page = 1;
    $key = getMonthsPageKeyboard($page);
    if(!$data){
    $key1[] = [
        ['text' => 'تغيير', 'callback_data' => 'ch']
    ];}
    $merge = array_merge($keyboard,$key,$key1);
    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => "Info list.♻",
        'reply_markup' => json_encode(['inline_keyboard' => $merge])
    ]);
}

if ($text == '/start') {
	$d = ('0123456');
	$dd = srtlen($d);
    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => "اهلا بك.......👋 ".$dd,
    ]);
}
function getMonthsPageKeyboard($page) {
    $months = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];

    $monthsPerPage = 6;
    $totalPages = ceil(count($months) / $monthsPerPage);

    if ($page < 1) $page = 1;
    if ($page > $totalPages) $page = $totalPages;

    $startIndex = ($page - 1) * $monthsPerPage;
    $monthsOnPage = array_slice($months, $startIndex, $monthsPerPage);

    $keyboard = [];

    // الصف الأول (3 أشهر)
    $row1 = [];
    for ($i = 0; $i < 3 && isset($monthsOnPage[$i]); $i++) {
        $row1[] = [
            'text' => $monthsOnPage[$i],
            'callback_data' => 'month_' . strtolower($monthsOnPage[$i])
        ];
    }
    if (!empty($row1)) $keyboard[] = $row1;

    // الصف الثاني (3 أشهر التالية)
    $row2 = [];
    for ($i = 3; $i < 6 && isset($monthsOnPage[$i]); $i++) {
        $row2[] = [
            'text' => $monthsOnPage[$i],
            'callback_data' => 'month_' . strtolower($monthsOnPage[$i])
        ];
    }
    if (!empty($row2)) $keyboard[] = $row2;

    // أزرار التنقل بين الصفحات
    $navRow = [];

    if ($page > 1) {
        $navRow[] = ['text' => '⏮️ السابق', 'callback_data' => 'page_' . ($page - 1)];
    }

    if ($page < $totalPages) {
        $navRow[] = ['text' => '⏭️ التالي', 'callback_data' => 'page_' . ($page + 1)];
    }

    if (!empty($navRow)) $keyboard[] = $navRow;

    return $keyboard;
}

if (strpos($data, 'page_') === 0) {
    $page = intval(str_replace('page_', '', $data));
    $replyMarkup = getMonthsPageKeyboard($page);
    bot('editMessageReplyMarkup', [
        'chat_id' => $callback_chat_id,
        'message_id' => $message_id,
        'reply_markup' => json_encode(['inline_keyboard' => $replyMarkup])
    ]);
}
?>
