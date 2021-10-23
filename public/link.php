<?php
echo getcwd(), "\n";
echo '<br/>';
echo exec('ls'), "\n";

unlink('./storage');
echo '<br/><hr/>';
symlink('/home/hajigrou/poem/storage/app/public','./storage');
echo '<br/><hr/>';
echo 'done';

