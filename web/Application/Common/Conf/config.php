<?php

return array(
	'DB_TYPE'   => 'mysql', // 数据库类型
	'DB_HOST'   => getenv("MOPAAS") == "1" ? getenv("MOPAAS_MYSQL22118_HOST") : 'localhost', // 服务器地址
	'DB_NAME'   => getenv("MOPAAS") == "1" ? getenv("MOPAAS_MYSQL22118_NAME") :  'psi', // 数据库名
	'DB_USER'   => getenv("MOPAAS") == "1" ? getenv("MOPAAS_MYSQL22118_USER") : 'root', // 用户名
	'DB_PWD'    => getenv("MOPAAS") == "1" ? getenv("MOPAAS_MYSQL22118_PASSWORD") : '', // 密码
	'DB_PORT'   => getenv("MOPAAS") == "1" ? getenv("MOPAAS_MYSQL22118_PORT") : 3306, // 端口
);
