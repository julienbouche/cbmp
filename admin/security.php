<?php
require_once('../script/db.php');

session_start();

function verify_session($url){
    db_connect();
    
    if(!isset($_SESSION['username'])){
        //is the user has a cookie
        if(isset($_COOKIE['cbmp.administration'])){
            //@TODO if we implement cookies management
        }
        else {
            header('Location:login.php');
        }
    }
}

function userBelongToGroup($username, $groupname){
    $belong = false;
    db_connect();
    
    $sql= "SELECT login FROM user, role, userrole WHERE user.id=userrole.userid AND userrole.roleid=role.id AND user.login='$username' AND role.name='$groupname'";
    $result = mysql_query($sql);
    
    if($result && mysql_numrows($result)>0){
        $belong = true;
    }
    
    return $belong;
}

?>
