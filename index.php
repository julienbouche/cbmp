<?php
require_once('script/db.php');
require_once('script/classes/Settings.php');

db_connect();
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
    <meta charset="utf8" />
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="lib/ol3/css/ol.css" type="text/css">
    <link rel="stylesheet" href="css/main.css" type="text/css">
    <script src="lib/ol3/js/ol.js" type="text/javascript"></script>
    
    <script src="lib/ol3-popup/ol3-popup.js" type="text/javascript"></script>
    <link rel="stylesheet" href="lib/ol3-popup/ol3-popup.css" type="text/css">
    
    <script src="js/cbmp-ol3.js" type="text/javascript"></script>
    <script src="js/cbmp-interactions.js" type="text/javascript"></script>
    <script src="js/cbmp-core.js" type="text/javascript"></script>
    
    <script src="js/autocomplete.js" type="text/javascript"></script>
    <link rel="stylesheet" href="css/autocomplete.css" type="text/css">
    <title><?=$cbmpTitlePage?></title>
  </head>
  <body>
    <header><input type="search" placeholder="search" class="autoCompletionTextBox"
                  onkeydown="captureKeyEvents(this, event)"
                onfocus="activateAutoComplete();"
	       onblur="disableAutoComplete();"
	       onkeyup="filterlist(this.value, document.getElementById('placesList'))" />
      <div class="autoCompletion" id="ac_container">
		<ul id="placesList">
		</ul>
	</div>
        <h1><?=$cbmpTitlePage?></h1>
        
        <nav id="main-navigation" class="main-navigation">
            <a href="#main-navigation" class="nav-open">Menu</a>
            <a href="#" class="nav-close">Close</a>
            <div id="main-menu">
                <ul>
                    <li><a href="index.html">The Map</a></li>
                    <li><a href="project.html">The Project</a></li>
                </ul>
            </div>
        </nav>  
    </header>
    <div id="map" class="map">
        <div id="popup"></div>
        <div class="LayersSelector" >
          <ul id="layersListDOM" >
          </ul>
        </div>
        
    </div>
    <script type="text/javascript">
        //create the map
        var carte = new cbmp.CBMap("map");
        
        //initialise the map and necessary elements (layers, etc.)
        //uses a callback function to add every options (after initialisation)
        carte.init('layersListDOM', function() {
	  if (carte.getSettingValue("cbmp_application_NewLocation") == "enabled") {
	    //add the ability to add new place using the mouse
	    cbmp.interactions.addDrawControl(carte.getMap());
	  }
          
          //add geo location tracking feature
          cbmp.interactions.addGeoLocTrackingControl(carte.getMap());
          
          //enable centering map based on url params
          cbmp.interactions.loadPositionFromURL('ll', 'lg', 'z', carte.adjustView);
          
          //update url whenever the view is changed
          cbmp.interactions.keepURLUpToDateWithLocation('ll', 'lg', 'z', carte.getMap());
          
        });
    </script>
  </body>
</html>