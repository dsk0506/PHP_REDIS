<?php
/**
 * @date 2014-1-13 
 * @author 郑印
 * @email zhengyin.name@gmail.com
 *  
 */
class Weibo
{
	private $view;
	private $redis;
	const PAGE_REQUEST_NUM=10;	//分页请求的数据条数
	public function __construct()
	{
		include 'view.class.php';
		include 'model.class.php';
		$this->view = new View();
		$this->redis = new Redis();
		$this->redis->connect('127.0.0.1',6379);
	}
	/**
	 * 首页
	 * 显示最近微博
	 * 当请求为异步时，返回异步数据
	 * 随机分配一个用户会话
	 * 并设置当前会话用户的缓存的微博id，提供给异步数据分页是使用
	 */
	public function index()
	{
		$wids = $this->redis->lrange('newestWeibos',0,self::PAGE_REQUEST_NUM-1);
		$data = $this->getWeiboToWids($wids);
		//随机分配一个用户会话
		$this->randAssignUser();
		//压入缓存的微博id
		$_SESSION['temp_wids'] = $this->redis->lrange('newestWeibos',0,100);
		if($this->isAjax())
			$this->ajaxResult($data);
		$this->view->render('index.phtml',array('data'=>$data));
	}
	/**
	 * ajax请求异步数据分页 [前台查看更多异步调用]
	 * 接受前台数据start，标示从那个记录段开始请求。
	 * 从当前会话用户的session信息中，读取缓存微博id，
	 * 这样做是为了，在请求时按照发布顺序依次展示
	 */
	public function ajaxPage()
	{
		if(!$this->isAjax()) exit('request Error!');
		$start = isset($_POST['start'])?(int)$_POST['start']:self::PAGE_REQUEST_NUM;
		$end = $start+self::PAGE_REQUEST_NUM-1;
		$wids = array_slice($_SESSION['temp_wids'],$start,self::PAGE_REQUEST_NUM);
		$data = $this->getWeiboToWids($wids);
		$this->ajaxResult(array('start'=>$end+1,'data'=>$data));
	}
	
	/**
	 * 添加微博
	 * 把数据转换为json，暂存进redis,避免产生过多的mysql进程
	 * 后续工作交由,msgQueue.php处理
	 */
	public function add()
	{
		$data = array();
		$data['uid'] = $_POST['uid'];
		$data['uname'] = $_POST['uname'];
		$data['content'] = $_POST['content'];
		$data['addtime'] = date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']);
		$this->redis->lPush('weiboList',json_encode($data));
		$this->redis->close();
	}
	/**
	 * 异步请求@我的 微博处理
	 * 首先判断是否为异步请求
	 * 然后从redis中读取 @用户的微博id，在取得微博id以后查询出微博数据返回
	 */
	public function atMe()
	{
		if(!$this->isAjax()) exit('request Error!');
		$wids = array();
		$uid = $_SESSION['user']['uid'];
		$key = 'atMe'.$uid;
		while ($this->redis->lSize($key)>0){
			$wid = $this->redis->lPop($key);
			array_push($wids,(int)$wid);
		}
		$data = $this->getWeiboToWids($wids);
		$this->ajaxResult($data);
	}
	/**
	 * 通过微博id获取微博
	 * @param array $wids
	 */
	private function getWeiboToWids($wids)
	{
		$data = array();
		if(!empty($wids)){
			$data = Model::fetchWeiBo($wids);
		}
		return $data;
	}
	
	/**
	 * 返回异步结果
	 * @param array() $data
	 */
	private function ajaxResult($data)
	{
		if(empty($data)){
			echo(json_encode(array('status'=>false,'msg'=>'data is empty!')));
		}else{
			echo(json_encode(array('status'=>true,'data'=>$data)));
		}
		exit;
	}
	
	/**
	 * 检查是否为异步请求
	 * @return boolean
	 */
	private function isAjax()
	{
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
			return true;
		return false;
	}
	/**
	 * 随机分配一个当前会话用户
	 * 如果用户会话信息已经存在，不分配，
	 * 不存在时，随机查询出一个用户信息
	 */
	private function randAssignUser()
	{
		if(isset($_SESSION['user'])) return;
		$uid = rand(7,24);
		$_SESSION['user'] = Model::fetchUser($uid);
	}
}
