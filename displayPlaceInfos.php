<?php
require_once('script/db.php');
require_once('script/string.php');
require_once('script/classes/Settings.php');

db_connect();
$settings = new CBMPSettings();

if(isset($_GET['id'])){
    $id = intval($_GET['id']);
    $sql_query = "SELECT DISTINCT place.id as id, place.name as placeName, def_closed, lat, lng, description, website, facebook, twitter, category.name as category FROM place, category WHERE place.id_category=category.id AND place.id=$id";
    $result= mysql_query($sql_query);
    
    if($result && mysql_numrows($result)>0){
        $row = mysql_fetch_assoc($result);    
        
?>

<div>
    <h2><?=utf8_encode($stripslashes($row['placeName']))?></h2>
    <?php if($row['def_closed']=='1') : ?>                
        <img src="img/gemicon/closed32x32.png" alt="No longer exists" title="No longer exists" height="20" width="20"/>
    <?php endif; ?>
    <?php if($row['website']) : ?>                
        <a href="<?=$row['website']?>" target="_blank"><img src="img/gemicon/website32x32.png" height="20" width="20"/></a>
    <?php endif; ?>
    <?php if($row['facebook']) : ?>
        <a href="<?=$row['facebook']?>" target="_blank"><img src="img/gemicon/fb32.png" height="20" width="20"/></a>
    <?php endif; ?>
                        
    <?php if($row['twitter'] ) : ?>
        <a href="<?=$row['twitter']?>" target="_blank"><img src="img/gemicon/twitter32.png" height="20" width="20"/></a>
    <?php endif; ?>
    <div class="description" id="desc'+selectedFeature.get('id')+'"><?=utf8_encode(nl2br($stripslashes($row['description'])))?></div>
    <?php if($settings->getSettingValue('cbmp_application_EditLocation')=='enabled') : ?>
    <input type="button" value="Edit" onclick="getEditForm(this,<?=$id?>);" >
    <input type="button" value="Delete" onclick="deletePlace('<?=htmlspecialchars(addslashes($stripslashes(utf8_encode($row['placeName']))))?>',<?=$id?>);"/>
    <?php endif; ?>
</div>
<?php
    }
    else{
?>
<div>An error occured. Please, try again later.</div>         
<?php
    }
}else{
?>
<div>An error occured. Please, try again later.</div>
<?php
}
?>