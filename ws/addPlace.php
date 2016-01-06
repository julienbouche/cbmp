<?php
    require_once('../script/db.php');
    require_once('../script/string.php');
    require_once('../script/classes/Settings.php');
    
    db_connect();
    
    $settings = new CBMPSettings();

    if(isset($_POST['name']) and strlen(trim($_POST['name']))>0 and isset($_POST['type'])){    
        $name = utf8_decode(mysql_real_escape_string(stripslashes(trim($_POST['name']))));
        $desc=  utf8_decode(mysql_real_escape_string(stripslashes(trim($_POST['desc']))));
        $website = mysql_real_escape_string(trim($_POST['website']));
        $facebook = mysql_real_escape_string(trim($_POST['facebook']));
        $twitter =  mysql_real_escape_string(trim($_POST['twitter']));

        $id_cat = intval($_POST['type']);
        
        //cleaning, reformating field $website
        if(strlen($website)>0 && !preg_match("#^https?://#i", $website)){
            $website = "http://".$website;
        }
        
        //cleaning, reformating field $facebook
        if(strlen($facebook)>0 && !preg_match("#^https?://#i", $facebook)){
            $facebook = "http://".$facebook;
        }
        
        //cleaning, reformating field $twitter
        if(strlen($twitter)>0 && !preg_match("#^https?://#i", $twitter)){
            $twitter = "http://".$twitter;
        }
        
        //managing checkbox "def_closed" : true/false
        $def_closed = 0;
        if($_POST['def_closed']){
            $def_closed = 1;
        }
        
        
        //inserting new entry
        if(isset($_POST['id']) and strlen($_POST['id'])>0){
            //we verify if editing is enable
            if($settings->getSettingValue('cbmp_application_EditLocation')=='enabled'){
                $id = intval($_POST['id']);
                $sql = "UPDATE place set name='$name', description='$desc', website='$website', id_category=$id_cat, facebook='$facebook', twitter='$twitter', def_closed=$def_closed WHERE id=$id";
            }
        }
        else{
            //extract coordinates only if insert a new place
            $lat =  intval($_POST['lat']*1000000);
            $lng = intval($_POST['lng']*1000000);
            
            $sql = "INSERT INTO place(name, description, lat, lng, website, id_category, facebook, twitter, def_closed) VALUES('$name', '$desc', $lat, $lng, '$website', $id_cat, '$facebook', '$twitter', $def_closed)";
        }
        
        $result = mysql_query($sql);
    }
    else{
        error_log('not enough data to create place.');   
    }
?>