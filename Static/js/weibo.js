//查看更多请求微博数据
function requestWeibo()
{
	if(this.ajaxStatus) return false;
	var data = this.iStart?'start='+this.iStart:'';
	var url = __JSAPP__+'?method=ajaxPage';
	var _this = this;
	$.ajax({
		url:url,
		type:'POST',
		data:data,
		dataType:'json',
		beforeSend:function()
		{
			_this.ajaxStatus = true;
			$(_this).find('img').show()
		},
		success:function(result)
		{
			if(result.status === true)
			{
				var data = result.data.data;
				var start = result.data.start;
				var weiboContent = $('#weibo-content');
				for(var i in data){
					weiboContent.append('<dl><dt>'+data[i].uname+'</dt><dd>'+data[i].content+'</dd><dd>'+data[i].addtime+'<em>wid:'+data[i].wid+'</em></dd></dl>');
				}
				_this.iStart = start;
			}else{
				$('#bottom').html(result.msg)
			}
		},
		complete:function()
		{
			_this.ajaxStatus = false;
			$(_this).find('img').hide()
		}
	})
}

//ajax 轮询请求@我的微博
ajaxInquiry();
function ajaxInquiry(){
	var url = __JSAPP__+'?method=atMe';
	$.ajax({
		url:url,
		dataType:'json',
		success:function(result){
			var atmeWeibo = $('#atme-weibo');
			if(result.status === true)
			{
				var data = result.data;
				for(var i in data){
					atmeWeibo.append('<dl><dt>'+data[i].uname+'</dt><dd>'+data[i].content+'</dd><dd>'+data[i].addtime+'</dd></dl>');
				}
				
			}
		},
		complete:function(){
			setTimeout(function(){
				ajaxInquiry();
			},10000)
		}
	})
}
//获取最近微博
function getNewestWeibo()
{
	var url = __JSAPP__;
	var _this = this;
	$.ajax({
		url:url,
		dataType:'json',
		beforeSend:function()
		{
			$(_this).hide();
			$(_this).siblings('.loading-span').show();
		},
		success:function(result)
		{
			
			if(result.status === true)
			{
				var data = result.data;
				var weiboContent = $('#weibo-content');
				$('#weibo-content').find('dl').remove();
				for(var i in data){
					weiboContent.append('<dl><dt>'+data[i].uname+'</dt><dd>'+data[i].content+'</dd><dd>'+data[i].addtime+'<em>wid:'+data[i].wid+'</em></dd></dl>');
				}
			}else{
				$('#bottom').html(result.msg)
			}
		},
		complete:function()
		{
			$(_this).show();
			$(_this).siblings('.loading-span').hide();
		}
	});	
	
}
//发生数据表单提交
$('#sendForm').submit(function(){
	var url = __JSROOT__+'/send.php';
	var oSubmitBtn = $('#sendSubmit');
	var data = $(this).serialize();
	
	$.ajax({
		url:url,
		data:data,
		type:'POST',
		beforeSend:function()
		{
			oSubmitBtn.val('正在发送 ..').addClass('disabled');
		},
		success:function(result){
			$('#send-time').html(result);
		},
		complete:function(){
			oSubmitBtn.val('Send ').removeClass('disabled');
		}
	})
	return false;
})

//读取队列监控信息
readQueueMonitor()
function readQueueMonitor()
{
	var url = __JSROOT__+'/queueMonitor.php';
	$.ajax({
		url:url,
		dataType:'html',
		success:function(result){
			$('#queue-box').html(result);
		},
		complete:function(){
			setTimeout(function(){
				readQueueMonitor();
			},10000)
		}
	})
}
