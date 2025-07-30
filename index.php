<?php
$token = '8416879937:AAGaScKAEj5OIQxDy_Yfgi1TOngHpWVq678';
define("API_KEY",$token);
function bot($method,$datas=[]){
$url = "https://api.telegram.org/bot".API_KEY."/".$method;
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
$res = curl_exec($ch);
if(curl_error($ch)){
var_dump(curl_error($ch));
}else{
return json_decode($res);
}}
$update = json_decode(file_get_contents('php://input'), true);
$text = $update['message']['text'] ?? '';
$chat_id = $update['message']['chat']['id'] ?? '';
$data = $update['callback_query']['data'] ?? '';
if($text == "فحص"){
	$keyboard = getMonthsPageKeyboard($page);
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"جاري فحص القنوات إنتظر قليلا ..♻",
'reply_markup'=>json_encode(['inline_keyboard' => $keyboard])
]);}
if ($text == '/start') {
    sendMessage($chat_id, "أهلاً بك في بوت PHP على Railway!");
}

function sendMessage($chat_id, $text) {
    $url = "https://api.telegram.org/bot$token/sendMessage";
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
for($i=$j;$i<=$jj;$i++){
	$month = $months[$i];
        $monthRow[] = [
            'text' => $month,
            'callback_data' => 'month_' . strtolower($month)
        ];
    }
    $keyboard[] = $monthRow;
    $keyboard[] = ['text' => '⏮️ السابق', 'callback_data' => 'page_' . ($page - 1)];
    $keyboard[] = ['text' => '⏭️ التالي', 'callback_data' => 'page_' . ($page + 1)];
    return $keyboard;
}

if (strpos($data, 'page_') === 0) {
    $page = intval(str_replace('page_', '', $data));
    $replyMarkup = getMonthsPageKeyboard($page);
}
function isLeapYear($year) {
    return ($year % 4 == 0) && (($year % 100 != 0) || ($year % 400 == 0));
}

function getDaysInMonth($month, $year = null) {
    $month = strtolower($month);

    // سنة افتراضية = السنة الحالية إذا لم تُحدد
    if (!$year) {
        $year = date('Y');
    }

    $mapping = [
        'يناير'   => 31,
        'فبراير'  => isLeapYear($year) ? 29 : 28,
        'مارس'    => 31,
        'أبريل'   => 30,
        'مايو'    => 31,
        'يونيو'   => 30,
        'يوليو'   => 31,
        'أغسطس'   => 31,
        'سبتمبر'  => 30,
        'أكتوبر'  => 31,
        'نوفمبر'  => 30,
        'ديسمبر'  => 31,
    ];

    return $mapping[$month] ?? 30;
}
function getDaysPageKeyboard($month, $page) {
	
    $daysInMonth = getDaysInMonth($month, date('Y')); // أو استخدم التاريخ الحالي ديناميكيًا; // يمكن تخصيصه لاحقًا حسب الشهر
    $perPage = 10;
    $totalPages = ceil($daysInMonth / $perPage);

    if ($page < 1) $page = 1;
    if ($page > $totalPages) $page = $totalPages;

    $start = ($page - 1) * $perPage + 1;
    $end = min($start + $perPage - 1, $daysInMonth);

    $days = range($start, $end);

    $keyboard = [];

    // سطر 1: زر1 + زر2
    $keyboard[] = [
        ['text' => 'زر1', 'callback_data' => 'btn1'],
        ['text' => 'زر2', 'callback_data' => 'btn2']
    ];

    // سطر 2: زر3
    $keyboard[] = [
        ['text' => 'زر3', 'callback_data' => 'btn3']
    ];

    // سطر 3: أول 4 أيام
    $row1 = [];
    for ($i = 0; $i < 4 && isset($days[$i]); $i++) {
        $row1[] = [
            'text' => $days[$i],
            'callback_data' => "day_{$month}_{$days[$i]}"
        ];
    }
    if (!empty($row1)) $keyboard[] = $row1;

    // سطر 4: ثاني 4 أيام
    $row2 = [];
    for ($i = 4; $i < 8 && isset($days[$i]); $i++) {
        $row2[] = [
            'text' => $days[$i],
            'callback_data' => "day_{$month}_{$days[$i]}"
        ];
    }
    if (!empty($row2)) $keyboard[] = $row2;

    // سطر 5: ⏮️ السابق | يوم9 + يوم10 | ⏭️ التالي
    $row3 = [];

    // السابق (يسار)
    if ($page > 1) {
        $row3[] = ['text' => '⏮️', 'callback_data' => "daypage_{$month}_" . ($page - 1)];
    } else {
        $row3[] = ['text' => ' ', 'callback_data' => 'ignore'];
    }

    // اليوم 9 و 10 (وسط)
    $midButtons = [];
    if (isset($days[8])) {
        $midButtons[] = ['text' => $days[8], 'callback_data' => "day_{$month}_{$days[8]}"];
    }
    if (isset($days[9])) {
        $midButtons[] = ['text' => $days[9], 'callback_data' => "day_{$month}_{$days[9]}"];
    }
    $row3 = array_merge($row3, $midButtons);

    // التالي (يمين)
    if ($page < $totalPages) {
        $row3[] = ['text' => '⏭️', 'callback_data' => "daypage_{$month}_" . ($page + 1)];
    } else {
        $row3[] = ['text' => ' ', 'callback_data' => 'ignore'];
    }

    $keyboard[] = $row3;

    // سطر 6: زر4 + زر5
    $keyboard[] = [
        ['text' => 'زر4', 'callback_data' => 'btn4'],
        ['text' => 'زر5', 'callback_data' => 'btn5']
    ];

    return json_encode(['inline_keyboard' => $keyboard]);
}

if (strpos($data, 'month_') === 0) {
    $month = str_replace('month_', '', $data);
    $page = 1;

    // يمكن حفظ الشهر في ملف لكل user_id إن أردت

    $replyMarkup = getDaysPageKeyboard($month, $page);

    $chat_id = $update['callback_query']['message']['chat']['id'];
    $message_id = $update['callback_query']['message']['message_id'];

    file_get_contents("https://api.telegram.org/bot$token/editMessageText?" . http_build_query([
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "📅 اختر اليوم من شهر $month:",
        'reply_markup' => $replyMarkup
    ]));
}
if (strpos($data, 'daypage_') === 0) {
    $parts = explode('_', $data); // [daypage, الشهر, الصفحة]
    $month = $parts[1];
    $page = intval($parts[2]);

    $replyMarkup = getDaysPageKeyboard($month, $page);

    file_get_contents("https://api.telegram.org/bot$token/editMessageReplyMarkup?" . http_build_query([
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'reply_markup' => $replyMarkup
    ]));
}
?>
