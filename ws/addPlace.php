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
        
        //cleaning, reformating field $website
        if(strlen($website)>0 && !preg_match("#^http://#i", $website)){
            $website = "http://".$website;
        }
        
        //cleaning, reformating field $facebook
        if(strlen($facebook)>0 && !preg_match("#^http://#i", $facebook)){
            $facebook = "http://".$facebook;
        }
        
        //cleaning, reformating field $twitter
        if(strlen($twitter)>0 && !preg_match("#^http://#i", $twitter)){
            $twitter = "http://".$twitter;
        }
        
        //inserting new entry
        
        $sql = "INSERT INTO place(name, description, lat, lng, website, id_category, facebook, twitter) VALUES('$name', '$desc', $lat, $lng, '$website', $id_cat, '$facebook', '$twitter')";
        
        $result = mysql_query($sql);
    }
    else{
        error_log('not enough data to create place.');   
    }
?>