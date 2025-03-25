<?php
require 'vendor/autoload.php';

// xhprof start
// xhprof_enable(
// 	XHPROF_FLAGS_NO_BUILTINS | XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY,
// 	["ignored_functions" => ["call_user_func"]]
// );

$f3 = \Base::instance();

$f3->config('conf/index.ini');

$f3->run();

// xhprof end
// $data = xhprof_disable();
// $namespace = 'myapp';
// $filename = sys_get_temp_dir() . "/" . uniqid() . "." . $namespace . ".xhprof"; // /tmp/xxxxxxxxxxxxx.myapp.xhprof
// $res = file_put_contents($filename,serialize($data));
