alter table `qw_hlydelivery` 
   add column `expstatus` varchar(200) NULL COMMENT '����״̬' after `status`, 
   add column `statusmsg` varchar(200) NULL COMMENT '����״̬����' after `expstatus`,
   change `status` `status` tinyint(1) default '1' NULL  comment '����״̬';
   
alter table `qw_hlydelivery` 
   add column `booking_id` varchar(128) NULL COMMENT '����id' after `code`;