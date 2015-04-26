<?php
require_once('script/db.php');
 /**
  * Form that is displayed when user is adding a place
  */
if(isset($_GET['lat']) && strlen(trim($_GET['lat'])) > 0 && isset($_GET['lng']) && strlen(trim($_GET['lng'])) > 0 ){
    $lat = mysql_escape_string(trim($_GET['lat']));
    $lng = mysql_escape_string(trim($_GET['lng']));
?>
<form method='GET' action='ws/addPlace.php' onsubmit='return(sendFormData(this));'>
    <input type='text' placeholder='Name' name='name' />
    <textarea rows=4 placeholder='Description' name='desc'></textarea>
    <input type='text' placeholder='http://' name='website' />
    <input type='text' placeholder='URL Facebook' name='facebook'/>
    <input type='text' placeholder='URL twitter' name='twitter' />
    <input type='hidden' name='lat' value='<?=$lat?>'/>
    <input type='hidden' name='lng' value='<?=$lng?>'/>
    <?php
    db_connect();
    $query="SELECT id, name FROM category";
    
    $result = mysql_query($query);
    if($result){
    ?>
    <select name='type'>
        <?php while($row=mysql_fetch_assoc($result)) : ?>
        <option value='<?=$row['id']?>'><?=$row['name']?></option>
        <?php endwhile; ?>
    </select>
    
    <?php
    }    
    ?>
    <input type='submit' value='ADD' />
    
</form>
<?php
    mysql_close();
}else{
?>
<p>An error occured. Please, try again later</p>
<?php
}
?>
