<?php
require_once('security.php');
require_once('../script/classes/Settings.php');


verify_session($_SERVER['PHP_SELF']);
$settings = new CBMPSettings();

//construct the title page
$cbmpTitlePage = $settings->getSettingValue("cbmp_application_title");
if(strlen(trim($cbmpTitlePage))==0){
  $cbmpTitlePage = htmlentities(CBMPSettings::$DEFAULT_TITLE);
}

if(userBelongToGroup($_SESSION['username'], 'ADMINISTRATORS')){
  //then we can allow user to do modifications
  
  //user is adding a new group
  if(isset($_POST['group_name_to_add']) and strlen(trim($_POST['group_name_to_add']))>0){
    $newgroup = mysql_real_escape_string(trim($_POST['group_name_to_add']));
    $sql = "INSERT INTO role (name) VALUES('$newgroup')";
    mysql_query($sql);
  }
  
  //user is adding a user to a group
  if(isset($_POST['role_selected']) and isset($_POST['users'])){
    if(is_array($_POST['users'])){
        $group_id = intval($_POST['role_selected']);
        $users = $_POST['users'];
        foreach($users as $userToAdd){
            $sql = "INSERT INTO userrole(roleid,userid) VALUES($group_id, $userToAdd)";
            mysql_query($sql);
        }
    }
  }
  
  //user is adding a new user
  if(isset($_POST['user_login_to_add']) and isset($_POST['user_email_to_add'])){
    $login = mysql_real_escape_string($_POST['user_login_to_add']);
    $email = mysql_real_escape_string($_POST['user_login_to_add']);
    $newpass = substr(md5(time()),0,6);
    
    $sql = "INSERT INTO user (login,email, password) VALUES('$login', '$email', PASSWORD('$newpass'))";
    mysql_query($sql);
    
    //send notifications
    $headers = "From:$fromName <craftbeermap@free.fr>";
    $headers .= "Content-Type: text/plain;charset=\"iso-8859-1\"\n";
    $message = str_replace("\\", "", "Your account has been created. \nlogin: $login \npass: $newpass \n Please change this password as soon as possible.");
    mail($email, "CBMP Account created", $message, "From: craftbeermap@free.fr");
  }
  
  //user is deleting a user from a group
  if(isset($_POST['DELETE']) and isset($_POST['userlist'])){
    $user = intval($_POST['userlist']);
    $group = intval($_POST['role_selected']);
    $sql = "DELETE FROM userrole WHERE userid=$user AND roleid=$group";
    mysql_query($sql);
  }
  
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf8" />
    <meta name="viewport" content="width=device-width" />
    <link rel="stylesheet" href="../css/main.css" media="screen" type="text/css" />
    <link rel="stylesheet" href="../css/admin.css" media="screen" type="text/css" />
    <link rel="stylesheet" href="../css/popInside.css" media="screen" type="text/css" />

    <script type="text/javascript">
      function popinside_show(id){
	document.getElementById(id).style.display="block";
      }

      function popinside_close(id){
        document.getElementById(id).style.display="none";
      }
    </script>
    <title><?=$cbmpTitlePage?> Administration page</title>
  </head>
  <body>
    <header>
        <h1><?=$cbmpTitlePage?></h1>
        <nav id="main-navigation" class="main-navigation">
            <a href="#main-navigation" class="nav-open">Menu</a>
            <a href="#" class="nav-close">Close</a>
            <div id="main-menu">
                <ul>
                    <li><a href="<?=$settings->getSettingValue("cbmp_application_baseurl")?>/index.php">The Map</a></li>
                    <li><a href="<?=$settings->getSettingValue("cbmp_application_baseurl")?>/project.html">The Project</a></li>
                </ul>
            </div>
        </nav>
    </header>
    <nav class="admin-navigation">
      <div class="admin-menu">
          <ul>
              <li><a href="<?=$settings->getSettingValue("cbmp_application_baseurl")?>/admin/index.php">Main</a></li>
              <li><a href="<?=$settings->getSettingValue("cbmp_application_baseurl")?>/admin/acl.php">Users & Groups</a></li>
              <li><a href="<?=$settings->getSettingValue("cbmp_application_baseurl")?>/admin/categories.php">Categories</a></li>
              <li><a href="<?=$settings->getSettingValue("cbmp_application_baseurl")?>/admin/statistics.php">Statistics</a></li>
          </ul>
      </div>
    </nav>
    <article>
        <h1>Users & Groups</h1>
        <form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
      
        <?php
        $sql="select id,name FROM role";
        $result = mysql_query($sql);        
        ?>
        
        <select size=10 multiple onchange="this.form.submit();" name="role_selected" style="width:40%">
          <?php if($result && mysql_numrows($result)) : ?>
            <?php while($row=mysql_fetch_assoc($result)) : ?>
              <?php if(isset($_POST['role_selected']) && $_POST['role_selected']==$row['id']): ?>
                <option selected value="<?=$row['id']?>"><?=$row['name']?></option>
              <?php else : ?>
                <option value="<?=$row['id']?>"><?=$row['name']?></option>
              <?php endif; ?>
            <?php endwhile; ?>
          <?php endif; ?>
        </select>
        <select size=10 name="userlist" style="width:40%;">
          <?php if(isset($_POST['role_selected'])) : ?>
            <?php
                $sql = "SELECT userid, login FROM user, userrole WHERE userrole.userid=user.id AND roleid=".$_POST['role_selected']." ORDER BY userid ASC";
                $result = mysql_query($sql);
            ?>
              <?php if($result && mysql_num_rows($result)>0) : ?>    
            
                <?php while ($row=mysql_fetch_assoc($result)) : ?>
                  <option value="<?=$row['userid']?>"><?=$row['login']?></option> 
                <?php endwhile; ?>
              <?php endif; ?>
            <?php endif; ?>
        </select>
        <input type="button" onclick="popinside_show('addUsersWindow')" value="ADD" style="width:auto"/>
        <input type="submit" name="DELETE" value="REMOVE" style="width:auto"/>
        <!--input type="submit" value="SAVE" style="margin-top: 20px;"/-->
      </form>
      <br/>
      <hr>
        <form method="POST" action="<?=$_SERVER['PHP_SELF']?>">
            <input name="group_name_to_add" value="" placeholder="Group name" />
            <input type="submit" value="Add Group" />
        </form>
        <br/>
        <form method="POST" action="<?=$_SERVER['PHP_SELF']?>">
            <input name="user_login_to_add" value="" placeholder="Login" required />
            <input name="user_email_to_add" value="" placeholder="email" required />
            <input type="submit" value="Create user" />
        </form>
        <br/>

    </article>
    
    <?php if(isset($_POST['role_selected'])) : ?>
    <div id="addUsersWindow" class="insideWindow">
	<span class="insideWindowTitle">Utilisateurs</span><span class="insideWindowCloser" onclick="popinside_close('addUsersWindow')">X</span>
	<form method="POST" action="<?=$_SERVER['PHP_SELF']?>">
            <div class="insideWindowContent" >
                <input type="hidden" name="role_selected" value="<?=$_POST['role_selected']?>" />
            <?php
		
                $sql = "SELECT id,login FROM user WHERE id NOT IN (SELECT userid FROM userrole WHERE userrole.roleid=".$_POST['role_selected'].")";
		$results = mysql_query($sql);
	    ?>
                <?php if ($results && mysql_numrows($results)>0) : ?>
                        <table>
                        <?php while($row = mysql_fetch_assoc($results)) : ?>
                                <tr>
                                    <td style="width:90%;text-align:left;color:black;">
                                        <?=$row['login']?>
                                    </td>	
				    <td>
					<input type="checkbox" name="users[]" value="<?=$row['id']?>" />
				    </td>
                                </tr>
                        <?php endwhile; ?>
                        </table>
                <?php else :  ?>
                  No user available.
                <?php endif; ?>
                
            
            </div>
            <input type="submit" value="Valider" style="float:right;margin-right:5px"/>
            <input type="button" value="Annuler" onclick="popinside_close('addUsersWindow')" style="float:right;margin-right:5px"/>
        </form>
    </div> 
    <?php endif; ?>
  </body>
</html>