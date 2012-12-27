<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);



include 'fullcourt.php';


$phoneNum = strip_tags(htmlspecialchars($_POST['phone_number'], ENT_QUOTES, 'UTF-8'));

//ユーザー名と電話番号が入力されているか判断
if($phoneNum <> ""){

	$message = userGenerateToken($phoneNum);

}

//SMS送信
function userGenerateToken($phoneNum){

    //送信者の設定
    $accountsid = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";//Account SID
    $authtoken = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";//AuthToken
    $fromNumber ="090-8098-xxxx";//送信者

	//SMS送信のインスタンス
    $client = new RestAPI($accountsid, $authtoken);
	
	//送信内容
    $content = "パスワードを発行したよ ☆（ゝω・）つ";
    
    //送信する際に必要なデータ
    $params = array(
          'To' => $phoneNum,//宛先
          'From' => $fromNumber,//送信者
          'Body' => $content//送信内容
    );
    
    //送信
	$message = $client->send_message($params);
    
    
	return $message;
}


print "<pre>";
print_r($message);
print "</pre>";
	
?>