<?php
/**
 * @date 2014-1-13 
 * @author 郑印
 * @email zhengyin.name@gmail.com
 * 这是一个用于发布测试数据的文件没有什么特别的地方，随机读取用户，向weibo.class.php ,post一定量的数据
 */


include 'Config/dbConfig.php';
define('__APP__','http://'.$_SERVER['HTTP_HOST'].str_replace('\\','/',$_SERVER['SCRIPT_NAME']));
define('__ROOT__', dirname(__APP__));
function send()
{
	$link = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_DATABASE);
	$link->query('SET NAMES '.DB_CHARSET);
	$result = $link->query('SELECT uid,uname FROM user order by rand() limit 20');
	
	$user = array();
	while ($row = $result->fetch_assoc()){
		$user[] = $row;
	}
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HEADER,0);
	curl_setopt($ch, CURLOPT_POST,1);
	curl_setopt($ch, CURLOPT_URL,__ROOT__.'/index.php?method=add');
	
	foreach ($user as $v){
		$atNum = rand(0,3);
		$atStr = '';
		//随机产生@用户
		if($atNum){
			for($i=0;$i<$atNum;$i++){
				$atStr .='@'.$user[rand(0,9)]['uname']." ";
			}
		}
		$data = array(
				'uid'=>$v['uid'], 
				'uname'=>$v['uname'],
				'content'=>'Hi,'.$atStr.' my name is '.$v['uname']
		);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_exec($ch);
	}
}

$sendNum = isset($_POST['sendNum'])?(int)$_POST['sendNum']:0;
if($sendNum == 0 || $sendNum>5) exit('request Error!');
$stime = microtime(true);
for($i=0;$i<$sendNum;$i++){
	send();
}
$exeTime = microtime(true)-$stime;
echo round($exeTime,4).'秒!';