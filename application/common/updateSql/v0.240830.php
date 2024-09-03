<?php

$ret_data[1] = [
    'tb_add',
    $this->tb_prefix.'cron_tasks',
];
$ret_data[1][2] = <<<INFO
CREATE TABLE `zf_cron_tasks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '任务名称',
  `interval` int(11) NOT NULL COMMENT '任务间隔（秒）',
  `utime` int(11) NOT NULL COMMENT '更新间戳（秒）',
  `func` varchar(255) NOT NULL COMMENT '方法',
  `last_run` int(11) NOT NULL DEFAULT 0 COMMENT '上次运行时间戳',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '任务状态：1正常，0禁用',
  `ctime` int(11) NOT NULL COMMENT '创建时间戳',
  `token` varchar(32) NOT NULL COMMENT '随机字符串',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='定时任务表';
INFO;
$ret_data[1][2] = str_replace('zf_cron_tasks',$this->tb_prefix.'cron_tasks',$ret_data[1][2]);

return $ret_data;


