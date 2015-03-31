<?php
    require_once('../script/db.php');

    db_connect();

    if(isset($_GET['id'])){
        $id = intval($_GET['id']);
        $sql = "SELECT description FROM place WHERE place.id=$id";

        $result = mysql_query($sql);
?>

<?php
        //if there is at least 1 row, we return it. 
        if($result && mysql_num_rows($result)>0){
            $row = mysql_fetch_assoc($result);
            echo utf8_encode($row['description']);
        }
    }

    mysql_close();
?>