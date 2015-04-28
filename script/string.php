<?php

if( 1 === get_magic_quotes_gpc()){
    $stripslashes = create_function('$txt', 'return stripslashes($txt);');
}
else{
    $stripslashes = create_function('$txt', 'return $txt;');
}