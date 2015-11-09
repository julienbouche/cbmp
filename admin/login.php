<?php
    require_once('../script/db.php');
    db_connect();
    
    //init session variables
    session_start();
    
    //check if database has been initialized.
    $sql = "SELECT login FROM user, role, userrole WHERE user.id=userrole.userid AND userrole.roleid=role.id AND role.name='ADMINISTRATORS'";
    $result = mysql_query($sql);
    
    if($result && mysql_numrows($result)==0){
        header('Location:init_admin.php');
    }
    
    //user is trying to connect
    if(isset($_POST['uid']) && isset($_POST['pwd'])){
        $uid = mysql_real_escape_string($_POST['uid']);
        $pwd = mysql_real_escape_string($_POST['pwd']);
        
        $sql_request = "SELECT login FROM user, role, userrole WHERE user.id=userrole.userid AND userrole.roleid=role.id AND login='$uid' AND password=PASSWORD('$pwd') AND role.name='ADMINISTRATORS'";
        $result = mysql_query($sql_request);
        
        if($result && mysql_numrows($result)>0){
            //user is successfully connected
            $_SESSION['username'] = $uid;
            
            //redirect to admin index page
            header('Location:index.php');
        }
        else error_log("Authentication error :"+$_POST['uid']);
    }
?>


<html lang="en">
  <head>
    <meta charset="utf8" />
    <meta name="viewport" content="width=device-width" />
    <link rel="stylesheet" href="../css/main.css" type="text/css" />
    <link rel="stylesheet" href="../css/admin.css" type="text/css" />


    
    <title>The Craft Beer Map Project - Administration page</title>
  </head>
  <body>
    <header>
        <h1>Craft Beer Map Project</h1>
        <nav id="main-navigation" class="main-navigation">
            <a href="#main-navigation" class="nav-open">Menu</a>
            <a href="#" class="nav-close">Close</a>
            <div id="main-menu">
                <ul>
                    <li><a href="/index.html">The Map</a></li>
                    <li><a href="/project.html">The Project</a></li>
                </ul>
            </div>
        </nav>
    </header>
    
    <form class="loginbox" name="loginForm" method="POST" action="<?=$_SERVER['PHP_SELF']?>">
        <label for="uid">Login/e-mail:</label>
        <input name="uid" type="text" maxlength="100" size="25" />
        <label for="" >Mot de passe:</label>
        <input name="pwd" type="password" maxlength="100" size="25" />
        <!--label for="creercookie" style="font-size:x-small;color:#999;">Rester connect&eacute; ?</label>
        <input type="checkbox" name="creercookie" <?php if(isset($_COOKIE["cbmp.administration"]))echo "checked";?> /-->
        
        <input type="submit" name="submitok" value="Se connecter" />
        <br/>
        <br/>
        <a href="resetPwd.php"  style="font-size:x-small;color:#999;">Mot de passe oubli&eacute; ?</a>

        <br/>
    </form>
  </body>
</html>