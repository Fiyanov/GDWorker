<?php
include 'GDWorker.php';

GDWorker::get_instance() ->
load('test2.png')        ->
set_size(500, 0)         ->
save('800.jpg');
?>
