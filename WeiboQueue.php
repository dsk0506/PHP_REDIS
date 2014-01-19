<?php
/**
 * @date 2014-1-14 
 * @author 郑印
 * @email zhengyin.name@gmail.com
 * 这是一个在后台运行的PHP脚本，它的主要工作是把存储在Redis中的微博信息写入进mysql
 * 并处理其中的用户关系
 */
include 'Config/dbConfig.php';
class WeiboQueue
{
	public static $redis;
	public static $queueNum =0;
	private static $link;
	private static $weiboPrepare;
	private static $userPrepare;
	private static $messagePrepare;
	const REDIS_HOST = '127.0.0.1';
	const REDIS_PROT = 6379;
	
	/**
	 * 初始化运行
	 * 在这个方法中，完成Redis连接,mysql 持久连接
	 * 并执行了方法 WeiboQueue::setPrepareSql() 设置程序运行中需要的sql预准备语句
	 * 最后调用 WeiboQueue::saveWeibo() 来存储微博
	 */
	public static function initRun()
	{
		self::$redis =  new Redis();
		self::$redis->pconnect(self::REDIS_HOST,self::REDIS_PROT);
		//当队列中没有数据时，不做处理
		if(self::$redis->lSize('weiboList')<=0) return;
		self::$link = new  mysqli(DB_HOST,DB_USER,DB_PASS,DB_DATABASE);
		if(self::$link->connect_errno) exit(self::$link->connect_error);
		self::$link->query('SET NAMES '.DB_CHARSET);
		self::setPrepareSql();
		self::saveWeibo();
	}
	/**
	 * 设置预准备sql语句
	 * self::$weiboPrepare 存储微博语句
	 * self::$userPrepare 查询用户uid语句,在获取用户@信息时使用
	 * self::$messagePrepare 存储用户消息语句
	 */
	private static function setPrepareSql()
	{
		self::$weiboPrepare = self::$link->prepare('INSERT INTO weibo(`uid`,`content`,`addtime`) VALUES(?,?,?)');
		self::$userPrepare = self::$link->prepare('SELECT uid FROM user WHERE uname=?');
		self::$messagePrepare = self::$link->prepare('INSERT INTO message(`uid`,`to_uid`,`wid`) VALUES(?,?,?)');
	}
	/**
	 * 存储微博消息
	 */
	private static function saveWeibo()
	{
		while (self::$redis->lSize('weiboList')>0){
			//获得队列中最早的微博
			$json = self::$redis->rPop('weiboList');
			//转换为数组
			$weiboData = json_decode($json,true);
			//绑定预准备参数
			self::$weiboPrepare->bind_param('iss',$weiboData['uid'],$weiboData['content'],$weiboData['addtime']);
			self::$weiboPrepare->execute();
			//新增微博id
			$wid = self::$weiboPrepare->insert_id;
			//存储最近发布微博
			self::newestWeibo($wid);
			//处理用户关系
			self::disposeAtUsers($wid,$weiboData['uid'],$weiboData['content']);
			self::$queueNum ++;
		}
	}
	/**
	 * 使用Redis存储最新发布的5000条微博id
	 * @param unknown $wid
	 */
	private static function newestWeibo($wid)
	{
		//压入
		self::$redis->lPush('newestWeibos',$wid);
		//截取最近5000条
		self::$redis->ltrim('newestWeibos',0,4999);
	}
	
	
	/**
	 * 处理微博中用户关系
	 * @param int $wid	微博id
	 * @param int $uid  用户id
	 * @param string $content 微博内容
	 * 1.程序首先通过正则，查找出微博中所有 @的用户
	 * 2.使用一个循环处理这些用户
	 * 3.在循环过程中完成，取得@用户的uid，并组合数据写入到用户消息表中,并把@用户的微博，进行缓存
	 */
	private static function disposeAtUsers($wid,$uid,$content)
	{
		//匹配@用户
		$atPreg = '/@([\x{4e00}-\x{9fa5}\w]{1,19})\s{1,255}/us';	
		preg_match_all($atPreg,$content,$users);
		//循环所匹配出的用户
		foreach ($users[1] as $k=>$user)
		{
			//检查用户uid在，Redis中是否存在，存在读取否则查询mysql取得用户uid并缓存进Redis
			$toUid = self::$redis->get($user);
			if(!$toUid){
				self::$userPrepare->bind_param('s',$users[1][$k]);
				self::$userPrepare->execute();
				self::$userPrepare->bind_result($toUid);
				if(!self::$userPrepare->fetch()) continue;
				self::$redis->set($user,$toUid);
				//释放结果集,同时避免mysql[mysqli Commands out of sync; you can't run this command now]错误发生
				self::$userPrepare->free_result();
			}
			//组合消息，并写入数据库
			$message = array();
			$message['uid'] = $uid;
			$message['to_uid'] = $toUid;
			$message['wid'] = $wid;
			//把@用户的微博压入Redis缓存
			self::$redis->lPush('atMe'.$toUid,$wid);
			self::$messagePrepare->bind_param('iii',$message['uid'],$message['to_uid'],$message['wid']);
			self::$messagePrepare->execute();
		}
	}
	
}
	
	
$stime = microtime(true);

WeiboQueue::initRun();

$exeTime = microtime(true)-$stime;

WeiboQueue::$redis->lPush('monitorMsg','处理队列:'.WeiboQueue::$queueNum.'次,执行时间:'.round($exeTime,4)."秒。");
WeiboQueue::$redis->ltrim('monitorMsg',0,9);