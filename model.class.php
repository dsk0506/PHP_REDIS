<?php
/**
 * @date 2014-1-15 
 * @author zhengyin
 * @email zhengyin.name@gmail.com
 * 数据库操作模型
 */
include 'Config/dbConfig.php';
class Model
{
	static $link=null;
	
	/**
	 * 查询微博信息
	 * @param int $wids
	 * @return array
	 */
	public static function fetchWeiBo($wids)
	{
		$in = implode(',',$wids);
		$sql = 'SELECT wid,u.uid,content,addtime,uname FROM weibo AS w JOIN user as u ON w.uid=u.uid WHERE wid IN('.$in.') ORDER BY w.wid DESC';
		self::getLink()->query('SET NAMES UTF8');
		$result = self::getLink()->query($sql);
		$data =array();
		while($row=$result->fetch_assoc()){
			$data[] = $row;
		}
		$result->free_result();
		return $data;
	}
	/**
	 * 获取用户信息
	 * @param int $uid   用户id
	 */
	public static function fetchUser($uid)
	{
		$sql = 'SELECT uid,uname FROM user WHERE uid='.$uid;
		$result = self::getLink()->query($sql);
		
		return $result->fetch_assoc();
	}
	/**
	 * 获取数据库连接
	 * @return mysqli
	 */
	private function getLink()
	{
		if(self::$link === null){
			self::$link = new  mysqli(DB_HOST,DB_USER,DB_PASS,DB_DATABASE);
			if(self::$link->connect_errno) exit(self::$link->connect_error);
			self::$link->query('SET NAMES '.DB_CHARSET);
		}
		return self::$link;
	}
	
}
