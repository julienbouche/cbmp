<?php
require_once('security.php');
require_once('../script/classes/Settings.php');
require_once('../script/images.php');

verify_session($_SERVER['PHP_SELF']);
$settings = new CBMPSettings();

//construct the title page
$cbmpTitlePage = $settings->getSettingValue("cbmp_application_title");
if(strlen(trim($cbmpTitlePage))==0){
  $cbmpTitlePage = htmlentities(CBMPSettings::$DEFAULT_TITLE);
}

if(userBelongToGroup($_SESSION['username'], 'ADMINISTRATORS')){
  //then we can allow user to save modifications
  if(isset($_POST['CATEGORY_UPDATE'])){
    if(isset($_POST['name']) && isset($_POST['idcat']) && isset($_POST['catcolor'])){
      $str_fileinput_name = "img_file_".$_POST['idcat'];
      
      if(isset($_FILES[$str_fileinput_name])){
	ImageTools::save_image($_FILES[$str_fileinput_name], $_POST['name'], "../img/");
      }
      
      //if necessary save any change in the name
      //if name changes, we need to update the corresponding image filename
      $newname = mysql_real_escape_string($_POST['name']);
      $newcolor = mysql_real_escape_string($_POST['catcolor']);
      $idcat = intval($_POST['idcat']);
      
      //read the old category name
      $sql ="SELECT name FROM category where id=$idcat";
      $result = mysql_query($sql);
      
      if($result && mysql_numrows($result)>0){
	//renaming corresponding icon on the filesystem
	$str_oldfilename = '../img/'.mysql_result($result, 0, 'name').'.png';
	if(file_exists($str_oldfilename)) {
	  rename($str_oldfilename, '../img/'.$newname.'.png');
	}
	
	//verify color format
	if(strlen($newcolor)>6){ //'#ABCDEF' -> 'ABCDEF'
	  $newcolor = substr($newcolor, strlen($newcolor)-6, 6);
	}
	//renaming category in database
	$sql= "UPDATE category SET name='$newname', clustercolor='$newcolor' WHERE id=$idcat";
        mysql_query($sql);
      }
      else{
	error_log("No category to update : $sql");
      }
      
    }
  }
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
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
        <h1>Categories</h1>
      
        <?php
        $sql="select id,name, clustercolor FROM category";
        $result = mysql_query($sql);        
        ?>
        
          <?php if($result && mysql_numrows($result)) : ?>
	    <ul class="categoryListing">
            <?php while($row=mysql_fetch_assoc($result)) : ?>
              <li>
		<form action="<?=$_SERVER['PHP_SELF']?>" method="POST" enctype='multipart/form-data'>
		  <input type="hidden" name="idcat" value="<?=$row['id']?>" />
		  <input type="text" name="name" value="<?=$row['name']?>" />
		  <img src="../img/<?=$row['name']?>.png" >
		  <span>Change icon : <input name="img_file_<?=$row['id']?>" type="file" size="25" /></span>
		  <span>Clustering color :<input type="color" name="catcolor" value="#<?=$row['clustercolor']?>"></span>
		  <input type="submit" name="CATEGORY_UPDATE" value="Save"/>
		</form>
	      </li>
            <?php endwhile; ?>
	    </ul>
          <?php endif; ?>
      <br/>
      <hr>

        <form method="POST" action="<?=$_SERVER['PHP_SELF']?>">
            <input name="category_name_to_add" value="" placeholder="name" required />
            <input type="submit" value="Create category" />
        </form>
        <br/>
    </article>
  </body>
</html>