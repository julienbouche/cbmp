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

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width" />
    <link rel="stylesheet" href="../css/main.css" media="screen" type="text/css" />
    <link rel="stylesheet" href="../css/admin.css" media="screen" type="text/css" />
    
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
        <h1>Statistics</h1>
      
        <?php
        $sql="SELECT category.name as name, count( * ) AS number
	      FROM place, category
	      WHERE place.id_category = category.id
	      GROUP BY id_category, category.name
	      ORDER BY number DESC ";
        $result = mysql_query($sql);
	$total = 0;
        ?>
        
          <?php if($result && mysql_numrows($result)) : ?>
	    <table class="statistics">
            <?php while($row=mysql_fetch_assoc($result)) : $total+=intval($row['number'])?>
              <tr>
		<td><?=$row['name']?></td>
		<td><?=$row['number']?></td>
	      </tr>
            <?php endwhile; ?>
	      <tr>
		<td>Total</td>
		<td><?=$total?></td>
	      </tr>
	    </table>
          <?php endif; ?>
      <br/>
      
    </article>
  </body>
</html>