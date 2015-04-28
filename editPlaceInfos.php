<?php
require_once('script/db.php');
 /**
  * Form that is displayed when user is adding a place
  */
if(isset($_GET['id']) && strlen(trim($_GET['id'])) > 0 ){
    db_connect();
    $id = intval($_GET['id']);
    $sql_query = "SELECT DISTINCT place.id as id, place.name as placeName, lat, lng, description, website, facebook, twitter, category.name as category, category.id as cat_id FROM place, category WHERE place.id_category=category.id AND place.id=$id";
    $result= mysql_query($sql_query);
    
    if($result && mysql_numrows($result)>0){
        $row = mysql_fetch_assoc($result);    


?>
<form method='POST' action='ws/addPlace.php' onsubmit='return(sendFormData(this));'>
    <input type='hidden' name='id' value='<?=$id?>' />
    <input type='text' placeholder='Name' name='name' value="<?=$row['placeName']?>" />
    <textarea rows=4 placeholder='Description' name='desc'><?=$row['description']?></textarea>
    <input type='text' placeholder='http://' name='website' value='<?=$row['website']?>' />
    <input type='text' placeholder='URL Facebook' name='facebook' value='<?=$row['facebook']?>' />
    <input type='text' placeholder='URL twitter' name='twitter' value='<?=$row['twitter']?>' />
    <?php
        $query="SELECT id, name FROM category";
        
        $result = mysql_query($query);
        if($result){
        ?>
        <select name='type'>
            <?php while($rowPlaces=mysql_fetch_assoc($result)) : ?>
                <option <?php if($rowPlaces['id'] === $row['cat_id']) echo "selected";?> value='<?=$rowPlaces['id']?>'><?=$rowPlaces['name']?></option>
            <?php endwhile; ?>
        </select>
        
        <?php
        }    
        ?>
    <input type='submit' value='SAVE' />
    
</form>
<?php
        mysql_close();
    }
}else{
?>
<p>An error occured. Please, try again later</p>
<?php
}
?>
