<?php

function sendTelegramMessage($token, $params) {
    $ch = curl_init("https://api.telegram.org/bot$token/sendMessage");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function editMessage($token, $chat_id, $message_id, $text, $keyboard) {
    $params = [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => $text,
        'reply_markup' => json_encode(['inline_keyboard' => $keyboard], JSON_UNESCAPED_UNICODE)
    ];
    $ch = curl_init("https://api.telegram.org/bot$token/editMessageText");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

function answerCallback($token, $callback_id, $text) {
    $params = [
        'callback_query_id' => $callback_id,
        'text' => $text,
        'show_alert' => false
    ];
    $ch = curl_init("https://api.telegram.org/bot$token/answerCallbackQuery");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

function isLeapYear($year) {
    return ($year % 4 == 0) && (($year % 100 != 0) || ($year % 400 == 0));
}

function getDaysInMonth($month, $year = null) {
    $month = strtolower($month);
    if (!$year) $year = date('Y');

    $mapping = [
        'يناير' => 31,
        'فبراير' => isLeapYear($year) ? 29 : 28,
        'مارس' => 31,
        'أبريل' => 30,
        'مايو' => 31,
        'يونيو' => 30,
        'يوليو' => 31,
        'أغسطس' => 31,
        'سبتمبر' => 30,
        'أكتوبر' => 31,
        'نوفمبر' => 30,
        'ديسمبر' => 31,
    ];

    return $mapping[$month] ?? 30;
}

function generateMonthButtons() {
    $months = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
    $rows = [];
    for ($i = 0; $i < count($months); $i += 3) {
        $chunk = array_slice($months, $i, 3);
        $rows[] = array_map(fn($m) => ['text' => $m, 'callback_data' => "month_$m"], $chunk);
    }
    $rows[] = [['text' => '⏮️ السابق', 'callback_data' => 'ignore'], ['text' => 'التالي ⏭️', 'callback_data' => 'ignore']];
    return $rows;
}

function generateExtraButtons() {
    return [
        [['text' => 'زر1', 'callback_data' => 'btn1'], ['text' => 'زر2', 'callback_data' => 'btn2']],
        [['text' => 'زر3', 'callback_data' => 'btn3']],
        [['text' => 'زر4', 'callback_data' => 'btn4'], ['text' => 'زر5', 'callback_data' => 'btn5']],
    ];
}

function generateDayButtons($month, $page) {
    $daysInMonth = getDaysInMonth($month);
    $perPage = 10;
    $totalPages = ceil($daysInMonth / $perPage);
    $page = max(1, min($page, $totalPages));

    $start = ($page - 1) * $perPage + 1;
    $end = min($start + $perPage - 1, $daysInMonth);
    $days = range($start, $end);

    $rows = [];

    $rows[] = array_map(fn($d) => ['text' => "$d", 'callback_data' => "day_{$month}_$d"], array_slice($days, 0, 4));
    $rows[] = array_map(fn($d) => ['text' => "$d", 'callback_data' => "day_{$month}_$d"], array_slice($days, 4, 4));

    $nav = [];

    $nav[] = $page > 1
        ? ['text' => '⏮️', 'callback_data' => "daypage_{$month}_" . ($page - 1)]
        : ['text' => ' ', 'callback_data' => 'ignore'];

    if (isset($days[8])) $nav[] = ['text' => $days[8], 'callback_data' => "day_{$month}_{$days[8]}"];
    if (isset($days[9])) $nav[] = ['text' => $days[9], 'callback_data' => "day_{$month}_{$days[9]}"];

    $nav[] = $page < $totalPages
        ? ['text' => '⏭️', 'callback_data' => "daypage_{$month}_" . ($page + 1)]
        : ['text' => ' ', 'callback_data' => 'ignore'];

    $rows[] = $nav;

    return $rows;
}
function saveUserData($user_id, $data) {
    $file = 'data.json';
    $allData = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

    $allData[$user_id] = array_merge($allData[$user_id] ?? [], $data);

    file_put_contents($file, json_encode($allData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

function getUserData($user_id) {
    $file = 'data.json';
    if (!file_exists($file)) return [];
    $allData = json_decode(file_get_contents($file), true);
    return $allData[$user_id] ?? [];
}
