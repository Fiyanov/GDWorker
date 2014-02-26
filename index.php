<?php
include 'GDWorker.php';

$gd = GDWorker::get_instance();

$gd->load('test2.png');
$gd->set_size(800, 0)->save('800.jpg');
?>
