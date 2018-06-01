alter table `qw_hlydelivery` 
   add column `expstatus` varchar(200) NULL COMMENT '物流状态' after `status`, 
   add column `statusmsg` varchar(200) NULL COMMENT '订单状态描述' after `expstatus`,
   change `status` `status` tinyint(1) default '1' NULL  comment '订单状态';
   
alter table `qw_hlydelivery` 
   add column `booking_id` varchar(128) NULL COMMENT '订单id' after `code`;
   
   
   
日常操作
从远程更新代码 
git pull origin master
