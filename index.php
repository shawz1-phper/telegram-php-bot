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

if (!file_exists('storage')) {
    mkdir('storage', 0777, true);
}

$update = json_decode(file_get_contents('php://input'), true);
$text = $update['message']['text'] ?? '';
$chat_id = $update['message']['chat']['id'] ?? '';
$data = $update['callback_query']['data'] ?? '';
$callback_id = $update['callback_query']['id'] ?? '';
$callback_chat_id = $update['callback_query']['message']['chat']['id'] ?? '';
$message_id = $update['callback_query']['message']['message_id'] ?? '';
$user_id = $update['callback_query']['from']['id'] ?? '';

if ($text == "فحص") {
    $page = 1;
    $keyboard = getMonthsPageKeyboard($page);
    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => "جاري فحص القنوات إنتظر قليلاً ..♻",
        'reply_markup' => json_encode(['inline_keyboard' => $keyboard])
    ]);
}

if ($text == '/start') {
    sendMessage($chat_id, "أهلاً بك في بوت PHP على Render! 🎉");
}

function sendMessage($chat_id, $text) {
    $url = "https://api.telegram.org/bot" . API_KEY . "/sendMessage";
    $data = ['chat_id' => $chat_id, 'text' => $text];
    file_get_contents($url . '?' . http_build_query($data));
}

function getMonthsPageKeyboard($page) {
    $months = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
    if ($page < 1) $page = 1;
    if ($page > 2) $page = 2;
    $keyboard = [];

    if($page == '1'){
$j = 1;
$jj = 6;}
if($page == '2'){
$j = 7;
$jj = 12;}
    $monthRow = [];
    for ($i = $j; $i < $jj; $i++) {
        $month = $months[$i];
        $monthRow[] = [
            'text' => $month,
            'callback_data' => 'month_' . strtolower($month)
        ];
    }
    $keyboard[] = $monthRow;
    $keyboard[] = [
        ['text' => '⏮️ السابق', 'callback_data' => 'page_' . ($page - 1)],
        ['text' => '⏭️ التالي', 'callback_data' => 'page_' . ($page + 1)]
    ];
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

function isLeapYear($year) {
    return ($year % 4 == 0) && (($year % 100 != 0) || ($year % 400 == 0));
}

function getDaysInMonth($month, $year = null) {
    $month = strtolower($month);
    if (!$year) {
        $year = date('Y');
    }
    $mapping = [
        'يناير' => 31, 'فبراير' => isLeapYear($year) ? 29 : 28,
        'مارس' => 31, 'أبريل' => 30, 'مايو' => 31,
        'يونيو' => 30, 'يوليو' => 31, 'أغسطس' => 31,
        'سبتمبر' => 30, 'أكتوبر' => 31, 'نوفمبر' => 30, 'ديسمبر' => 31,
    ];
    return $mapping[$month] ?? 30;
}

function getDaysPageKeyboard($month, $page) {
    $daysInMonth = getDaysInMonth($month);
    $perPage = 10;
    $totalPages = ceil($daysInMonth / $perPage);
if ($page < 1) $page = 1;
    if ($page > $totalPages) $page = '1';
    $start = ($page - 1) * $perPage + 1;
    $end = min($start + $perPage - 1, $daysInMonth);
    $days = range($start, $end);

    $keyboard = [];
    $keyboard[] = [
        ['text' => 'زر1', 'callback_data' => 'btn1'],
        ['text' => 'زر2', 'callback_data' => 'btn2']
    ];
    $keyboard[] = [
        ['text' => 'زر3', 'callback_data' => 'btn3']
    ];

    $row1 = [];
    for ($i = 0; $i < 4 && isset($days[$i]); $i++) {
        $row1[] = ['text' => $days[$i], 'callback_data' => "day_{$month}_{$days[$i]}"];
    }
    if (!empty($row1)) $keyboard[] = $row1;

    $row2 = [];
    for ($i = 4; $i < 8 && isset($days[$i]); $i++) {
        $row2[] = ['text' => $days[$i], 'callback_data' => "day_{$month}_{$days[$i]}"];
    }
    if (!empty($row2)) $keyboard[] = $row2;

    $row3 = [];
    $row3[] = ['text' => '⏮️', 'callback_data' => "daypage_{$month}_" . ($page - 1)];
    if (isset($days[8])) $row3[] = ['text' => $days[8], 'callback_data' => "day_{$month}_{$days[8]}"];
    if (isset($days[9])) $row3[] = ['text' => $days[9], 'callback_data' => "day_{$month}_{$days[9]}"];
    $row3[] = ['text' => '⏭️', 'callback_data' => "daypage_{$month}_" . ($page + 1)];

    $keyboard[] = $row3;
    $keyboard[] = [
        ['text' => 'زر4', 'callback_data' => 'btn4'],
        ['text' => 'زر5', 'callback_data' => 'btn5']
    ];

    return json_encode(['inline_keyboard' => $keyboard]);
}

if (strpos($data, 'month_') === 0) {
    $month = str_replace('month_', '', $data);
    $page = 1;
    $replyMarkup = getDaysPageKeyboard($month, $page);
    bot('editMessageText', [
        'chat_id' => $callback_chat_id,
        'message_id' => $message_id,
        'text' => "📅 اختر اليوم من شهر $month:",
        'reply_markup' => $replyMarkup
    ]);
}

if (strpos($data, 'daypage_') === 0) {
    $parts = explode('_', $data);
    $month = $parts[1];
    $page = intval($parts[2]);
    $replyMarkup = getDaysPageKeyboard($month, $page);
    bot('editMessageReplyMarkup', [
        'chat_id' => $callback_chat_id,
        'message_id' => $message_id,
        'reply_markup' => $replyMarkup
    ]);
}

if (strpos($data, 'day_') === 0) {
    $parts = explode('_', $data);
    $month = $parts[1];
    $day = $parts[2];
    
    // ✅ حفظ اليوم المختار في ملف حسب user_id
    file_put_contents("storage/".$user_id.".txt", "$month:$day\n", FILE_APPEND);

    // ✅ تعديل نفس الرسالة بنجاح
    bot('editMessageText', [
        'chat_id' => $callback_chat_id,
        'message_id' => $message_id,
        'text' => "✅ لقد اخترت يوم $day من شهر $month."
    ]);
}
?>
