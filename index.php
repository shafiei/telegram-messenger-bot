<?php

// do not change anything in this file
// Robot source by https://shafiei.dev

include_once("handler.php");
flush();
//------------------------------------------------------------------------------
if(in_array($from_id, $list['ban'])){
	exit();
}
if(($tc == 'group' | $tc == 'supergroup') and $data['feed'] != null and $chat_id != $data['feed']){
	SendMessage($chat_id,"■ ربات اجازه ورود به هیچ گروهی را ندارد!", 'Html');
	Bot('LeaveChat',['chat_id'=> $chat_id]);
	exit();
}
@$flood = json_decode(file_get_contents("data/flood.json"),true);
@$floods = $flood['flood']["$now-$from_id"];
@$flood['flood']["$now-$from_id"] = $floods+1;
@file_put_contents("data/flood.json",json_encode($flood));
@$flood = json_decode(file_get_contents("data/flood.json"),true);
@$floods = $flood['flood']["$now-$from_id"];

if($floods >= 3 and $from_id != $Dev and $tc == 'private'){
    if($list['ban'] == null){
        $list['ban'] = [];
    }
    unlink("data/flood.json");
	array_push($list['ban'], $from_id);
	file_put_contents("data/list.json",json_encode($list));
	SendMessage($from_id,"■ شما به علت اسپم، از ربات مسدود شدید.", 'MarkDown', null, $remove);
	SendMessage($Dev,"■ کاربر [$from_id](tg://user?id=$from_id) به علت اسپم از ربات مسدود گردید.", 'MarkDown');
}
elseif($data['stats'] == "off" and $from_id != $Dev and $tc == 'private'){
	SendMessage($chat_id,"■ ربات تا اطلاع ثانوی توسط ادمین خاموش می باشد\n■ لطفا پیام خود را در وقتی دیگر ارسال کنید", null, $message_id);
	exit();
}
//--------[User]--------//
elseif(preg_match('/^\/(start)$/i',$text) and $from_id != $Dev and $tc == 'private'){
	$start = $data['text']['start'];
	if($start != null){
		$get = json_decode(file_get_contents("https://api.raminsudo.ir/time.php"), true);
		$date = $get['date'];
		$today = $get['today'];
		
		$start = str_replace('F-NAME', "$first_name", $start);
		$start = str_replace('L-NAME', "$last_name", $start);
		$start = str_replace('U-NAME', "$username", $start);
		$start = str_replace('TIME', date('H:i:s'), $start);
		$start = str_replace('DATE', $date, $start);
		$start = str_replace('TODAY', $today, $start);
		SendMessage($chat_id,$start, null, $message_id, $button_user);
	}else{
		SendMessage($chat_id,"■ سلام، به ربات من خوش آمدید.", null, $message_id,$button_user);
	}
}
elseif(preg_match('/^\/(creator)/i',$text) and $from_id != $Dev){
	SendMessage($chat_id,"@OpenSource_IR", null, $message_id);
}
elseif(($rank == 'left' and $from_id != $Dev) && ($data['lock']['channel'] == "✅" and $data['channel'] != null)){
	SendMessage($chat_id,"■ دوست عزیز برای استفاده از ربات ابتدا وارد کانال :\n\n➔ ".$data['channel']."\n\n■ شوید و سپس به ربات برگشته و /start بزنید.", null, $message_id, $remove);
}
elseif($text == $profile_key and isset($text) and $tc == 'private'){
	$profile = isset($data['text']['profile']) ? $data['text']['profile'] : "■ پروفایل خالی است!";
	if($from_id == $Dev){
		SendMessage($chat_id,$profile, null, $message_id);
	}else{
		SendMessage($chat_id,$profile, null, $message_id, $button_user);
	}
}
elseif($data['quick'][$text] != null and $from_id != $Dev and $tc == 'private'){
	$answer = $data['quick'][$text];
	
	$get = json_decode(file_get_contents("https://api.raminsudo.ir/time.php"), true);
	$date = $get['date'];
	$today = $get['today'];
		
	$answer = str_replace('F-NAME', "$first_name", $answer);
	$answer = str_replace('L-NAME', "$last_name", $answer);
	$answer = str_replace('U-NAME', "$username", $answer);
	$answer = str_replace('TIME', date('H:i:s'), $answer);
	$answer = str_replace('DATE', $date, $answer);
	$answer = str_replace('TODAY', $today, $answer);
	SendMessage($chat_id,$answer, null, $message_id, $button_user);
}
elseif($data['buttonans'][$text] != null and $tc == 'private'){
	if($from_id != $Dev){
		$ansbtn = $data['buttonans'][$text];
		SendMessage($chat_id,"$ansbtn", null, $message_id, $button_user);
	}else{
		if($data['step'] == "none"){
			$ansbtn = $data['buttonans'][$text];
			SendMessage($chat_id,"$ansbtn", null, $message_id);
		}
	}
}
elseif(isset($update->message) and $from_id != $Dev and $data['feed'] == null && $tc == 'private'){
	$done = isset($data['text']['done'])?$data['text']['done'] : "■ پیام ارسال شد.";
	
	$get = json_decode(file_get_contents("https://api.raminsudo.ir/time.php"), true);
	$date = $get['date'];
	$today = $get['today'];
		
	$done = str_replace('F-NAME', "$first_name", $done);
	$done = str_replace('L-NAME', "$last_name", $done);
	$done = str_replace('U-NAME', "$username", $done);
	$done = str_replace('TIME', date('H:i:s'), $done);
	$done = str_replace('DATE', $date, $done);
	$done = str_replace('TODAY', $today, $done);
	if(isset($update->message->forward_from) or isset($update->message->forward_from_chat)){
		if($data['lock']['forward'] == "✅"){
			SendMessage($chat_id,"🔐 ارسال این نوع رسانه توسط ادمین محدود شده!", 'Html' ,$message_id, $button_user);
			exit();
		}
	}
	if(isset($message->text)){
		if($data['lock']['text'] != "✅"){
			$checklink = CheckLink($text);
			$checkfilter = CheckFilter($text);
			if($checklink != true){
				if($checkfilter != true){
					Forward($Dev, $chat_id, $message_id);
					SendMessage($chat_id,"$done", 'Html' ,$message_id, $button_user);
                    SendMessage($Dev, "[$chat_id](tg://user?id=$chat_id)", 'MarkDown');
                    // if(isset($update->message->forward_from) or isset($update->message->forward_from_chat)){
					// 	SendMessage($Dev,"■ ارسال کننده پیام فوق : [$from_id](tg://user?id=$from_id)", 'MarkDown');
					// }
				}
			}
			if($checklink == true){
				SendMessage($chat_id,"🔐 ارسال لینک توسط ادمین محدود شده!", 'Html' ,$message_id, $button_user);
			}
			if($checkfilter == true){
				SendMessage($chat_id,"■ پیام شما به دلیل داشتن کلمه غیر مجاز ارسال نشد!", 'Html' ,$message_id, $button_user);
			}
		}else{
			SendMessage($chat_id,"🔐 ارسال این نوع رسانه توسط ادمین محدود شده!", 'Html' ,$message_id, $button_user);
		}
	}
	if(isset($message->photo)){
		if($data['lock']['photo'] != "✅"){
			Forward($Dev, $chat_id, $message_id);
			SendMessage($chat_id,"$done", 'Html' ,$message_id, $button_user);
			SendMessage($Dev, "[$chat_id](tg://user?id=$chat_id)", 'MarkDown');
            // if(isset($update->message->forward_from) or isset($update->message->forward_from_chat)){
			// 	SendMessage($Dev,"■ ارسال کننده پیام فوق : [$from_id](tg://user?id=$from_id)", 'MarkDown');
			// }
		}else{
			SendMessage($chat_id,"🔐 ارسال این نوع رسانه توسط ادمین محدود شده!", 'Html' ,$message_id, $button_user);
		}
	}
	if(isset($message->video)){
		if($data['lock']['video'] != "✅"){
			Forward($Dev, $chat_id, $message_id);
			SendMessage($chat_id,"$done", 'Html' ,$message_id, $button_user);
			SendMessage($Dev, "[$chat_id](tg://user?id=$chat_id)", 'MarkDown');
            // if(isset($update->message->forward_from) or isset($update->message->forward_from_chat)){
			// 	SendMessage($Dev,"■ ارسال کننده پیام فوق : [$from_id](tg://user?id=$from_id)", 'MarkDown');
			// }
		}else{
			SendMessage($chat_id,"🔐 ارسال این نوع رسانه توسط ادمین محدود شده!", 'Html' ,$message_id, $button_user);
		}
	}
	if(isset($message->voice)){
		if($data['lock']['voice'] != "✅"){
			Forward($Dev, $chat_id, $message_id);
			SendMessage($chat_id,"$done", 'Html' ,$message_id, $button_user);
			SendMessage($Dev, "[$chat_id](tg://user?id=$chat_id)", 'MarkDown');
            // if(isset($update->message->forward_from) or isset($update->message->forward_from_chat)){
			// 	SendMessage($Dev,"■ ارسال کننده پیام فوق : [$from_id](tg://user?id=$from_id)", 'MarkDown');
			// }
		}else{
			SendMessage($chat_id,"🔐 ارسال این نوع رسانه توسط ادمین محدود شده!", 'Html' ,$message_id, $button_user);
		}
	}
	if(isset($message->audio)){
		if($data['lock']['audio'] != "✅"){
			Forward($Dev, $chat_id, $message_id);
            // SendMessage($Dev,"■ ارسال کننده پیام فوق : [$from_id](tg://user?id=$from_id)", 'MarkDown');
			SendMessage($chat_id,"$done", 'Html' ,$message_id, $button_user);
			SendMessage($Dev, "[$chat_id](tg://user?id=$chat_id)", 'MarkDown');
		}else{
			SendMessage($chat_id,"🔐 ارسال این نوع رسانه توسط ادمین محدود شده!", 'Html' ,$message_id, $button_user);
		}
	}
	if(isset($message->sticker)){
		if($data['lock']['sticker'] != "✅"){
			Forward($Dev, $chat_id, $message_id);
            // SendMessage($Dev,"■ ارسال کننده پیام فوق : [$from_id](tg://user?id=$from_id)", 'MarkDown');
			SendMessage($chat_id,"$done", 'Html' ,$message_id, $button_user);
			SendMessage($Dev, "[$chat_id](tg://user?id=$chat_id)", 'MarkDown');
		}else{
			SendMessage($chat_id,"🔐 ارسال این نوع رسانه توسط ادمین محدود شده!", 'Html' ,$message_id, $button_user);
		}
	}
	if(isset($message->document)){
		if($data['lock']['document'] != "✅"){
			Forward($Dev, $chat_id, $message_id);
			SendMessage($chat_id,"$done", 'Html' ,$message_id, $button_user);
			SendMessage($Dev, "[$chat_id](tg://user?id=$chat_id)", 'MarkDown');
            // if(isset($update->message->forward_from) or isset($update->message->forward_from_chat)){
			// 	SendMessage($Dev,"■ ارسال کننده پیام فوق : [$from_id](tg://user?id=$from_id)", 'MarkDown');
			// }
		}else{
			SendMessage($chat_id,"🔐 ارسال این نوع رسانه توسط ادمین محدود شده!", 'Html' ,$message_id, $button_user);
		}
	}
}
//--------[Feed]--------//
elseif(preg_match('/^\/(setfeed)$/i',$text) and ($tc == 'group' | $tc == 'supergroup') and $from_id == $Dev){
    $data['feed'] = "$chat_id";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($chat_id,"■ این گروه برای پشتیبانی تنظیم گردید.", 'Html' ,$message_id, $remove);
}
elseif(preg_match('/^\/(delfeed)$/i',$text) and $tc == 'private' and $from_id == $Dev){
    unset($data['feed']);
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($chat_id,"■ گروه پشتیبانی حذف شد و پیام ها به ادمین ارسال خواهد شد.", 'Html' ,$message_id);
}
elseif(isset($update->message) and $from_id != $Dev and $data['feed'] != null && $tc == 'private'){
	$done = isset($data['text']['done'])?$data['text']['done'] : "■ پیام ارسال شد.";
	
	$get = json_decode(file_get_contents("https://api.raminsudo.ir/time.php"), true);
	$date = $get['date'];
	$today = $get['today'];
		
	$done = str_replace('F-NAME', "$first_name", $done);
	$done = str_replace('L-NAME', "$last_name", $done);
	$done = str_replace('U-NAME', "$username", $done);
	$done = str_replace('TIME', date('H:i:s'), $done);
	$done = str_replace('DATE', $date, $done);
	$done = str_replace('TODAY', $today, $done);
	if(isset($update->message->forward_from) or isset($update->message->forward_from_chat)){
		if($data['lock']['forward'] == "✅"){
			SendMessage($chat_id,"🔐 ارسال این نوع رسانه توسط ادمین محدود شده!", 'Html' ,$message_id, $button_user);
			exit();
		}
	}
	if(isset($message->text)){
		if($data['lock']['text'] != "✅"){
			$checklink = CheckLink($text);
			$checkfilter = CheckFilter($text);
			if($checklink != true){
				if($checkfilter != true){
					Forward($data['feed'], $chat_id, $message_id);
					SendMessage($chat_id,"$done", 'Html' ,$message_id, $button_user);
					if(isset($update->message->forward_from) or isset($update->message->forward_from_chat)){
						SendMessage($data['feed'],"■ ارسال کننده پیام فوق : [$from_id](tg://user?id=$from_id)", 'MarkDown');
					}
				}
			}
			if($checklink == true){
				SendMessage($chat_id,"🔐 ارسال لینک توسط ادمین محدود شده!", 'Html' ,$message_id, $button_user);
			}
			if($checkfilter == true){
				SendMessage($chat_id,"■ پیام شما به دلیل داشتن کلمه غیر مجاز ارسال نشد!", 'Html' ,$message_id, $button_user);
			}
		}else{
			SendMessage($chat_id,"🔐 ارسال این نوع رسانه توسط ادمین محدود شده!", 'Html' ,$message_id, $button_user);
		}
	}
	if(isset($message->photo)){
		if($data['lock']['photo'] != "✅"){
			Forward($data['feed'], $chat_id, $message_id);
			SendMessage($chat_id,"$done", 'Html' ,$message_id, $button_user);
			if(isset($update->message->forward_from) or isset($update->message->forward_from_chat)){
				SendMessage($data['feed'],"■ ارسال کننده پیام فوق : [$from_id](tg://user?id=$from_id)", 'MarkDown');
			}
		}else{
			SendMessage($chat_id,"🔐 ارسال این نوع رسانه توسط ادمین محدود شده!", 'Html' ,$message_id, $button_user);
		}
	}
	if(isset($message->video)){
		if($data['lock']['video'] != "✅"){
			Forward($data['feed'], $chat_id, $message_id);
			SendMessage($chat_id,"$done", 'Html' ,$message_id, $button_user);
			if(isset($update->message->forward_from) or isset($update->message->forward_from_chat)){
				SendMessage($data['feed'],"■ ارسال کننده پیام فوق : [$from_id](tg://user?id=$from_id)", 'MarkDown');
			}
		}else{
			SendMessage($chat_id,"🔐 ارسال این نوع رسانه توسط ادمین محدود شده!", 'Html' ,$message_id, $button_user);
		}
	}
	if(isset($message->voice)){
		if($data['lock']['voice'] != "✅"){
			Forward($data['feed'], $chat_id, $message_id);
			SendMessage($chat_id,"$done", 'Html' ,$message_id, $button_user);
			if(isset($update->message->forward_from) or isset($update->message->forward_from_chat)){
				SendMessage($data['feed'],"■ ارسال کننده پیام فوق : [$from_id](tg://user?id=$from_id)", 'MarkDown');
			}
		}else{
			SendMessage($chat_id,"🔐 ارسال این نوع رسانه توسط ادمین محدود شده!", 'Html' ,$message_id, $button_user);
		}
	}
	if(isset($message->audio)){
		if($data['lock']['audio'] != "✅"){
			Forward($data['feed'], $chat_id, $message_id);
			SendMessage($data['feed'],"■ ارسال کننده پیام فوق : [$from_id](tg://user?id=$from_id)", 'MarkDown');
			SendMessage($chat_id,"$done", 'Html' ,$message_id, $button_user);
		}else{
			SendMessage($chat_id,"🔐 ارسال این نوع رسانه توسط ادمین محدود شده!", 'Html' ,$message_id, $button_user);
		}
	}
	if(isset($message->sticker)){
		if($data['lock']['sticker'] != "✅"){
			Forward($data['feed'], $chat_id, $message_id);
			SendMessage($data['feed'],"■ ارسال کننده پیام فوق : [$from_id](tg://user?id=$from_id)", 'MarkDown');
			SendMessage($chat_id,"$done", 'Html' ,$message_id, $button_user);
		}else{
			SendMessage($chat_id,"🔐 ارسال این نوع رسانه توسط ادمین محدود شده!", 'Html' ,$message_id, $button_user);
		}
	}
	if(isset($message->document)){
		if($data['lock']['document'] != "✅"){
			Forward($data['feed'], $chat_id, $message_id);
			SendMessage($chat_id,"$done", 'Html' ,$message_id, $button_user);
			if(isset($update->message->forward_from) or isset($update->message->forward_from_chat)){
				SendMessage($data['feed'],"■ ارسال کننده پیام فوق : [$from_id](tg://user?id=$from_id)", 'MarkDown');
			}
		}else{
			SendMessage($chat_id,"🔐 ارسال این نوع رسانه توسط ادمین محدود شده!", 'Html' ,$message_id, $button_user);
		}
	}
}
elseif(isset($reply) and (in_array($from_id, $list['admin']) | $from_id == $Dev) and $chat_id == $data['feed']){
    if($reply_id == GetMe()->result->id)
	if(preg_match('/^\/(ban)$/i',$text)){
		if(!in_array($reply, $list['ban'])){
		    if($list['ban'] == null){
                $list['ban'] = [];
            }
			array_push($list['ban'], $reply);
			file_put_contents("data/list.json",json_encode($list));
			SendMessage($chat_id,"■ کاربر مورد نظر از ربات مسدود شد.", 'MarkDown', $message_id);
			SendMessage($reply,"■ شما از طرف مدیر مسدود شدید و پیام شما دیگر دریافت نخواهد شد.", 'MarkDown', null, $remove);
		}else{
			SendMessage($chat_id,"■ کاربر از قبل مسدود شده بود.", 'MarkDown', $message_id);
		}
	}
	elseif(preg_match('/^\/(unban)$/i',$text)){
		if(in_array($reply, $list['ban'])){
			$search = array_search($reply, $list['ban']);
			unset($list['ban'][$search]);
			$list['ban'] = array_values($list['ban']);
			file_put_contents("data/list.json",json_encode($list));
			SendMessage($chat_id,"■ کاربر مورد نظر از ربات از مسدودیت خارج شد.", 'MarkDown', $message_id);
			SendMessage($reply,"■ شما از طرف مدیر آزاد شدید و پیام شما دریافت خواهد شد.", 'MarkDown', null, $button_user);
		}else{
			SendMessage($chat_id,"■ کاربر از قبل مسدود نبود.", 'MarkDown', $message_id);
		}
	}
	elseif(preg_match('/^\/(share)$/i',$text)){
	$name = $data['contact']['name'];
	$phone = $data['contact']['phone'];
		if($phone != null and $name != null){
			SendContact($reply, $name, $phone);
			SendMessage($chat_id,"■ شماره شما با موفقیت برای کاربر به اشتراک گذاشته شد.", 'MarkDown', $message_id);
		}else{
			SendMessage($chat_id,'■ شماره شما تنظیم نشده!'.PHP_EOL.'■ برای این کار ابتدا شماره خود را از بخش "کانتکت" تنظیم کنید.', 'MarkDown', $message_id);
		}
	}
	elseif(isset($message)){
		if($text != null){
			SendMessage($reply,$text,null);
		}
		elseif($voice_id != null){
			SendVoice($reply,$voice_id,$caption);
		}
		elseif($file_id != null){
			SendDocument($reply,$file_id,$caption);
		}
		elseif($music_id != null){
			SendAudio($reply,$music_id,$caption);
		}
		elseif($photo2_id != null){
			SendPhoto($reply,$photo2_id,$caption);
		}
		elseif($photo1_id != null){
			SendPhoto($reply,$photo1_id,$caption);
		}
		elseif($photo0_id != null){
			SendPhoto($reply,$photo0_id,$caption);
		}
		elseif($video_id != null){
			SendVideo($reply,$video_id,$caption);
		}
		elseif($sticker_id != null){
			SendSticker($reply,$sticker_id);
		}
		SendMessage($chat_id,"■ _پیام مورد نظر ارسال شد_", 'MarkDown', $message_id);
	}
}
//--------[Dev]--------//
if(($from_id == $Dev | $fromid == $Dev) and ($tc == 'private' | $tccall == 'private')){
if($rankdev == 'left'){
	SendMessage($chat_id,"■ برای استفاده از ربات و همچنین حمایت از ما ابتدا وارد کانال\n● @OpenSource_IR\n■ سپس به ربات برگشته و /start را بزنید.", null, $message_id, $remove);
}
elseif($text == '▫️ برگشت ▫️' | $text == '❄️ مدیریت'){
	$data['step'] = "none";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($chat_id,"■ چه کمکی میتونم بهتون کنم قربان؟", 'MarkDown' ,$message_id, $panel);
}
elseif($text == '⏹ غیرفعال کردن حالت مدیریت' | preg_match('/^\/(start)$/i',$text)){
	foreach($data['buttons'] as $key => $name){
        $name = $data['buttons'][$key];
        $manage_off[] = [['text'=>"$name"]];
    }
    $manage_off[] = [ ['text'=>"$profile_key"] ];
    $manage_off[] = [ ['text'=>"$contact_key",'request_contact' => true],['text'=>"$location_key",'request_location' => true]];
    $manage_off[] = [ ['text'=>"❄️ مدیریت"]];
    $manage_off = json_encode(['keyboard'=> $manage_off ,'resize_keyboard'=>true]);
	SendMessage($chat_id,"■ حالت مدیریت غیرفعال گردید.", 'MarkDown' ,$message_id, $manage_off);
}
elseif(isset($reply)){
	if(preg_match('/^\/(ban)$/i',$text)){
		if(!in_array($reply, $list['ban'])){
		    if($list['ban'] == null){
                $list['ban'] = [];
            }
			array_push($list['ban'], $reply);
			file_put_contents("data/list.json",json_encode($list));
			SendMessage($chat_id,"■ کاربر مورد نظر از ربات مسدود شد.", 'MarkDown', $message_id);
			SendMessage($reply,"■ شما از طرف مدیر مسدود شدید و پیام شما دیگر دریافت نخواهد شد.", 'MarkDown', null, $remove);
		}else{
			SendMessage($chat_id,"■ کاربر از قبل مسدود شده بود.", 'MarkDown', $message_id);
		}
	}
	elseif(preg_match('/^\/(unban)$/i',$text)){
		if(in_array($reply, $list['ban'])){
			$search = array_search($reply, $list['ban']);
			unset($list['ban'][$search]);
			$list['ban'] = array_values($list['ban']);
			file_put_contents("data/list.json",json_encode($list));
			SendMessage($chat_id,"■ کاربر مورد نظر از ربات از مسدودیت خارج شد.", 'MarkDown', $message_id);
			SendMessage($reply,"■ شما از طرف مدیر آزاد شدید و پیام شما دریافت خواهد شد.", 'MarkDown', null, $button_user);
		}else{
			SendMessage($chat_id,"■ کاربر از قبل مسدود نبود.", 'MarkDown', $message_id);
		}
	}
	elseif(preg_match('/^\/(share)$/i',$text)){
	$name = $data['contact']['name'];
	$phone = $data['contact']['phone'];
		if($phone != null and $name != null){
			SendContact($reply, $name, $phone);
			SendMessage($chat_id,"■ شماره شما با موفقیت برای کاربر به اشتراک گذاشته شد.", 'MarkDown', $message_id);
		}else{
			SendMessage($chat_id,'■ شماره شما تنظیم نشده!'.PHP_EOL.'■ برای این کار ابتدا شماره خود را از بخش "کانتکت" تنظیم کنید.', 'MarkDown', $message_id);
		}
	}
	elseif(isset($message)){
		if($text != null){
			SendMessage($reply,$text,null);
		}
		elseif($voice_id != null){
			SendVoice($reply,$voice_id,$caption);
		}
		elseif($file_id != null){
			SendDocument($reply,$file_id,$caption);
		}
		elseif($music_id != null){
			SendAudio($reply,$music_id,$caption);
		}
		elseif($photo2_id != null){
			SendPhoto($reply,$photo2_id,$caption);
		}
		elseif($photo1_id != null){
			SendPhoto($reply,$photo1_id,$caption);
		}
		elseif($photo0_id != null){
			SendPhoto($reply,$photo0_id,$caption);
		}
		elseif($video_id != null){
			SendVideo($reply,$video_id,$caption);
		}
		elseif($sticker_id != null){
			SendSticker($reply,$sticker_id);
		}
		SendMessage($chat_id,"■ _پیام مورد نظر ارسال شد_", 'MarkDown', $message_id);
	}
}
elseif($text == '📊 آمار ربات'){
	$countban = count(array_unique($list['ban']));
	$countuser = 0;
	foreach(glob('data/*') as $dir){
	    if(is_dir($dir)){
	        $countuser++;
	    }
	}
	$countupdate = $data['count']['update'];
	SendMessage($chat_id,"■ تعداد کل اعضای ربات : *$countuser*\n■ تعداد افراد مسدود شده : *$countban*\n■ کل آپدیت های ارسالی از سمت تلگرام : *$countupdate*", 'MarkDown', $message_id);
}
elseif($text == '📑 لیست پاسخ ها'){
	$quick = $data['quick'];
	if($quick != null){
		$str = null;
		foreach($quick as $word => $answer){
			$str .= "کلمه ($word) | پاسخ ($answer)\n";
		}
		SendMessage($chat_id,"■ لیست پاسخ های سریع :\n$str", 'Html', $message_id);
	}else{
		SendMessage($chat_id,"■ لیست پاسخ های سریع خالی می باشد!", 'Html', $message_id);
	}
}
elseif($text == '📑 لیست فیلتر'){
	$filters = $data['filters'];
	if($filters != null){
		$im = implode(PHP_EOL, $filters);
		SendMessage($chat_id,"■ لیست کلمات فیلتر شده :\n$im", 'Html', $message_id);
	}else{
		SendMessage($chat_id,"■ لیست فیلتر خالی می باشد!", 'Html', $message_id);
	}
}
elseif($text == '🔐 قفل ها'){
	$video = $data['lock']['video'];
	$audio = $data['lock']['audio'];
	$voice = $data['lock']['voice'];
	$text = $data['lock']['text'];
	$sticker = $data['lock']['sticker'];
	$link = $data['lock']['link'];
	$photo = $data['lock']['photo'];
	$document = $data['lock']['document'];
	$forward = $data['lock']['forward'];
	$channel = $data['lock']['channel'];
	
	if($video == null){
		$data['lock']['video'] = "❌";
		file_put_contents("data/data.json",json_encode($data));
	}
	if($audio == null){
		$data['lock']['audio'] = "❌";
		file_put_contents("data/data.json",json_encode($data));
	}
	if($voice == null){
		$data['lock']['voice'] = "❌";
		file_put_contents("data/data.json",json_encode($data));
	}
	if($text == null){
		$data['lock']['text'] = "❌";
		file_put_contents("data/data.json",json_encode($data));
	}
	if($sticker == null){
		$data['lock']['sticker'] = "❌";
		file_put_contents("data/data.json",json_encode($data));
	}
	if($link == null){
		$data['lock']['link'] = "❌";
		file_put_contents("data/data.json",json_encode($data));
	}
	if($photo == null){
		$data['lock']['photo'] = "❌";
		file_put_contents("data/data.json",json_encode($data));
	}
	if($document == null){
		$data['lock']['document'] = "❌";
		file_put_contents("data/data.json",json_encode($data));
	}
	if($forward == null){
		$data['lock']['forward'] = "❌";
		file_put_contents("data/data.json",json_encode($data));
	}
	if($channel == null){
		$data['lock']['channel'] = "❌";
		file_put_contents("data/data.json",json_encode($data));
	}
	$data = json_decode(file_get_contents("data/data.json"),true);
	$video = $data['lock']['video'];
	$audio = $data['lock']['audio'];
	$voice = $data['lock']['voice'];
	$text = $data['lock']['text'];
	$sticker = $data['lock']['sticker'];
	$link = $data['lock']['link'];
	$photo = $data['lock']['photo'];
	$document = $data['lock']['document'];
	$forward = $data['lock']['forward'];
	$channel = $data['lock']['channel'];
	
	$btnstats = json_encode(['inline_keyboard'=>[
    [['text'=>"$video",'callback_data'=>"video"],['text'=>"🎥 قفل ویدیو",'callback_data'=>"view1"]],
    [['text'=>"$audio",'callback_data'=>"audio"],['text'=>"🎵 قفل موسیقی",'callback_data'=>"view2"]],
    [['text'=>"$voice",'callback_data'=>"voice"],['text'=>"🔊 قفل ویس",'callback_data'=>"view3"]],
    [['text'=>"$text",'callback_data'=>"text"],['text'=>"🏷 قفل متن",'callback_data'=>"view4"]],
    [['text'=>"$sticker",'callback_data'=>"sticker"],['text'=>"😺 قفل استیکر",'callback_data'=>"view5"]],
    [['text'=>"$link",'callback_data'=>"link"],['text'=>"🔗 قفل لینک",'callback_data'=>"view6"]],
    [['text'=>"$photo",'callback_data'=>"photo"],['text'=>"🖼 قفل عکس",'callback_data'=>"view7"]],
    [['text'=>"$document",'callback_data'=>"document"],['text'=>"🗂 قفل فایل",'callback_data'=>"view8"]],
    [['text'=>"$forward",'callback_data'=>"forward"],['text'=>"⤴️ قفل فروارد",'callback_data'=>"view9"]],
    [['text'=>"$channel",'callback_data'=>"channel"],['text'=>"📢 قفل کانال",'callback_data'=>"view10"]],
    ]]);
	SendMessage($chat_id,"■ برای قفل و یا بازکردن روی دکمه های سمت چپ کلیک کنید.همچنین میتونید مفهوم اموجی هارو در زیر مشاهده کنید.\n👈 قفل : ✅ | 👈 آزاد : ❌", 'MarkDown', $message_id, $btnstats);
}
elseif($text == '📌 وضعیت نمایش دکمه ها'){
	$profile_btn = $data['button']['profile']['stats'];
	$contact_btn = $data['button']['contact']['stats'];
	$location_btn = $data['button']['location']['stats'];
	
	if($profile_btn == null){
		$data['button']['profile']['stats'] = "✅";
		file_put_contents("data/data.json",json_encode($data));
	}
	if($contact_btn == null){
		$data['button']['contact']['stats'] = "✅";
		file_put_contents("data/data.json",json_encode($data));
	}
	if($location_btn == null){
		$data['button']['location']['stats'] = "✅";
		file_put_contents("data/data.json",json_encode($data));
	}
	$data = json_decode(file_get_contents("data/data.json"),true);
	$profile_btn = $data['button']['profile']['stats'];
	$contact_btn = $data['button']['contact']['stats'];
	$location_btn = $data['button']['location']['stats'];
	$btnstats = json_encode(['inline_keyboard'=>[
    [['text'=>"دکمه پروفایل $profile_btn",'callback_data'=>"profile"]],
    [['text'=>"دکمه ارسال شماره $contact_btn",'callback_data'=>"contact"]],
    [['text'=>"دکمه ارسال مکان $location_btn",'callback_data'=>"location"]],
    ]]);
	SendMessage($chat_id,"■ برای تغییر وضعیت هر کدام، روی آن بزنید", 'MarkDown', $message_id, $btnstats);
}
elseif(isset($Data)){
	$locks = ['video','audio','voice','text','sticker','link','photo','document','forward','channel'];
	if(in_array($Data, $locks)){
		$media = $data['lock'][$Data];
		if($media == "❌"){
			$data['lock'][$Data] = "✅";
			file_put_contents("data/data.json",json_encode($data));
		}else{
			if($media == "✅"){
				$data['lock'][$Data] = "❌";
				file_put_contents("data/data.json",json_encode($data));
			}
		}
	$data = json_decode(file_get_contents("data/data.json"),true);
	$video = $data['lock']['video'];
	$audio = $data['lock']['audio'];
	$voice = $data['lock']['voice'];
	$text = $data['lock']['text'];
	$sticker = $data['lock']['sticker'];
	$link = $data['lock']['link'];
	$photo = $data['lock']['photo'];
	$document = $data['lock']['document'];
	$forward = $data['lock']['forward'];
	$channel = $data['lock']['channel'];
	$btnstats = json_encode(['inline_keyboard'=>[
    [['text'=>"$video",'callback_data'=>"video"],['text'=>"🎥 قفل ویدیو",'callback_data'=>"view1"]],
    [['text'=>"$audio",'callback_data'=>"audio"],['text'=>"🎵 قفل موسیقی",'callback_data'=>"view2"]],
    [['text'=>"$voice",'callback_data'=>"voice"],['text'=>"🔊 قفل ویس",'callback_data'=>"view3"]],
    [['text'=>"$text",'callback_data'=>"text"],['text'=>"🏷 قفل متن",'callback_data'=>"view4"]],
    [['text'=>"$sticker",'callback_data'=>"sticker"],['text'=>"😺 قفل استیکر",'callback_data'=>"view5"]],
    [['text'=>"$link",'callback_data'=>"link"],['text'=>"🔗 قفل لینک",'callback_data'=>"view6"]],
    [['text'=>"$photo",'callback_data'=>"photo"],['text'=>"🖼 قفل عکس",'callback_data'=>"view7"]],
    [['text'=>"$document",'callback_data'=>"document"],['text'=>"🗂 قفل فایل",'callback_data'=>"view8"]],
    [['text'=>"$forward",'callback_data'=>"forward"],['text'=>"⤴️ قفل فروارد",'callback_data'=>"view9"]],
    [['text'=>"$channel",'callback_data'=>"channel"],['text'=>"📢 قفل کانال",'callback_data'=>"view10"]],
    ]]);
	AnswerCallbackQuery($data_id,"■ انجام شد.");
	EditKeyboard($chatid, $messageid, $btnstats);
	}
	elseif($Data == "profile" | $Data == "contact" | $Data == "location"){
		$btn = $data['button'][$Data]['stats'];
		
		if($btn == "⛔️"){
			$data['button'][$Data]['stats'] = "✅";
			file_put_contents("data/data.json",json_encode($data));
		}else{
			if($btn == "✅"){
				$data['button'][$Data]['stats'] = "⛔️";
				file_put_contents("data/data.json",json_encode($data));
			}
		}
		
		$profile_btn = $data['button']['profile']['stats'];
		$contact_btn = $data['button']['contact']['stats'];
		$location_btn = $data['button']['location']['stats'];
		$btnstats = json_encode(['inline_keyboard'=>[
		[['text'=>"دکمه پروفایل $profile_btn",'callback_data'=>"profile"]],
		[['text'=>"دکمه ارسال شماره $contact_btn",'callback_data'=>"contact"]],
		[['text'=>"دکمه ارسال مکان $location_btn",'callback_data'=>"location"]],
		]]);
		AnswerCallbackQuery($data_id,"■ انجام شد.");
		EditKeyboard($chatid, $messageid, $btnstats);
	}
}
elseif($text == '📕 راهنما'){
	SendMessage($chat_id,"▪️*/Ban* مسدود کاربر (ریپلی)\n ▪️*/UnBan* (ریپلی) رفع مسدود کاربر\n\n▪️ */Ban* (آیدی) مسدود کاربر\n▪️ */UnBan* (آیدی) رفع مسدود کاربر\n\n▪️ */Share* (ریپلی) اشتراک گذاری شماره\n*----------------------------------*\n👈 همواره شما می توانید در پیغام های 'شروع' و 'پیشفرض' و 'پاسخ های سریع' خود از متغیر های زیر استفاده نمایید تا مقدار های اصلی آن ها در مطلب نمایش داده شود :\n▫️ `F-NAME` | نام شخص\n▫️ `L-NAME` | نام خانوادگی شخص\n▫️ `U-NAME` | نام کاربری شخص بدون @\n▫️ `TIME` | ساعت ایران\n▫️ `DATE` | تاریخ ایران\n▫️ `TODAY` | روز هفته\n*----------------------------------*\n● */SetFeed* تنظیم گروه پشتیبانی\n•ربات رو به گروه مورد نظر اد کرده و این دستور در گروه مورد نظر ارسال گردد.\n\n● */DelFeed* حذف گروه پشتیبانی و ارسال پیام ها به پیوی\n• این دستور در پیوی ربات ارسال گردد.", 'MarkDown', $message_id);
}
elseif($text == 'سایر تنظیمات🛠'){
	SendMessage($chat_id,"به بخش تنظیمات خوش آمدید.", 'MarkDown',$message_id,$tanzhadi);
}
elseif($text == '📢 آپدیت های اخیر'){
	$file = new CURLFile("data/updates.txt");
	SendDocument($chat_id, $file, "■ آپدیت های اخیر ارسالی از سمت تلگرام");
}
elseif($text == '▪️ کاربران اخیر'){
	$count = count($list['user'])-12;
	$lastmem = null;
	foreach($list['user'] as $key => $value){
		if($count <= $key){
			$lastmem .= "[$value](tg://user?id=$value) | ";
			$key++;
		}
	}
	SendMessage($chat_id,"■ لیست *12* کاربر اخیر ربات به شرح ذیل می باشد :\n$lastmem", 'MarkDown', $message_id);
}
elseif($text == '👥 لیست ادمین ها'){
    if(isset($list['admin'])){
	    $count = count($list['admin']);
	    $lastmem = null;
	    foreach($list['admin'] as $key => $value){
	    		$lastmem .= "[$value](tg://user?id=$value)\n";
	    }
	    SendMessage($chat_id,"■ لیست کامل ادمین های ربات به شرح ذیل می باشد :\n$lastmem", 'MarkDown', $message_id);
    }else{
        SendMessage($chat_id,"■ لیست ادمین ها خالی می باشد!", 'MarkDown', $message_id);
    }
}
elseif($text == '🔘 بهینه سازی'){
	unset($data['id']);
	unset($data['word']);
	unset($data['step']);
	unlink("data/updates.txt");
	unlink("data/flood.json");
	array_map('unlink', glob('backup*'));
	file_put_contents("data/data.json",json_encode($data));
	
	SendMessage($chat_id,"■ بهینه سازی با موفقیت انجام شد.", 'MarkDown', $message_id);
}
elseif($text == '🗄 پشتیبان گیری'){
	SendMessage($chat_id,"■ نسخه پشتیبان درحال آماده سازی است.\n■ منتظر بمانید ...", 'MarkDown', $message_id);
	
	copy('data/list.json','list.json');
	copy('data/data.json','data.json');
	$file_to_zip = array('list.json','data.json');
	$create = CreateZip($file_to_zip, "backup.zip");
	$zipfile = new CURLFile("backup.zip");
	$time = date('H:i:s');
	SendDocument($chat_id, $zipfile, "■ آخرین نسخه پشتیبان\n> ساعت : $time");
	unlink('list.json');
	unlink('data.json');
	unlink('backup.zip');
	unlink('updates.txt');
}
elseif($text == '✉️ پیغام ها' | $text == '↩️ برگشت'){
	$data['step'] = "none";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($chat_id,"■ به بخش تنظیم و مشاهده پیغام ها خوش آمدید", 'MarkDown', $message_id, $peygham);
}
elseif($text == '📯 فیلتر کلمه' | $text == '↩️  برگشـت'){
	$data['step'] = "none";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($chat_id,"■ به بخش فیلتر کلمات خوش آمدید", 'MarkDown', $message_id, $button_filter);
}
elseif($text == '📍 پاسخ سریع' | $text == '↩️ برگشت '){
	$data['step'] = "none";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($chat_id,"■ به بخش پاسخ سریع خوش آمدید", 'MarkDown',$message_id, $quick);
}
elseif($text == '⚗ دکمه ها' | $text == '↩️ بازگشت'){
	$data['step'] = "none";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($chat_id,"■ به بخش تنظیم و مشاهده وضعیت دکمه ها آمدید", 'MarkDown', $message_id, $button);
}
elseif($text == '💡 جعبه ابزار' | $text == '↩️ برگشت به جعبه ابزار'){
	$data['step'] = "none";
	unset($data['translate']);
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($chat_id,"■ به جعبه ابزار خوش آمدید\n■ این بخش تنها جهت سرگرمی شما طراحی شده !", 'MarkDown', $message_id, $button_tools);
}
elseif($text == '👨🏻‍💻 ادمین ها' | $text == '↩️ برگشت به منوی ادمین ها'){
	$data['step'] = "none";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($chat_id,"■ به منوی تنظیم ادمین ها خوش آمدید\n■ این بخش برای افراد مجاز پاسخگویی در گروه پشتیبانی طراحی شده !", 'MarkDown', $message_id, $button_admins);
}
elseif($text == '⚜ نام دکمه های سیستمی'){
	SendMessage($chat_id,"■ دکمه ای را برای تغییر نام انتخاب کنید", 'MarkDown', $message_id, $button_name);
}
elseif($text == 'دکمه پروفایل' || $text == 'دکمه ارسال شماره' || $text == 'دکمه ارسال مکان'){
	$fa = array ('دکمه پروفایل','دکمه ارسال شماره','دکمه ارسال مکان');
	$en = array ('profile','contact','location');
	$str = str_replace($fa, $en, $text);
	if($str == 'profile'){
	    if($data['button'][$str]['name'] == null){
		    $btnname = "🎫 پروفایل";
	    }else{
		    $btnname = $data['button'][$str]['name'];
	    }
	}
	if($str == 'contact'){
	    if($data['button'][$str]['name'] == null){
		    $btnname = "ارسال شماره شما 📞";
	    }else{
		    $btnname = $data['button'][$str]['name'];
	    }
	}
	if($str == 'location'){
	    if($data['button'][$str]['name'] == null){
		    $btnname = "⚓️ ارسال مکان شما";
	    }else{
		    $btnname = $data['button'][$str]['name'];
	    }
	}
	$data['step'] = "btn$str";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($chat_id,"■ شما هم اکنون درحال تغییر نام ( $text ) هستید!\n■ نام فعلی دکمه : ( $btnname )", null, $message_id, $backbtn);
}
elseif($text == '⚜ کانتکت'){
	SendMessage($chat_id,"■ به بخش تنظیم و مشاهده کانتکت (شماره) خوش آمدید", 'MarkDown', $message_id, $contact);
}
elseif($text == '📞 شماره من'){
	$name = $data['contact']['name'];
	$phone = $data['contact']['phone'];
	if($phone != null and $name != null){
		SendMessage($chat_id,"■ شماره تنظیم شده فعلی :", 'MarkDown', $message_id, $contact);
		SendContact($chat_id, $name, $phone);
	}else{
		SendMessage($chat_id,'■ تا کنون شماره ای تنظیم نشده!'.PHP_EOL.'■ شما هم اکنون می توانید توسط دکمه "تنظیم شماره" شماره خود را تنظیم نمائید و به کاربر توسط دستور *share/* با ریپلی ارسال کنید', 'MarkDown', $message_id, $contact);
	}
}
elseif(isset($update->message->contact) and $data['step'] == "none" | $data['step'] == null){
	$name_contact = $update->message->contact->first_name;
	$number_contact = $update->message->contact->phone_number;
	
	$data['contact']['name'] = "$name_contact";
	$data['contact']['phone'] = "+$number_contact";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($chat_id,"■ _شماره ارسالی_ :\n*+$number_contact*\n■ با موفقیت تنظیم گردید.", 'MarkDown', $message_id, $contact);
}
elseif($text == '💮 ریست ربات'){
	$data['step'] = "reset";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($chat_id,"■ با انجام این عمل کلیه سیستم ربات از جمله : اعضای ربات و تمامی داده های تنظیمی توسط شما از بین خواهند رفت!\n■ اگر اطمینان دارید دکمه زیر را لمس کنید", 'MarkDown', $message_id, $reset);
}
elseif($text == 'بله، مطمئن هستم' and $data['step'] == "reset"){
	DeleteFolder("data");
	mkdir("data");
	SendMessage($chat_id,"■ با موفقیت ریست شد.", 'MarkDown', $message_id, $panel);
}
elseif($text == '✅ روشن کردن ربات'){
	$data['stats'] = "on";
	file_put_contents("data/data.json",json_encode($data));
	$panel = json_encode(['keyboard'=>[
[['text'=>"📕 راهنما"],['text'=>"📊 آمار ربات"]],
[['text'=>"📬 ارسال همگانی"],['text'=>"📮 فروارد همگانی"]],
[['text'=>"📍 پاسخ سریع"],['text'=>"✉️ پیغام ها"]],
[['text'=>"✏️ پیام به کاربر"],['text'=>"⚗ دکمه ها"]],
[['text'=>"❎ خاموش کردن ربات"]],
[['text'=>"سایر تنظیمات🛠"]],
[['text'=>"⏹ غیرفعال کردن حالت مدیریت"]]
    ],'resize_keyboard'=>true]);
	SendMessage($chat_id,"■ ربات با موفقیت روشن شد و پیام کاربران دریافت خواهد شد.", 'MarkDown', $message_id, $panel);
}
elseif($text == '❎ خاموش کردن ربات'){
	$data['stats'] = "off";
	file_put_contents("data/data.json",json_encode($data));
	$panel = json_encode(['keyboard'=>[
    [['text'=>"📕 راهنما"],['text'=>"📊 آمار ربات"]],
[['text'=>"📬 ارسال همگانی"],['text'=>"📮 فروارد همگانی"]],
[['text'=>"📍 پاسخ سریع"],['text'=>"✉️ پیغام ها"]],
[['text'=>"✏️ پیام به کاربر"],['text'=>"⚗ دکمه ها"]],
[['text'=>"✅ روشن کردن ربات"]],
[['text'=>"سایر تنظیمات🛠"]],
[['text'=>"⏹ غیرفعال کردن حالت مدیریت"]]
    ],'resize_keyboard'=>true]);
	SendMessage($chat_id,"■ ربات با موفقیت خاموش شد و پیام کاربران دریافت نخواهد شد.", 'MarkDown', $message_id, $panel);
}
//------------------------------------------------------------------------------
elseif($text == '🎐 عکس به استیکر'){
	$data['step'] = "tosticker";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($chat_id,"■ عکس مورد نظر را جهت تبدیل به استیکر ارسال کنید", 'MarkDown', $message_id, $backto);
}
elseif($text == '🖼 استیکر به عکس'){
	$data['step'] = "tophoto";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($chat_id,"■ استیکر مورد نظر را جهت تبدیل شدن به عکس ارسال کنید", 'MarkDown', $message_id, $backto);
}
elseif($text == '😬 جوک'){
	$jock = file_get_contents("https://api.bot-dev.org/jock/");
	SendMessage($chat_id,"$jock", null, $message_id, $button_tools);
}
elseif($text == '🙏🏻 فال حافظ'){
	$pic = "http://www.beytoote.com/images/Hafez/".rand(1,149).".gif";
	SendPhoto($chat_id,$pic,"■ با ذکر صلوات و فاحته ای جهت شادی روح 'حافظ شیرازی' فال تان را بخوانید.");
}
elseif($text == '🔍 ویکی پدیا'){
	$data['step'] = "wikipedia";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($chat_id,"■ موضوع مورد نظر را در قالب کلمه ارسال کنید", 'MarkDown', $message_id, $backto);
}
elseif($text == '🌐 مترجم'){
	$data['step'] = "translate";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($chat_id,"■ متن مورد نظر را برای ترجمه ارسال کنید", 'MarkDown', $message_id, $backto);
}
elseif($text == '🎪 عکس تصادفی'){
	$get = file_get_contents("http://api.mostafa-am.ir/fun-photo/");
	$result = json_decode($get, true);
	SendPhoto($chat_id,$result['animals'],"■ موضوع : حیوانات");
	sleep(1);
	SendPhoto($chat_id,$result['cars'],"■ موضوع : ماشین ها");
	sleep(1);
	SendPhoto($chat_id,$result['nature'],"■ موضوع : طبیعت");
	sleep(1);
	SendPhoto($chat_id,$result['textgraphy'],"■ موضوع : عکس نوشته");
}
elseif($text == '🖊 طراحی متن'){
	$data['step'] = "write";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($chat_id,"■ متن مورد نظر را برای فونت دهی در قالب انگلیسی ارسال کنید", 'MarkDown', $message_id, $backto);
}
//------------------------------------------------------------------------------
elseif($text == '🔹 متن شروع'){
	$data['step'] = "setstart";
	file_put_contents("data/data.json",json_encode($data));
	$start = $data['text']['start'];
	if($data['text']['start'] != null){
		$start = $data['text']['start'];
    }else{
    	$start = "■ سلام، به پیام رسان من خوش آمدید\nپیام خود را ارسال کنید :";
    }
	SendMessage($chat_id,"■ پیغام فعلی شروع :\n<code>$start</code>\n■ جهت تغییر ، متن جدید را ارسال کنید.", 'Html', $message_id, json_encode(['keyboard'=>[ [['text'=>"↩️ برگشت"]] ],'resize_keyboard'=>true]));
}
elseif($text == '🔸 متن پیش فرض'){
	$data['step'] = "setdone";
	file_put_contents("data/data.json",json_encode($data));
	if($data['text']['done'] != null){
		$done = $data['text']['done'];
    }else{
    	$done = "■ پیام ارسال شد.";
    }
	SendMessage($chat_id,"■ پیغام فعلی پیش فرض :\n<code>$done</code>\n■ جهت تغییر ، متن جدید را ارسال کنید.", 'Html', $message_id, json_encode(['keyboard'=>[ [['text'=>"↩️ برگشت"]] ],'resize_keyboard'=>true]));
}
elseif($text == '🎟 متن پروفایل'){
	$data['step'] = "setprofile";
	file_put_contents("data/data.json",json_encode($data));
	if($data['text']['profile'] != null){
		$profile = $data['text']['profile'];
    }else{
    	$profile = "■ پروفایل خالی است!";
    }
	SendMessage($chat_id,"■ پیغام فعلی پروفایل :\n<code>$profile</code>\n■ جهت تغییر ، متن جدید را ارسال کنید.", 'Html', $message_id, json_encode(['keyboard'=>[[['text'=>"حذف پروفایل"]],[['text'=>"↩️ برگشت"]]],'resize_keyboard'=>true]));
}
elseif($text == '✏️ پیام به کاربر'){
	$data['step'] = "user";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($chat_id,"■ آیدی عددی فرد مربوطه را ارسال کرده یا پیامی از شخص فروارد کنید", 'MarkDown', $message_id, $back);
}
elseif($text == '➕ افزودن کلمه'){
	$data['step'] = "addword";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($chat_id,"■ کلمه ای که می خواهید به آن پاسخ داده شود را ارسال کنید", 'MarkDown', $message_id, $backans);
}
elseif($text == '➖ حذف کلمه'){
	$data['step'] = "delword";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($chat_id,"■ کلمه ای که می خواهید حذف شود را ارسال کنید", 'MarkDown', $message_id, $backans);
}
elseif($text == '➕ افزودن فیلتر'){
	$data['step'] = "addfilter";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($chat_id,"■ کلمه ای که می خواهید فیلتر شود را ارسال کنید", 'MarkDown', $message_id, json_encode(['keyboard'=>[ [['text'=>"↩️  برگشـت"]] ],'resize_keyboard'=>true]));
}
elseif($text == '➖ حذف فیلتر'){
	$data['step'] = "delfilter";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($chat_id,"■ کلمه ای که می خواهید از لیست فیلتر حذف شود را ارسال کنید", 'MarkDown', $message_id, json_encode(['keyboard'=>[ [['text'=>"↩️  برگشـت"]] ],'resize_keyboard'=>true]));
}
elseif($text == '➕ افزودن ادمین'){
	$data['step'] = "addadmin";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($chat_id,"■ آیدی عددی فرد را ارسال کنید یا پیامی از شخص فروارد کنید", 'MarkDown', $message_id, json_encode(['keyboard'=>[ [['text'=>"↩️ برگشت به منوی ادمین ها"]] ],'resize_keyboard'=>true]));
}
elseif($text == '➖ حذف ادمین'){
	$data['step'] = "deladmin";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($chat_id,"■ آیدی عددی فرد را ارسال کنید یا پیامی از شخص فروارد کنید", 'MarkDown', $message_id, json_encode(['keyboard'=>[ [['text'=>"↩️ برگشت به منوی ادمین ها"]] ],'resize_keyboard'=>true]));
}
elseif($text == '➕ افزودن دکمه'){
	$data['step'] = "addbutton";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($chat_id,"■ نام دکمه ای که میخواهید اضافه شود را ارسال کنید", 'MarkDown', $message_id, $backbtn);
}
elseif($text == '➖ حذف دکمه'){
	$data['step'] = "delbutton";
	file_put_contents("data/data.json",json_encode($data));
	if($data['buttons'] != null){
	    foreach($data['buttons'] as $key => $name){
            $name = $data['buttons'][$key];
            $delbuttons[] = [['text'=>"$name"]];
        }
        $delbuttons[] = [ ['text'=>"↩️ بازگشت"] ];
        $delbuttons = json_encode(['keyboard'=> $delbuttons ,'resize_keyboard'=>true]);
	    SendMessage($chat_id,"■ قصد حذف کدام دکمه را دارید ؟", 'MarkDown', $message_id, $delbuttons);
	}else{
	    SendMessage($chat_id,"■ شما هنوز هیچ دکمه ای اضافه نکرده اید!", 'MarkDown', $message_id, $button);
	}
}
elseif($text == '📤 آپلودر'){
	$data['step'] = "upload";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($chat_id,"■ رسانه مورد نظر را برای آپلود ارسال کنید", 'MarkDown', $message_id, $back);
}
elseif($text == '📥 دانلودر'){
	$data['step'] = "download";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($chat_id,"■ لینک مستقیم یا لینکی از اینستاگرام ارسال کنید تا برای شما ارسال شود", 'MarkDown', $message_id, $back);
}
elseif($text == '📣 قفل کانال'){
	$data['step'] = "setchannel";
	file_put_contents("data/data.json",json_encode($data));
	$ch = $data['channel']? $data['channel'] : "تنظیم نشده !";
	SendMessage($chat_id,"■ پیامی از کانال مورد نظر فروارد کنید.\n■ می توانید آیدی کانال را هم به طور دستی وارد کنید\n📢 کانال فعلی : $ch", 'Html', $message_id, $back);
}
elseif($text == '🀄️ اطلاعات کاربر'){
	$data['step'] = "userinfo";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($chat_id,"■ آیدی عددی کاربر مورد نظر را ارسال کنید", 'MarkDown', $message_id, $back);
}
elseif($text == '📬 ارسال همگانی'){
    $data['step'] = "s2all";
	file_put_contents("data/data.json",json_encode($data));
    SendMessage($chat_id,"■ پیام مورد نظر را ارسال کنید", 'MarkDown', $message_id, $back);
}
elseif($text == '📮 فروارد همگانی'){
    $data['step'] = "f2all";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($chat_id,"■ پیام مورد نظر را فروارد کنید", 'MarkDown', $message_id, $back);
}
//------------------------------------------------------------------------------
elseif($data['step'] == "tosticker" and isset($message->photo)){
	$data['step'] = "none";
	file_put_contents("data/data.json",json_encode($data));
	$photo = $message->photo;
	$file = $photo[count($photo)-1]->file_id;
    $get = Bot('getFile',['file_id'=> $file]);
    $patch = $get->result->file_path;
    file_put_contents("data/sticker.webp",fopen('https://api.telegram.org/file/bot'.API_KEY.'/'.$patch, 'r'));
	SendSticker($chat_id,new CURLFile("data/sticker.webp"));
	unlink("data/sticker.webp");
	SendMessage($chat_id,"■ منوی جعبه ابزار :", 'MarkDown', $message_id, $button_tools);
}
elseif($data['step'] == "tophoto" and isset($message->sticker)){
	$data['step'] = "none";
	file_put_contents("data/data.json",json_encode($data));
	$file = $message->sticker->file_id;
    $get = Bot('getFile',['file_id'=> $file]);
    $patch = $get->result->file_path;
    file_put_contents("data/photo.png",fopen('https://api.telegram.org/file/bot'.API_KEY.'/'.$patch, 'r'));
	SendPhoto($chat_id,new CURLFile("data/photo.png"));
	unlink("data/photo.png");
	SendMessage($chat_id,"■ منوی جعبه ابزار :", 'MarkDown', $message_id, $button_tools);
}
elseif($data['step'] == "wikipedia" and isset($text)){
	$data['step'] = "none";
	file_put_contents("data/data.json",json_encode($data));
	$get = file_get_contents("http://api.mostafa-am.ir/wikipedia-search/".urlencode($text));
	$result = json_decode($get, true);
	$wiki = $result['2']['0'];
	$link = $result['3']['0'];
	SendMessage($chat_id,"■ توضیحات :\n$wiki\n\n■ مشاهده کامل جزئیات در سایت رسمی ویکی پدیا :\n".urldecode($link), null, $message_id, $button_tools);
}
elseif($data['step'] == "translate" and isset($text)){
	$data['step'] = "translate0";
	$data['translate'] = "$text";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($chat_id,"■ به چه زبانی ترجمه شود ؟", 'MarkDown', $message_id, $languages);
}
elseif($data['step'] == "translate0"){
	$langs = ["🇮🇷 فارسی","🇵🇳 انگلیسی","🇸🇦 عربی","🇷🇺 روسی","🇫🇷 فرانسوی","🇹🇷 ترکی"];
	if(in_array($text, $langs)){
		$langs = ["🇮🇷 فارسی","🇵🇳 انگلیسی","🇸🇦 عربی","🇷🇺 روسی","🇫🇷 فرانسوی","🇹🇷 ترکی"];
		$langs_a = ["fa","en","ar","ru","fr","tr"];
		$lan = str_replace($langs, $langs_a, $text);
		$get = file_get_contents("http://api.mostafa-am.ir/translate-txt/".$lan.'/'.urlencode($data['translate']));
		$result = json_decode($get, true);
		if($result['ok'] == true){
			SendMessage($chat_id,$result['text']['0'], null, $message_id);
		}else{
			SendMessage($chat_id,"■ خطایی در پردازش متن ارسالی شما یافت شد!", null, $message_id);
		}
	}
}
elseif($data['step'] == "write" and isset($text)){
	if(strlen($text) == mb_strlen($text, 'utf-8')){
		$data['step'] = "none";
		file_put_contents("data/data.json",json_encode($data));
		$matn = urlencode($text);
		$get = file_get_contents("https://api.raminsudo.ir/font/?text=$matn");
		$result = json_decode($get, true);
		$font1 = $result['result']['1'];
		$font2 = $result['result']['2'];
		$font3 = $result['result']['3'];
		$font4 = $result['result']['4'];
		$font5 = $result['result']['5'];
		$font6 = $result['result']['6'];
		$font7 = $result['result']['7'];
		$font8 = $result['result']['8'];
		SendMessage($chat_id,"■ کلمه اصلی ($text) :\n\n● `$font1`\n● `$font2`\n● `$font3`\n● `$font4`\n● `$font5`\n● `$font6`\n● `$font7`\n● `$font8`", 'MarkDown', $message_id, $button_tools);
	}else{
		SendMessage($chat_id,"■ تنها متن انگلیسی قابل قبول است!", 'MarkDown', $message_id);
	}
}
//------------------------------------------------------------------------------
elseif($data['step'] == "setstart" and isset($text)){
	$data['step'] = "none";
	$data['text']['start'] = "$text";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($chat_id,"■ ثبت شد", 'MarkDown', $message_id, $peygham);
}
elseif($data['step'] == "setdone" and isset($text)){
	$data['step'] = "none";
	$data['text']['done'] = "$text";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($chat_id,"■ ثبت شد", 'MarkDown', $message_id, $peygham);
}
elseif($data['step'] == "setprofile" and isset($text)){
	$data['step'] = "none";
	if($text != 'حذف پروفایل'){
		$data['text']['profile'] = "$text";
		SendMessage($chat_id,"■ ثبت شد", 'MarkDown', $message_id, $peygham);
	}else{
		unset($data['text']['profile']);
		SendMessage($chat_id,"■ دکمه پروفایل خالی را نمایش خواهد داد.", 'MarkDown', $message_id, $peygham);
	}
	file_put_contents("data/data.json",json_encode($data));
}
elseif($data['step'] == "user"){
	if(isset($forward)){
		$get = file_get_contents("https://api.telegram.org/bot".API_KEY."/getChat?chat_id=".$forward_id);
		$result = json_decode($get, true);
		$ok = $result['ok'];
		if($ok == true){
			$data['step'] = "msg";
			$data['id'] = "$forward_id";
			file_put_contents("data/data.json",json_encode($data));
			SendMessage($chat_id,"■ پیام مورد نظر را در هر قالبی ارسال کنید", 'MarkDown', $message_id, $back);
		}else{
			SendMessage($chat_id,"■ خطا ، کاربر عضو ربات نیست و نمی تواند ربات به او پیام دهد!", 'MarkDown', $message_id, $panel);
		}
	}else{
		$get = file_get_contents("https://api.telegram.org/bot".API_KEY."/getChat?chat_id=".$text);
		$result = json_decode($get, true);
		$ok = $result['ok'];
		
		if($ok == true){
			$data['id'] = "$text";
			$data['step'] = "msg";
			file_put_contents("data/data.json",json_encode($data));
			SendMessage($chat_id,"■ پیام مورد نظر را در هر قالبی ارسال کنید", 'MarkDown', $message_id, $back);
		}else{
			SendMessage($chat_id,"■ خطا ، کاربر یافت نشد!", 'MarkDown', $message_id, $panel);
		}
	}
}
elseif($data['step'] == "msg"){
	$id = $data['id'];
	
	if($forward_from != null){
		Forward($id,$chat_id,$message_id);
	}
	elseif($video_id != null){
		SendVideo($id,$video_id,$caption);
	}
	elseif($voice_id != null){
		SendVoice($id,$voice_id,$caption);
	}
	elseif($file_id != null){
		SendDocument($id,$file_id,$caption);
	}
	elseif($music_id != null){
		SendAudio($id,$music_id,$caption);
	}
	elseif($photo2_id != null){
		SendPhoto($id,$photo2_id,$caption);
	}
	elseif($photo1_id != null){
		SendPhoto($id,$photo1_id,$caption);
	}
	elseif($photo0_id != null){
		SendPhoto($id,$photo0_id,$caption);
	}
	elseif($text != null){
		SendMessage($id, $text, null);
	}
	elseif($sticker_id != null){
		SendSticker($id,$sticker_id);
	}
	
	$data['step'] = "none";
	unset($data['id']);
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($chat_id,"■ پیام شما با موفقیت به کاربر ارسال شد.", null, $message_id, $panel);
}
elseif($data['step'] == "addword" and isset($text)){
	$data['step'] = "ans";
	SendMessage($chat_id,"■ پاسخی که باید برای ( $text ) ارسال کنم را ارسال کنید", null, $message_id, $backans);
	$data['word'] = "$text";
	$data['quick'][$text] = null;
	file_put_contents("data/data.json",json_encode($data));
}
elseif($data['step'] == "ans" and isset($text)){
	$word = $data['word'];
	$data['step'] = "none";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($chat_id,"> کلمه : ( $word )\n> پاسخ : ( $text )\n■ ثبت شد.", null, $message_id, $quick);
	$data['quick'][$word] = "$text";
	unset($data['word']);
	file_put_contents("data/data.json",json_encode($data));
}
elseif($data['step'] == "delword" and isset($text)){
	if($data['quick'][$text] != null){
		SendMessage($chat_id,"■ کلمه ( $text ) از لیست پاسخ سریع حذف گردید.", null, $message_id, $quick);
		$data['step'] = "none";
		unset($data['quick'][$text]);
		file_put_contents("data/data.json",json_encode($data));
	}else{
		SendMessage($chat_id,"■ خطا، کلمه ارسالی شما یافت نشد!\n■ لطفا مجدد با دقت بیشتری ارسال کنید", 'MarkDown', $message_id);
	}
}
elseif($data['step'] == "addfilter" and isset($text)){
	if(!in_array($text, $data['filters'])){
		$data['step'] = "none";
		SendMessage($chat_id,"■ کلمه : ( $text ) با موفقیت فیلتر شد.", null, $message_id, $button_filter);
		$data['filters'][] = "$text";
		file_put_contents("data/data.json",json_encode($data));
	}else{
		SendMessage($chat_id,"■ کلمه : ( $text ) از قبل فیلتر بود!", null, $message_id);
	}
}
elseif($data['step'] == "delfilter" and isset($text)){
	if(in_array($text, $data['filters'])){
		SendMessage($chat_id,"■ کلمه ( $text ) از لیست پاسخ سریع حذف گردید.", null, $message_id, $button_filter);
		$data['step'] = "none";
		$search = array_search($text, $data['filters']);
		unset($data['filters'][$search]);
		$data['filters'] = array_values($data['filters']);
		file_put_contents("data/data.json",json_encode($data));
	}else{
		SendMessage($chat_id,"■ خطا، کلمه ارسالی شما در لیست فیلتر یافت نشد!\n■ لطفا مجدد با دقت بیشتری ارسال کنید", 'MarkDown', $message_id);
	}
}
elseif($data['step'] == "addadmin"){
    if(is_numeric($text) == true){
	    $get = file_get_contents("https://api.telegram.org/bot".API_KEY."/getChat?chat_id=".$text);
    	$result = json_decode($get, true);
	    $ok = $result['ok'];
	    if($ok == true){
	        if(!in_array($text, $list['admin'])){
	            if($list['admin'] == null){
                    $list['admin'] = [];
                }
	    	    array_push($list['admin'], $text);
	    	    file_put_contents("data/list.json",json_encode($list));
	    	    $data['step'] = "none";
	    	    $mention = "<a href='tg://user?id=$text'>".GetChat($text)->result->first_name."</a>";
	    	    SendMessage($chat_id,"■ کاربر ($mention) در ربات ادمین شد.", 'Html', $message_id, $button_admins);
	    	    SendMessage($text,"■ شما در ربات ادمین شدید و از الان مجاز به پاسخگویی در گروه پشتیبانی هستید.", 'MarkDown', null);
	        }else{
	            $data['step'] = "none";
	            $mention = "<a href='tg://user?id=$text'>".GetChat($text)->result->first_name."</a>";
	            SendMessage($chat_id,"■ کاربر ($mention) از قبل ادمین بود!", 'Html', $message_id, $button_admins);
	        }
	    }else{
	    	SendMessage($chat_id,"■ کاربری با آیدی *".$text."* در ربات یافت نشد!", 'MarkDown', $message_id);
	    }
	    file_put_contents("data/data.json",json_encode($data));
    }
    elseif(isset($forward)){
		$get = file_get_contents("https://api.telegram.org/bot".API_KEY."/getChat?chat_id=".$forward_id);
		$result = json_decode($get, true);
		$ok = $result['ok'];
		if($ok == true){
			if(!in_array($forward_id, $list['admin'])){
	            if($list['admin'] == null){
                    $list['admin'] = [];
                }
	    	    array_push($list['admin'], $forward_id);
	    	    file_put_contents("data/list.json",json_encode($list));
	    	    $data['step'] = "none";
	    	    $mention = "<a href='tg://user?id=$forward_id'>".GetChat($forward_id)->result->first_name."</a>";
	    	    SendMessage($chat_id,"■ کاربر ($mention) در ربات ادمین شد.", 'Html', $message_id, $button_admins);
	    	    SendMessage($forward_id,"■ شما در ربات ادمین شدید و از الان مجاز به پاسخگویی در گروه پشتیبانی هستید.", 'MarkDown', null);
	        }else{
	            $data['step'] = "none";
	            $mention = "<a href='tg://user?id=$forward_id'>".GetChat($forward_id)->result->first_name."</a>";
	            SendMessage($chat_id,"■ کاربر ($mention) از قبل ادمین بود!", 'Html', $message_id, $button_admins);
	        }
		}else{
			SendMessage($chat_id,"■ کاربری با آیدی *".$forward_id."* در ربات یافت نشد!", 'MarkDown', $message_id);
		}
		file_put_contents("data/data.json",json_encode($data));
	}
}
elseif($data['step'] == "deladmin"){
    if(is_numeric($text) == true){
	    $get = file_get_contents("https://api.telegram.org/bot".API_KEY."/getChat?chat_id=".$text);
    	$result = json_decode($get, true);
	    $ok = $result['ok'];
	    if($ok == true){
	        if(in_array($text, $list['admin'])){
	            $search = array_search($text, $list['admin']);
		        unset($list['admin'][$search]);
		        $list['admin'] = array_values($list['admin']);
		        file_put_contents("data/list.json",json_encode($data));
	    	    $data['step'] = "none";
	    	    $mention = "<a href='tg://user?id=$text'>".GetChat($text)->result->first_name."</a>";
	    	    SendMessage($chat_id,"■ کاربر ($mention) از ادمینی برکنار شد.", 'Html', $message_id, $button_admins);
	    	    SendMessage($text,"■ شما از ادمین بودن در ربات برکنار شدید و دیگر مجاز به پاسخگویی در گروه پشتیبانی نیستید.", 'MarkDown', null);
	        }else{
	            $data['step'] = "none";
	            $mention = "<a href='tg://user?id=$text'>".GetChat($text)->result->first_name."</a>";
	            SendMessage($chat_id,"■ کاربر ($mention) از قبل ادمین نبود!", 'Html', $message_id, $button_admins);
	        }
	    }else{
	    	SendMessage($chat_id,"■ کاربری با آیدی *".$text."* در ربات یافت نشد!", 'MarkDown', $message_id);
	    }
	    file_put_contents("data/data.json",json_encode($data));
    }
    elseif(isset($forward)){
		$get = file_get_contents("https://api.telegram.org/bot".API_KEY."/getChat?chat_id=".$forward_id);
		$result = json_decode($get, true);
		$ok = $result['ok'];
		if($ok == true){
			if(in_array($forward_id, $list['admin'])){
	            $search = array_search($forward_id, $list['admin']);
		        unset($list['admin'][$search]);
		        $list['admin'] = array_values($list['admin']);
		        file_put_contents("data/list.json",json_encode($data));
	    	    $data['step'] = "none";
	    	    $mention = "<a href='tg://user?id=$forward_id'>".GetChat($forward_id)->result->first_name."</a>";
	    	    SendMessage($chat_id,"■ کاربر ($mention) از ادمینی برکنار شد.", 'Html', $message_id, $button_admins);
	    	    SendMessage($forward_id,"■ شما از ادمین بودن در ربات برکنار شدید و دیگر مجاز به پاسخگویی در گروه پشتیبانی نیستید.", 'MarkDown', null);
	        }else{
	            $data['step'] = "none";
	            $mention = "<a href='tg://user?id=$forward_id'>".GetChat($forward_id)->result->first_name."</a>";
	            SendMessage($chat_id,"■ کاربر ($mention) از قبل ادمین نبود!", 'Html', $message_id, $button_admins);
	        }
		}else{
			SendMessage($chat_id,"■ کاربری با آیدی *".$forward_id."* در ربات یافت نشد!", 'MarkDown', $message_id);
		}
		file_put_contents("data/data.json",json_encode($data));
	}
}
elseif($data['step'] == "addbutton" and isset($text)){
	$data['step'] = "ansbtn|$text";
	SendMessage($chat_id,"■ مطلب دکمه ( $text ) را ارسال کنید", null, $message_id, $backbtn);
	$data['buttons'][] = "$text";
	file_put_contents("data/data.json",json_encode($data));
}
elseif(strpos($data['step'], "ansbtn") !== false and isset($text)){
	$nambtn = str_replace("ansbtn|",null,$data['step']);
	$data['step'] = "none";
	SendMessage($chat_id,"■ مطلب ( $text ) برای دکمه ( $nambtn ) ثبت شد.", null, $message_id, $button);
	$data['buttonans'][$nambtn] = "$text";
	file_put_contents("data/data.json",json_encode($data));
}
elseif($data['step'] == "delbutton" and isset($text)){
	if(in_array($text, $data['buttons'])){
		SendMessage($chat_id,"■ دکمه ( $text ) از لیست دکمه ها حذف گردید.", null, $message_id, $button);
		$data['step'] = "none";
		$search = array_search($text, $data['buttons']);
		unset($data['buttons'][$search]);
		unset($data['buttonans'][$text]);
		$data['buttons'] = array_values($data['buttons']);
		file_put_contents("data/data.json",json_encode($data));
	}else{
		SendMessage($chat_id,"■ خطا، دکمه ارسالی شما در لیست دکمه ها یافت نشد!\n■ لطفا مجدد با دقت بیشتری ارسال کنید", 'MarkDown', $message_id);
	}
}
elseif($data['step'] == "upload" and isset($message)){
	$data['step'] = "none";
	file_put_contents("data/data.json",json_encode($data));
	
	if($sticker_id != null){
		$file = $sticker_id;
	}
	elseif($video_id != null){
		$file = $video_id;
	}
	elseif($voice_id != null){
		$file = $voice_id;
	}
	elseif($file_id != null){
		$file = $file_id;
	}
	elseif($music_id != null){
		$file = $music_id;
	}
	elseif($photo2_id != null){
		$file = $photo2_id;
	}
	elseif($photo1_id != null){
		$file = $photo1_id;
	}
	elseif($photo0_id != null){
		$file = $photo0_id;
	}
	
	$get = Bot('getFile',['file_id'=> $file]);
    $patch = $get->result->file_path;
	$file_link = 'https://api.telegram.org/file/bot'.API_KEY.'/'.$patch;
	$short = json_decode(file_get_contents("http://api.beyond-dev.ir/shortLink?url=$file_link"), true);
	$google = $short['results']['google'];
	SendMessage($chat_id,"■ با موفقیت آپلود شد.\n■ لینک کوتاه شده : $google", null, $message_id, $panel);
}
elseif($data['step'] == "download" and isset($text)){
	$data['step'] = "none";
	file_put_contents("data/data.json",json_encode($data));
	$file = pathinfo($text);
	$format = $file['extension'];
	
	$get = json_decode(file_get_contents("https://api.feelthecode.xyz/instagram/?url=$text"), true);
	
	if(isset($format) or $get['ok'] == true){
		if($format == 'mp4'){
			SendVideo($chat_id,$text);
		}
		elseif($format == 'webp'){
			SendSticker($chat_id,$text);
		}
		elseif($format == 'ogg'){
			SendVoice($chat_id,$text);
		}
		elseif($format == 'zip' || $format == 'pdf' || $format == 'gif' || $format == 'apk'){
			SendDocument($chat_id,$text);
		}
		elseif($format == 'mp3'){
			SendAudio($chat_id,$text);
		}
		elseif($format == 'png' || $format == 'jpg'){
			SendPhoto($chat_id,$text);
		}
		elseif($get['is_photo'] == true){	
			SendPhoto($chat_id,$get['url']);
		}
		elseif($get['is_video'] == true){
			SendVideo($chat_id,$get['url']);
		}
	}
}
elseif(strpos($data['step'], "btn") !== false){
	$nambtn = str_replace("btn",'',$data['step']);
	$data['step'] = "none";
	
	$en = array ('profile','contact','location');
	$fa = array ('دکمه پروفایل','دکمه ارسال شماره','دکمه ارسال مکان');
	$str = str_replace($en, $fa, $nambtn);
	SendMessage($chat_id,"■ نام ( $str ) با نام ( $text ) ثبت گردید.", null, $message_id, $button_name);
	$data['button'][$nambtn]['name'] = "$text";
	file_put_contents("data/data.json",json_encode($data));
}
elseif($data['step'] == "setchannel"){
	$GetMe = GetMe();
	$id = $GetMe->result->id;
	if(isset($message->forward_from_chat->username)){
		$get = Bot('GetChatMember',[
		'chat_id'=>"@".$message->forward_from_chat->username,
		'user_id'=>$id]);
		if($get->result->status == 'administrator'){
			SendMessage($chat_id,"■ کانال @".$message->forward_from_chat->username." ثبت گردید.", 'Html', $message_id, $panel);
			$data['step'] = "none";
			$data['channel'] = "@".$message->forward_from_chat->username;
			file_put_contents("data/data.json",json_encode($data));
		}else{
			SendMessage($chat_id,"■ ابتدا ربات را در کانال مربوطه ادمین کنید سپس اقدام به تنظیم کانال کنید.", 'MarkDown', $message_id);
		}
	}
	elseif(preg_match('/^(@)(\S{5,32})/i', $text)){
		$get = Bot('GetChatMember',[
		'chat_id'=>$text,
		'user_id'=>$id]);
		if($get->result->status == 'administrator'){
			SendMessage($chat_id,"■ کانال ".$text." ثبت گردید.", 'Html', $message_id, $panel);
			$data['step'] = "none";
			$data['channel'] = "$text";
			file_put_contents("data/data.json",json_encode($data));
		}else{
			SendMessage($chat_id,"■ ابتدا ربات را در کانال مربوطه ادمین کنید سپس اقدام به تنظیم کانال کنید.", 'MarkDown', $message_id);
		}
	}
	if(isset($message->forward_from_chat) and $message->forward_from_chat->username == null){
		SendMessage($chat_id,"■ کانال حتما باید عمومی باشد!", 'MarkDown', $message_id);
	}
}
elseif($data['step'] == "userinfo" and is_numeric($text) == true){
	$data['step'] = "none";
	file_put_contents("data/data.json",json_encode($data));
	
	$get = file_get_contents("https://api.telegram.org/bot".API_KEY."/getChat?chat_id=".$text);
	$result = json_decode($get, true);
	$ok = $result['ok'];
	if($ok == true){
		$mention = "<a href='tg://user?id=$text'>$text</a>";
		$f_name = $result['result']['first_name'];
		if($result['result']['last_name'] != null){
			$l_name = $result['result']['last_name'];
		}else{
			$l_name = "ندارد!";
		}
		if($result['result']['username'] != null){
			$username = "@".$result['result']['username'];
		}else{
			$username = "ندارد!";
		}
		$profile = GetProfile($text);
		if($profile != null){
			SendPhoto($chat_id,$profile,"■ عکس پروفایل کاربر خاطی");
		}
		SendMessage($chat_id,"■ اطلاعات کاربری <b>$text</b> :\n\n• منشن کاربر : [ $mention ]\n• نام کاربر : [ $f_name ]\n• نام خانوادگی کاربر : [ $l_name ]\n• آیدی کاربر : [ $username ]", 'Html', $message_id, $panel);
	}else{
		SendMessage($chat_id,"■ کاربری با آیدی *$text* در ربات یافت نشد!", 'MarkDown', $message_id, $panel);
	}
}
elseif($data['step'] == "s2all" and isset($message)){
    $count = 0;
	foreach(glob('data/*') as $dir){
	    if(is_dir($dir)){
	        $count++;
	    }
	}
	$sec = $count*2;
	$min = round($sec/60);
	SendMessage($chat_id,"■ پیام در صف قرار گرفت، نتیجه ارسال اطلاع داده خواهد شد.\n■ زمان تقریبی ارسال : $sec ثانیه یا تقریبا $min دقیقه", 'MarkDown', $message_id, $panel);
    $data['step'] = "none";
	file_put_contents("data/data.json",json_encode($data));
	if($text != null){
	    foreach(glob('data/*') as $dir){
	        if(is_dir($dir)){
	            $id = pathinfo($dir)['filename'];
	            SendMessage($id, $text, null, null, $button_user);
	            sleep(2);
	        }
	    }
	}
	elseif($video_id != null){
	    foreach(glob('data/*') as $dir){
	        if(is_dir($dir)){
	            $id = pathinfo($dir)['filename'];
	            SendVideo($id,$video_id,$caption);
	            sleep(2);
	        }
	    }
	}
	elseif($voice_id != null){
	    foreach(glob('data/*') as $dir){
	        if(is_dir($dir)){
	            $id = pathinfo($dir)['filename'];
	            SendVoice($id,$voice_id,$caption);
	            sleep(2);
	        }
	    }
	}
	elseif($file_id != null){
	    foreach(glob('data/*') as $dir){
	        if(is_dir($dir)){
	            $id = pathinfo($dir)['filename'];
	            SendDocument($id,$file_id,$caption);
	            sleep(2);
	        }
	    }
	}
	elseif($music_id != null){
	    foreach(glob('data/*') as $dir){
	        if(is_dir($dir)){
	            $id = pathinfo($dir)['filename'];
	            SendAudio($id,$music_id,$caption);
	            sleep(2);
	        }
	    }
	}
	elseif($photo2_id != null){
	    foreach(glob('data/*') as $dir){
	        if(is_dir($dir)){
	            $id = pathinfo($dir)['filename'];
	            SendPhoto($id,$photo2_id,$caption);
	            sleep(2);
	        }
	    }
	}
	elseif($photo1_id != null){
	    foreach(glob('data/*') as $dir){
	        if(is_dir($dir)){
	            $id = pathinfo($dir)['filename'];
	            SendPhoto($id,$photo1_id,$caption);
	            sleep(2);
	        }
	    }
	}
	elseif($photo0_id != null){
	    foreach(glob('data/*') as $dir){
	        if(is_dir($dir)){
	            $id = pathinfo($dir)['filename'];
	            SendPhoto($id,$photo0_id,$caption);
	            sleep(2);
	        }
	    }
	}
	elseif($sticker_id != null){
	    foreach(glob('data/*') as $dir){
	        if(is_dir($dir)){
	            $id = pathinfo($dir)['filename'];
	            SendSticker($id,$sticker_id);
	            sleep(2);
	        }
	    }
	}
	SendMessage($chat_id,"■ پیام به تمامی اعضا ارسال شد", 'MarkDown', null, $panel);
}
elseif($data['step'] == "f2all" and isset($message)){
    $count = 0;
	foreach(glob('data/*') as $dir){
	    if(is_dir($dir)){
	        $count++;
	    }
	}
	$sec = $count*2;
	$min = round($sec/60);
	SendMessage($chat_id,"■ پیام در صف قرار گرفت، نتیجه ارسال اطلاع داده خواهد شد.\n■ زمان تقریبی فروارد : $sec ثانیه یا تقریبا $min دقیقه", 'MarkDown', $message_id, $panel);
    $data['step'] = "none";
	file_put_contents("data/data.json",json_encode($data));
	foreach(glob('data/*') as $dir){
	    if(is_dir($dir)){
            $id = pathinfo($dir)['filename'];
            Forward($id, $chat_id, $message_id);
            sleep(2);
        }
    }
	SendMessage($chat_id,"■ پیام به تمامی اعضا فروارد شد", 'MarkDown', null, $panel);
}
//------------------------------------------------------------------------------
elseif(preg_match('/^\/(ban) ([0-9]+)/i',$text)){
	preg_match('/^\/(ban) ([0-9]+)/i',$text,$match);
	$get = file_get_contents("https://api.telegram.org/bot".API_KEY."/getChat?chat_id=".$match[2]);
	$result = json_decode($get, true);
	$ok = $result['ok'];
	if($ok == true){
	    if(in_array($match[2], $list['ban'])){
	        if($list['ban'] == null){
                $list['ban'] = [];
            }
		    array_push($list['ban'], $match[2]);
		    file_put_contents("data/list.json",json_encode($list));
		    SendMessage($chat_id,"■ کاربر *".$match[2]."* از ربات مسدود گردید.", 'MarkDown', $message_id);
		    SendMessage($match[2],"■ شما از طرف مدیر مسدود شدید و پیام شما دیگر دریافت نخواهد شد.", 'MarkDown', null, $remove);
	    }else{
	        SendMessage($chat_id,"■ کاربری با آیدی *".$match[2]."* از قبل در لیست مسدودیت بود!", 'MarkDown', $message_id);
	    }
	}else{
		SendMessage($chat_id,"■ کاربری با آیدی *".$match[2]."* در ربات یافت نشد!", 'MarkDown', $message_id);
	}
}
elseif(preg_match('/^\/(unban) ([0-9]+)/i',$text)){
	preg_match('/^\/(unban) ([0-9]+)/i',$text,$match);
	if(in_array($match[2], $list['ban'])){
		$search = array_search($match[2], $list['ban']);
		unset($list['ban'][$search]);
		$list['ban'] = array_values($list['ban']);
		file_put_contents("data/list.json",json_encode($list));
		SendMessage($chat_id,"■ کاربر *".$match[2]."* از مسدودیت آزاد گردید.", 'MarkDown', $message_id);
		SendMessage($match[2],"■ شما از طرف مدیر آزاد شدید و پیام شما دریافت خواهد شد.", 'MarkDown', null, $button_user);
	}else{
		SendMessage($chat_id,"■ کاربری با آیدی *".$match[2]."* در لیست مسدودیت وجود نداشت!", 'MarkDown', $message_id);
	}
}
//------------------------------------------------------------------------------
} //--------[End of Dev]--------// 
//------------------------------------------------------------------------------
elseif(isset($update->inline_query->query)){
$GetChat = GetChat($Dev);
$DevName = $GetChat->result->first_name;
$GetMe = GetMe();
$BotID = $GetMe->result->username;
   bot('AnswerInlineQuery',[
        'inline_query_id'=> $update->inline_query->id,
        'results'=> json_encode([[
            'type'=> 'article',
            'thumb_url'=> "https://www.digikala.com/mag/wp-content/uploads/2017/12/Samsung-Email-App.jpg",
            'id'=> base64_encode(rand(5,5555)),
            'title'=> "■ اینجا را کلیک کنید ■",
            'input_message_content'=>[
            'parse_mode'=> 'MarkDown',
            'message_text'=> "■ ربات پیام رسان [$DevName](tg://user?id=$Dev)\nشما از طریق این ربات می توانید با من در ارتباط باشید و ریپورت بودن شما در این حالت بلا مانع است.\nبرای هدایت شدن به این ربات پیام رسان از دکمه شیشه ای ذیل استفاده نمایید!"],
            'reply_markup'=> [
            'inline_keyboard'=> [	[['text' => "رفتن به پیام رسان من", 'url' => "https://telegram.me/$BotID"]]	]
            ]
        ]])
    ]);
}
//------------------------------------------------------------------------------
//--------[Auto Configer via Get]--------//
if(!is_dir('data')){
	mkdir('data');
	$txt = "*Bot is Running ... | Click* /start";
	file_get_contents("https://api.telegram.org/bot".API_KEY."/SendMessage?chat_id=".$Dev."&text=".$txt."&parse_mode=MarkDown");
	$WebHook = file_get_contents("https://api.telegram.org/bot".API_KEY."/SetWebHook?url=".$_SERVER['SERVER_NAME']."".$_SERVER['SCRIPT_NAME']);
	$result = json_decode($WebHook, true);
	$ok = $result['ok'];
	if($ok == true){
		header('Content-type: application/json');
		SendMessage($Dev,"■ ربات شما با موفقیت اجرا شد.\n■ هم اکنون روی /start بزنید :)", 'Html');
		$array = ['ok'=>'true','Description'=>'Bot is Running ...'];
		$json = json_encode($array, JSON_PRETTY_PRINT);
		Echo $json;
	}else{
		header('Content-type: application/json');
		$array = ['ok'=>'false','Description'=>'Failed To Run :('];
		$json = json_encode($array, JSON_PRETTY_PRINT);
		Echo $json;
	}
	exit();
}
//------------------------------------------------------------------------------
if(!in_array($from_id, $list['user']) and !is_null($from_id)){
    mkdir("data/$from_id");
    if($list['user'] == null){
    $list['user'] = [];
    }
    array_push($list['user'], $from_id);
    file_put_contents("data/list.json",json_encode($list));
}
//------------------------------------------------------------------------------
if(isset($update)){
	$up = $data['count']['update'];
	$data['count']['update'] = $up + 1;
	file_put_contents("data/data.json",json_encode($data));
	#Save Update to History
	$up_date = file_get_contents('php://input');
	$dir = 'data/updates.txt';
	file_put_contents($dir,$up_date."\n------------------------\n",FILE_APPEND);
}
//------------------------------------------------------------------------------
unlink('error_log');
?>
