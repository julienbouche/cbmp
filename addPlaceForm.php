<?php
 /**
  * Form that is displayed when user is adding a place
  */
if(isset($_GET['lat']) && strlen(trim($_GET['lat'])) > 0 && isset($_GET['lng']) && strlen(trim($_GET['lng'])) > 0 ){
    $lat = mysql_escape_string(trim($_GET['lat']));
    $lng = mysql_escape_string(trim($_GET['lng']));
?>
<form method='GET' action='ws/addPlace.php' onsubmit='return(sendFormData(this));'>
    <input type='text' placeholder='Name' name='name' />
    <input type='text' placeholder='Description' name='desc' />
    <input type='text' placeholder='http://' name='website' />
    <input type='text' placeholder='URL Facebook' name='facebook'/>
    <input type='text' placeholder='URL twitter' name='twitter' />
    <input type='text' name='lat' value='<?=$lat?>'/>
    <input type='text' name='lng' value='<?=$lng?>'/>
    <select name='type'><option value='1'>Brewery</option><option value='2'>Bar</option></select>
    <input type='submit' value='OK' />
</form>
<?php
}else{
?>
<p>An error occured. Please, try again later</p>
<?php
}
?>
