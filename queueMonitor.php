

<?php 
	$redis = new Redis();
	$redis->connect('127.0.0.1',6379);
	$info = $redis->lrange('monitorMsg',0,9);
	rsort($info);
?>
<ol>
<?php
	foreach ($info as $v):
?>
<li><?php echo $v;?></li>	 
<?php endforeach;?>
</ol>