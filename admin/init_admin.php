<?php
    require_once('../script/db.php');
    db_connect();
        
    //check if database has been initialized.
    $sql = "SELECT login FROM user, role, userrole WHERE user.id=userrole.userid AND userrole.roleid=role.id AND role.name='ADMINISTRATORS'";
    $result = mysql_query($sql);
    if($result && mysql_num_rows($result)>0)header('Location:login.php');
    
    //user is creating 
    if(isset($_POST['login']) && isset($_POST['pwd'])){
        //create the admin role if doesn't exist
        $sql = "SELECT name FROM role WHERE role.name='ADMINISTRATORS'";
        $result = mysql_query($sql);
        if($result && mysql_numrows($result)==0){
            $sql = "INSERT INTO role SET role.name='ADMINISTRATORS'";
            $result = mysql_query($sql);
        }
        
        //create first admin
        $login = mysql_real_escape_string($_POST['login']);
        $pwd = mysql_real_escape_string($_POST['pwd']);
        $email = mysql_real_escape_string($_POST['email']);
        
        //creates the user in table user
        $sql_request = "INSERT INTO user SET login='$login', email='$email',password=PASSWORD('$pwd')";
        $result = mysql_query($sql_request);
        
        //get the user id 
        
        //add the user ADMIN role
        $sql = "INSERT INTO userrole(userid, roleid) SELECT user.id, role.id FROM user, role WHERE user.login='$login' AND role.name='ADMINISTRATORS'";
        $result = mysql_query($sql);
        
        
        header('Location:index.php');
        
    }
?>


<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width" />
    <link rel="stylesheet" href="../css/main.css" type="text/css" />
    <link rel="stylesheet" href="../css/admin.css" type="text/css" />


    
    <title>The Craft Beer Map Project - Creation page</title>
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
    Welcome ! This page will help you create the first administrator of your website:
    <form class="loginbox" name="loginForm" method="POST" action="<?=$_SERVER['PHP_SELF']?>">
        <ul class="form">
        <li>
            <label for="login">Login:</label>
            <input name="login" type="text" maxlength="50" size="25" />
        </li>
        <li>
            <label for="pwd" >Password :</label>
            <input name="pwd" type="password" maxlength="100" size="25" />
        </li>
        <li>
            <label for="pwd2" >Confirm password:</label>
            <input name="pwd2" type="password" maxlength="100" size="25" />
        </li>
        <li>
            <label for="email">Email:</label>
            <input name="email" type="text" maxlength="100" size="25" />       
        </li>
        
        <input type="submit" name="submitok" value="Initialize" />
        </ul>
    </form>
  </body>
</html>