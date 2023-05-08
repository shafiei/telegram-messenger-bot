<?php

// do not change anything in this file
// Robot source by https://shafiei.dev

flush();
ob_start();
set_time_limit(0);
error_reporting(0);
ob_implicit_flush(1);
date_default_timezone_set('Asia/Tehran');
//--------[Your Config]--------//
require "config.php";
define('API_KEY',$Token);
define('API_KEY_CR',$Token);
//------------------------------------------------------------------------------
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
    }
}
function botCr($method,$datas=[]){
    $url = "https://api.telegram.org/bot".API_KEY_CR."/".$method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
    $res = curl_exec($ch);
    if(curl_error($ch)){
        var_dump(curl_error($ch));
    }else{
        return json_decode($res);
    }
}
//------------------------------------------------------------------------------
function CreateZip($files = array(),$destination) {
    if(file_exists($destination)){
		return false;
	}
    $valid_files = array();
    if(is_array($files)){
        foreach($files as $file){
            if(file_exists($file)){
                $valid_files[] = $file;
            }
        }
    }
    if(count($valid_files)){
        $zip = new ZipArchive();
        if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true){
            return false;
        }
        foreach($valid_files as $file){
            $zip->addFile($file,$file);
        }
        $zip->close();
        return file_exists($destination);
    }else{
        return false;
    }
}
function DeleteFolder($path){
	if($handle=opendir($path)){
		while (false!==($file=readdir($handle))){
			if($file<>"." AND $file<>".."){
				if(is_file($path.'/'.$file)){ 
					@unlink($path.'/'.$file);
				} 
				if(is_dir($path.'/'.$file)) { 
					deletefolder($path.'/'.$file); 
					@rmdir($path.'/'.$file); 
				}
			}
        }
    }
}
//------------------------------------------------------------------------------
function SendMessage($chat_id,$text,$mode,$reply = null,$keyboard = null){
	bot('SendMessage',[
	'chat_id'=>$chat_id,
	'text'=>$text,
	'parse_mode'=>$mode,
	'reply_to_message_id'=>$reply,
	'reply_markup'=>$keyboard
	]);
}
function EditMessageText($chat_id,$message_id,$text){
    bot('EditMessageText',[
    'chat_id'=>$chat_id,
    'message_id'=>$message_id,
    'text'=>$text
    ]);
}
function EditKeyboard($chat_id,$message_id,$keyboard){
	bot('EditMessageReplyMarkup',[
    'chat_id'=>$chat_id,
    'message_id'=>$message_id,
	'reply_markup'=>$keyboard
    ]);
}
function AnswerCallbackQuery($callback_query_id,$text,$show_alert = false){
	bot('AnswerCallbackQuery',[
    'callback_query_id'=>$callback_query_id,
    'text'=>$text,
	'show_alert'=>$show_alert
    ]);
}
function Forward($chatid,$from_id,$massege_id){
	bot('ForwardMessage',[
    'chat_id'=>$chatid,
    'from_chat_id'=>$from_id,
    'message_id'=>$massege_id
    ]);
}
function SendPhoto($chatid,$photo,$caption = null){
	bot('SendPhoto',[
	'chat_id'=>$chatid,
	'photo'=>$photo,
	'caption'=>$caption
	]);
}
function SendAudio($chatid,$audio,$caption = null,$sazande = null,$title = null){
	bot('SendAudio',[
	'chat_id'=>$chatid,
	'audio'=>$audio,
	'caption'=>$caption,
	'performer'=>$sazande,
	'title'=>$title
	]);
}
function SendDocument($chatid,$document,$caption = null){
	bot('SendDocument',[
	'chat_id'=>$chatid,
	'document'=>$document,
	'caption'=>$caption
	]);
}
function SendSticker($chatid,$sticker){
	bot('SendSticker',[
	'chat_id'=>$chatid,
	'sticker'=>$sticker
	]);
}
function SendVideo($chatid,$video,$caption = null,$duration = null){
	bot('SendVideo',[
	'chat_id'=>$chatid,
	'video'=>$video,
    'caption'=>$caption,
	'duration'=>$duration
	]);
}
function SendVoice($chatid,$voice,$caption = null){
	bot('SendVoice',[
	'chat_id'=>$chatid,
	'voice'=>$voice,
	'caption'=>$caption
	]);
}
function SendContact($chatid,$first_name,$phone_number){
	bot('SendContact',[
	'chat_id'=>$chatid,
	'first_name'=>$first_name,
	'phone_number'=>$phone_number
	]);
}
function GetProfile($from_id){
    $get = file_get_contents('https://api.telegram.org/bot'.API_KEY.'/getUserProfilePhotos?user_id='.$from_id);
    $decode = json_decode($get, true);
    $result = $decode['result'];
    $profile = $result['photos'][0][0]['file_id'];
    return $profile;
}
function GetChat($chatid){
	$get =  bot('GetChat',['chat_id'=> $chatid]);
	return $get;
}
function GetMe(){
	$get =  bot('GetMe',[]);
	return $get;
}
function CheckLink($text){
	global $data;
	if($data['lock']['link'] == "✅"){
		if(stripos($text, "t.me") !== false || stripos($text, "http") !== false || stripos($text, "www.") !== false){
			return true;
		}
	}
}
function CheckFilter($text){
	global $data;
	foreach($data['filters'] as $value){
		if(mb_strstr($text, "$value")){
			return true;
		}
	}
}
//------------------------------------------------------------------------------
$update = json_decode(file_get_contents('php://input'));
if(!isset($update->update_id)){ exit(); }
if(isset($update->message)){
    $message = $update->message; 
    $chat_id = $message->chat->id;
    $text = $message->text;
    $message_id = $message->message_id;
    $from_id = $message->from->id;
    $tc = $message->chat->type;
    $first_name = $message->from->first_name;
    $last_name = $message->from->last_name;
    $username = $message->from->username;
    $caption = $message->caption;
    $reply = $message->reply_to_message->forward_from->id;
    $reply_id = $message->reply_to_message->from->id;
    $forward = $message->forward_from;
    $forward_id = $message->forward_from->id;
    $sticker_id = $message->sticker->file_id;
    $video_id = $message->video->file_id;
    $voice_id = $message->voice->file_id;
    $file_id = $message->document->file_id;
    $music_id = $message->audio->file_id;
    $photo0_id = $message->photo[0]->file_id;
    $photo1_id = $message->photo[1]->file_id;
    $photo2_id = $message->photo[2]->file_id;
}
if(isset($update->callback_query)){
    $callback_query = $update->callback_query;
    $Data = $callback_query->data;
    $data_id = $callback_query->id;
    $chatid = $callback_query->message->chat->id;
    $fromid = $callback_query->from->id;
    $tccall = $callback_query->message->chat->type;
    $messageid = $callback_query->message->message_id;
}
$now = date("Y-m-d-h-i-sa");
//--------[Json]--------//
@$list = json_decode(file_get_contents("data/list.json"),true);
@$data = json_decode(file_get_contents("data/data.json"),true);
//------------------------------------------------------------------------------
$get = Bot('GetChatMember',[
'chat_id'=>$data['channel'],
'user_id'=>$from_id]);
$rank = $get->result->status;
$getd = BotCr('GetChatMember',[
'chat_id'=>"@shafieidev",// ایدی کانال
'user_id'=>$Dev]);
$rankdev = $getd->result->status;
//------------------------------------------------------------------------------
if($data['button']['profile']['stats'] != "⛔️"){
	if($data['button']['profile']['name'] == null){
		$profile_key = "🎫 پروفایل";
	}else{
		$profile_key = $data['button']['profile']['name'];
	}
}
if($data['button']['contact']['stats'] != "⛔️"){
	if($data['button']['contact']['name'] == null){
		$contact_key = "ارسال شماره شما 📞";
	}else{
		$contact_key = $data['button']['contact']['name'];
	}
}
if($data['button']['location']['stats'] != "⛔️"){
	if($data['button']['location']['name'] == null){
		$location_key = "⚓️ ارسال مکان شما";
	}else{
		$location_key = $data['button']['location']['name'];
	}
}
//------------------------------------------------
if($data['button']['profile']['stats'] == "⛔️"){
	$profile_key = null;
}
if($data['button']['contact']['stats'] == "⛔️"){
	$contact_key = null;
}
if($data['button']['location']['stats'] == "⛔️"){
	$location_key = null;
}
//--------[Buttons]--------//
if($profile_key == null and $contact_key == null and $location_key == null){
	$button_user = json_encode(['KeyboardRemove'=>[],'remove_keyboard'=>true]);
}else{
    foreach($data['buttons'] as $key => $name){
        $name = $data['buttons'][$key];
        $button_user[] = [['text'=>"$name"]];
    }
    $button_user[] = [ ['text'=>"$profile_key"] ];
    $button_user[] = [ ['text'=>"$contact_key",'request_contact' => true],['text'=>"$location_key",'request_location' => true] ];
    $button_user = json_encode(['keyboard'=> $button_user ,'resize_keyboard'=>true]);
}

