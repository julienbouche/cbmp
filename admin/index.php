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
  //try to create or update every $_POST variables with a key beginning with cbmp.*
  foreach($_POST as $key => $value){
    //verify pattern
    if(strlen($key)>4 && substr($key, 0, strlen('cbmp_'))==='cbmp_'){
      
      //verify existence to update or create
      if(!$settings->exists($key)){
        $settings->createSetting($key, $value);
      }else{
        $settings->updateSettingByKey($key, $value);
      }
    }
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
      <form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
        <h1>Main</h1>
        <ul class="form">
          <li>
            <label for="cbmp_application_baseurl">Base URL:</label>
            <input type="text" name="cbmp_application_baseurl" value="<?=$settings->getSettingValue('cbmp_application_baseurl')?>"/>
          </li>
          <li>
            <label for="cbmp_application_title">Title:</label>
            <input type="text" name="cbmp_application_title" value="<?=$settings->getSettingValue('cbmp_application_title')?>"/>
          </li>
        </ul>
        <h1>Map's Display</h1>
        <ul class="form">
          <li>
            <label for="cbmp_application_baselayer">Base Layer : </label>
            <select name="cbmp_application_baselayer">
              <option value="osm" <?="osm"==$settings->getSettingValue("cbmp_application_baselayer")?"selected":""?>>Roads (OSM)</option>
              <option value="sat" <?="sat"==$settings->getSettingValue("cbmp_application_baselayer")?"selected":""?>>Satellite (OSM)</option>
              <option value="hyb" <?="hyb"==$settings->getSettingValue("cbmp_application_baselayer")?"selected":""?>>Hybrid (OSM)</option>
            </select>
          </li>
          <li>
            <label for="cbmp_application_maxZoom">Max Zoom Level : </label>
            <input type="number" name="cbmp_application_maxZoom" min=0 max=28 value="<?=$settings->getSettingValue('cbmp_application_maxZoom')?>"/>
          </li>
          <li>
            <label for="cbmp_application_minZoom">Min Zoom Level : </label>
            <input type="number" name="cbmp_application_minZoom" min=0 max=28 value="<?=$settings->getSettingValue('cbmp_application_minZoom')?>"/>
          </li>
          <li>
            <label for="cbmp_application_clusterSizeItemCount">Cluster Size On Item Count : </label>
            <select name="cbmp_application_clusterSizeItemCount">
              <option value="enabled"  <?="enabled"==$settings->getSettingValue("cbmp_application_clusterSizeItemCount")?"selected":""?>>Enabled</option>
              <option value="disabled" <?="disabled"==$settings->getSettingValue("cbmp_application_clusterSizeItemCount")?"selected":""?>>Disabled</option>
            </select>
          </li>
          <li>
            <label for="cbmp_application_locationsName">Location's Name: </label>
            <select name="cbmp_application_locationsName">
              <option value="enabled"  <?="enabled"==$settings->getSettingValue("cbmp_application_locationsName")?"selected":""?>>Enabled</option>
              <option value="disabled" <?="disabled"==$settings->getSettingValue("cbmp_application_locationsName")?"selected":""?>>Disabled</option>
            </select>
          </li>
        </ul>
        <h1>Authoring</h1>
        <ul class="form">
          <li>
            <label for="cbmp_application_NewLocation">New location : </label>
            <select name="cbmp_application_NewLocation">
              <option value="enabled"  <?="enabled"==$settings->getSettingValue("cbmp_application_NewLocation")?"selected":""?>>Enabled</option>
              <option value="disabled" <?="disabled"==$settings->getSettingValue("cbmp_application_NewLocation")?"selected":""?>>Disabled</option>
            </select>
          </li>
          <li>
            <label for="cbmp_application_EditLocation">Edit location : </label>
            <select name="cbmp_application_EditLocation">
              <option value="enabled"  <?="enabled"==$settings->getSettingValue("cbmp_application_EditLocation")?"selected":""?>>Enabled</option>
              <option value="disabled" <?="disabled"==$settings->getSettingValue("cbmp_application_EditLocation")?"selected":""?>>Disabled</option>
            </select>
          </li>
        </ul>
        <h1>User</h1>
        <ul class="form">
          <li>
            <label for="cbmp_application_geotrack">GeoTracking : </label>
            <select name="cbmp_application_geotrack">
              <option value="enabled"  <?="enabled"==$settings->getSettingValue("cbmp_application_geotrack")?"selected":""?>>Enabled</option>
              <option value="disabled" <?="disabled"==$settings->getSettingValue("cbmp_application_geotrack")?"selected":""?>>Disabled</option>
            </select>
          </li>
          <li>
            <label for="cbmp_application_urlgeoparams">Update URL with geolocation params : </label>
            <select name="cbmp_application_urlgeoparams">
              <option value="enabled"  <?="enabled"==$settings->getSettingValue("cbmp_application_urlgeoparams")?"selected":""?>>Enabled</option>
              <option value="disabled" <?="disabled"==$settings->getSettingValue("cbmp_application_urlgeoparams")?"selected":""?>>Disabled</option>
            </select>
          </li>
        </ul>
        <input type="submit" value="SAVE" />
      </form>
    </article>
  </body>
</html>