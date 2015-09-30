<?php
    require_once('../script/db.php');

    db_connect();

    $sql = "SELECT DISTINCT category.name as name FROM category ";
    
    $result = mysql_query($sql);
?>
[
<?php
    if($result && mysql_num_rows($result)>0){
        $cpt=1;
        $nb_categories = mysql_num_rows($result);
        while($row = mysql_fetch_assoc($result)){
?>

    {
        "name" : "<?=htmlspecialchars(addslashes((utf8_encode($row['name']))))?>",
    }
<?php
            if($cpt < $nb_categories){
                echo ",";
            }
            $cpt++;
        }
    }

    mysql_close();
?>
]