<?php
    require_once('../script/db.php');

    db_connect();

    if(isset($_POST['name']) and strlen(trim($_POST['name']))>0 and isset($_POST['lat']) and isset($_POST['lng'])){
        
        $name = utf8_decode(mysql_real_escape_string(trim($_POST['name'])));
        $desc=  utf8_decode(mysql_real_escape_string(trim($_POST['desc'])));
        $website = mysql_real_escape_string(trim($_POST['website']));
        $facebook = mysql_real_escape_string(trim($_POST['facebook']));
        $twitter =  mysql_real_escape_string(trim($_POST['twitter']));
        $lat =  intval($_POST['lat']*1000000);
        $lng = intval($_POST['lng']*1000000);
        $id_cat = intval($_POST['type']);
        
        $sql = "INSERT INTO place(name, description, lat, lng, website, id_category) VALUES('$name', '$desc', $lat, $lng, '$website', $id_cat)";
        
        $result = mysql_query($sql);
    }
    else{
        error_log('not enough data to create place.');   
    }
?>