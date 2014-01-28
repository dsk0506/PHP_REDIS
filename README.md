此Demo使用PHP+REDIS实现一个微博写入消息队列,处理并发写入的问题。 
在线预览地址:http://115.29.38.172/redis/weibo/
如何在本地运行此Demo？
1.有LANP，或者LAMP环境。
2.安装redis，以及php-redis扩展
3.导入weibo.sql
4.修改Config/dbConfig.php
5.nohup ./WeiboQueue.sh &   [确保能通过php命令，直接执行php脚本]