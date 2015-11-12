<?php
require_once('security.php');
require_once('../script/classes/Settings.php');


verify_session($_SERVER['PHP_SELF']);
$settings = new CBMPSettings();

//construct the title page
$cbmpTitlePage = $settings->getSettingValue("cbmp_application_title");
if(strlen(trim($cbmpTitlePage))==0){
  $cbmpTitlePage = htmlentities(CBMPSettings::DEFAULT_TITLE);
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
                    <li><a href="<?=$settings->getSettingValue("cbmp_application_baseurl")?>/index.html">The Map</a></li>
                    <li><a href="<?=$settings->getSettingValue("cbmp_application_baseurl")?>/project.html">The Project</a></li>
                </ul>
            </div>
        </nav>
    </header>
    
    <h1>Main</h1>
    <article>
      <form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
        <ul class="form">
          <li>
            <label for="cbmp_application_baseurl">Base URL:</label>
            <input type="text" name="cbmp_application_baseurl" value="<?=$settings->getSettingValue('cbmp_application_baseurl')?>"/>
          </li>
          <li>
            <label for="cbmp_application_title">Title:</label>
            <input type="text" name="cbmp_application_title" value="<?=$settings->getSettingValue('cbmp_application_title')?>"/>
          </li>
          <li>
            <label for="cbmp_application_baselayer">Base Layer : </label>
            <select name="cbmp_application_baselayer">
              <option value="osm" <?="osm"==$settings->getSettingValue("cbmp_application_baselayer")?"selected":""?>>Roads (OSM)</option>
              <option value="sat" <?="sat"==$settings->getSettingValue("cbmp_application_baselayer")?"selected":""?>>Satellite (OSM)</option>
              <option value="hyb" <?="hyb"==$settings->getSettingValue("cbmp_application_baselayer")?"selected":""?>>Hybrid (OSM)</option>
            </select>
          </li>
          <li>
            
          </li>
        </ul>
        
        <input type="submit" value="SAVE" />
      </form>
    </article>
  </body>
</html>