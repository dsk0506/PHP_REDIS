<?php
/**
 * @date 2014-1-13 
 * @author zhengyin
 * @email zhengyin.name@gmail.com
 * 
 */
class View
{
	private $vars = array();
	
	/**
	 * 渲染视图
	 * @param  $fileName
	 */
	public function render($fileName,$data=array())
	{
		if(!empty($data)) extract($data);
		include 'View/'.$fileName;
	}
}