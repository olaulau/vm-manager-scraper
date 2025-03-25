<?php
require 'vendor/autoload.php';

// xhprof start
// xhprof_enable();

$f3 = \Base::instance();

$f3->config('conf/index.ini');

$f3->run();

// xhprof end
// $data = xhprof_disable();
// $namespace = 'myapp';
// $filename = sys_get_temp_dir() . "/" . uniqid() . "." . $namespace . ".xhprof"; // /tmp/xxxxxxxxxxxxx.myapp.xhprof
// $res = file_put_contents($filename,serialize($data));
