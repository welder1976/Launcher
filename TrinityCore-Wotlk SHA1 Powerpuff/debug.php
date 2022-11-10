<?php

$file = file_get_contents('https://www.dropbox.com/s/8z7nh2xdzqokwe1/downloadlist.txt?dl=1', FILE_USE_INCLUDE_PATH);

print_r($file);