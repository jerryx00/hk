alter table `qw_hlydelivery` 
   add column `expstatus` varchar(200) NULL COMMENT 'ÎïÁ÷×´Ì¬' after `status`, 
   add column `statusmsg` varchar(200) NULL COMMENT '¶©µ¥×´Ì¬ÃèÊö' after `expstatus`,
   change `status` `status` tinyint(1) default '1' NULL  comment '¶©µ¥×´Ì¬';
   
alter table `qw_hlydelivery` 
   add column `booking_id` varchar(128) NULL COMMENT '¶©µ¥id' after `code`;