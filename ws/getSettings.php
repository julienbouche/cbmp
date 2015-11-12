<?php
    require_once('../script/db.php');

    db_connect();

    $sql = "SELECT DISTINCT id, name, value FROM config";
    /*if(isset($_GET['q'])){
        $filter = mysql_real_escape_string($_GET['q']);
        $sql .= " AND name like '%$filter%'";
    }*/
    $result = mysql_query($sql);
?>
({
<?php
    if($result && mysql_num_rows($result)>0){
        $cpt=1;
        $nb_settings = mysql_num_rows($result);
        while($row = mysql_fetch_assoc($result)){
            //"key":"value" format
?>

    
        "<?=htmlspecialchars(addslashes((utf8_encode($row['name']))))?>":"<?=$row['value']?>"
    
<?php
            if($cpt < $nb_settings){
                echo ",";
            }
            $cpt++;
        }
    }

    mysql_close();
?>
})