if($data['stats'] == "off"){
$panel = json_encode(['keyboard'=>[
[['text'=>"📕 راهنما"],['text'=>"📊 آمار ربات"]],
[['text'=>"📬 ارسال همگانی"],['text'=>"📮 فروارد همگانی"]],
[['text'=>"📍 پاسخ سریع"],['text'=>"✉️ پیغام ها"]],
[['text'=>"✏️ پیام به کاربر"],['text'=>"⚗ دکمه ها"]],
[['text'=>"✅ روشن کردن ربات"]],
[['text'=>"سایر تنظیمات🛠"]],
[['text'=>"⏹ غیرفعال کردن حالت مدیریت"]]
],'resize_keyboard'=>true]);
}else{
$panel = json_encode(['keyboard'=>[
[['text'=>"📕 راهنما"],['text'=>"📊 آمار ربات"]],
[['text'=>"📬 ارسال همگانی"],['text'=>"📮 فروارد همگانی"]],
[['text'=>"📍 پاسخ سریع"],['text'=>"✉️ پیغام ها"]],
[['text'=>"✏️ پیام به کاربر"],['text'=>"⚗ دکمه ها"]],
[['text'=>"❎ خاموش کردن ربات"]],
[['text'=>"سایر تنظیمات🛠"]],
[['text'=>"⏹ غیرفعال کردن حالت مدیریت"]]
],'resize_keyboard'=>true]);
}
//------------------------------------------------------Zir Menu
$tanzhadi = json_encode(['keyboard'=>[
[['text'=>"📥 دانلودر"],['text'=>"📤 آپلودر"]],
[['text'=>"📣 قفل کانال"],['text'=>"💡 جعبه ابزار"]],
[['text'=>"⚜ کانتکت"],['text'=>"📯 فیلتر کلمه"]],
[['text'=>"👨🏻‍💻 ادمین ها"],['text'=>"🔐 قفل ها"]],
[['text'=>"🔘 بهینه سازی"],['text'=>"🗄 پشتیبان گیری"]],
[['text'=>"🀄️ اطلاعات کاربر"],['text'=>"▪️ کاربران اخیر"]],
[['text'=>"📢 آپدیت های اخیر"],['text'=>"💮 ریست ربات"]],
[['text'=>"▫️ برگشت ▫️"]]
],'resize_keyboard'=>true]);
$peygham = json_encode(['keyboard'=>[
[['text'=>"🔸 متن پیش فرض"],['text'=>"🔹 متن شروع"]],
[['text'=>"🎟 متن پروفایل"]],
[['text'=>"▫️ برگشت ▫️"]]
],'resize_keyboard'=>true]);
$quick = json_encode(['keyboard'=>[
[['text'=>"➖ حذف کلمه"],['text'=>"➕ افزودن کلمه"]],
[['text'=>"📑 لیست پاسخ ها"]],
[['text'=>"▫️ برگشت ▫️"]]
],'resize_keyboard'=>true]);
$button = json_encode(['keyboard'=>[
[['text'=>"➖ حذف دکمه"],['text'=>"➕ افزودن دکمه"]],
[['text'=>"⚜ نام دکمه های سیستمی"],['text'=>"📌 وضعیت نمایش دکمه ها"]],
[['text'=>"▫️ برگشت ▫️"]]
],'resize_keyboard'=>true]);
$button_tools = json_encode(['keyboard'=>[
[['text'=>"🖼 استیکر به عکس"],['text'=>"🎐 عکس به استیکر"]],
[['text'=>"🙏🏻 فال حافظ"],['text'=>"😬 جوک"]],
[['text'=>"🌐 مترجم"],['text'=>"🔍 ویکی پدیا"]],
[['text'=>"🖊 طراحی متن"],['text'=>"🎪 عکس تصادفی"]],
[['text'=>"▫️ برگشت ▫️"]]
],'resize_keyboard'=>true]);
$languages = json_encode(['keyboard'=>[
[['text'=>"🇵🇳 انگلیسی"],['text'=>"🇮🇷 فارسی"]],
[['text'=>"🇷🇺 روسی"],['text'=>"🇸🇦 عربی"]],
[['text'=>"🇹🇷 ترکی"],['text'=>"🇫🇷 فرانسوی"]],
[['text'=>"↩️ برگشت به جعبه ابزار"]]
],'resize_keyboard'=>true]);
$button_name = json_encode(['keyboard'=>[
[['text'=>"دکمه پروفایل"]],
[['text'=>"دکمه ارسال شماره"],['text'=>"دکمه ارسال مکان"]],
[['text'=>"↩️ بازگشت"]]
],'resize_keyboard'=>true]);
$button_filter = json_encode(['keyboard'=>[
[['text'=>"➖ حذف فیلتر"],['text'=>"➕ افزودن فیلتر"]],
[['text'=>"📑 لیست فیلتر"]],
[['text'=>"▫️ برگشت ▫️"]]
],'resize_keyboard'=>true]);
$button_admins = json_encode(['keyboard'=>[
[['text'=>"➖ حذف ادمین"],['text'=>"➕ افزودن ادمین"]],
[['text'=>"👥 لیست ادمین ها"]],
[['text'=>"▫️ برگشت ▫️"]]
],'resize_keyboard'=>true]);
$reset = json_encode(['keyboard'=>[
[['text'=>"بله، مطمئن هستم"]],
[['text'=>"▫️ برگشت ▫️"]]
],'resize_keyboard'=>true]);
$contact = json_encode(['keyboard'=>[
[['text'=>"📞 شماره من"]],
[['text'=>"☎️ تنظیم شماره",'request_contact'=>true]],
[['text'=>"▫️ برگشت ▫️"]]
],'resize_keyboard'=>true]);
//------------------------------------------------------Back
$back = json_encode(['keyboard'=>[
[['text'=>"▫️ برگشت ▫️"]]
],'resize_keyboard'=>true]);
$backans = json_encode(['keyboard'=>[
[['text'=>"↩️ برگشت "]]
],'resize_keyboard'=>true]);
$backbtn = json_encode(['keyboard'=>[
[['text'=>"↩️ بازگشت"]]
],'resize_keyboard'=>true]);
$backto = json_encode(['keyboard'=>[
[['text'=>"↩️ برگشت به جعبه ابزار"]]
],'resize_keyboard'=>true]);
//------------------------------------------------------Inline
$profile_btn = $data['button']['profile']['stats'];
$contact_btn = $data['button']['contact']['stats'];
$location_btn = $data['button']['location']['stats'];
$btnstats = json_encode(['inline_keyboard'=>[
[['text'=>"دکمه پروفایل $profile_btn",'callback_data'=>"profile"]],
[['text'=>"دکمه ارسال شماره $contact_btn",'callback_data'=>"contact"]],
[['text'=>"دکمه ارسال مکان $location_btn",'callback_data'=>"location"]],
]]);
//------------------------------------------------------
$remove = json_encode(['KeyboardRemove'=>[],'remove_keyboard'=>true]);
?>
