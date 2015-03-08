<?php
    require_once('../script/db.php');

    db_connect();

    if(isset($_POST['id'])){
        
        $id = intval($_POST['id']);
        
        $sql = "DELETE FROM place WHERE id=$id";
        
        $result = mysql_query($sql);
    }
    else{
        error_log('No Id supplied to delete place');   
    }
?>