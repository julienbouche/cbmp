<?php
require_once('script/db.php');

if(isset($_GET['id'])){
    db_connect();
    $id = intval($_GET['id']);
    $sql_query = "SELECT DISTINCT place.id as id, place.name as placeName, lat, lng, description, website, facebook, twitter, category.name as category FROM place, category WHERE place.id_category=category.id AND place.id=$id";
    $result= mysql_query($sql_query);
    
    if($result && mysql_numrows($result)>0){
        $row = mysql_fetch_assoc($result);    
        
?>

<div>
    <h2><?=utf8_encode($row['placeName'])?></h2>
    <?php if($row['website']) : ?>                
        <a href="<?=$row['website']?>" target="_blank"><img src="img/gemicon/website32x32.png" height="20" width="20"/></a>
    <?php endif; ?>
    <?php if($row['facebook']) : ?>
        <a href="<?=$row['facebook']?>"><img src="img/gemicon/fb32.png" height="20" width="20"/></a>
    <?php endif; ?>
                        
    <?php if($row['twitter'] ) : ?>
        <a href="<?=$row['twitter']?>"><img src="img/gemicon/twitter32.png" height="20" width="20"/></a>
    <?php endif; ?>
    <div class="description" id="desc'+selectedFeature.get('id')+'"><?=utf8_encode(nl2br($row['description']))?></div>
    <input type="button" value="Edit" onclick="getEditForm(this,<?=$id?>);" >
    <input type="button" value="Delete" onclick="deletePlace('<?=$row['placeName']?>',<?=$id?>);"/>
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