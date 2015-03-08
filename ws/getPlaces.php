<?php
    require_once('../script/db.php');

    db_connect();

    $sql = "SELECT place.id as id, place.name as place, lat, lng, description, website, category.name as category FROM place, category WHERE place.id_category=category.id";
    if(isset($_GET['q'])){
        $filter = mysql_real_escape_string($_GET['q']);
        $sql .= " AND place.name like '%$filter%'";
    }
    error_log($sql);
    $result = mysql_query($sql);
?>
[
<?php
    if($result && mysql_num_rows($result)>0){
        $cpt=1;
        $nb_places = mysql_num_rows($result);
        while($row = mysql_fetch_assoc($result)){
?>

    {
        "id" : <?=$row['id']?>,
        "name" : "<?=utf8_encode($row['place'])?>",
        "lat" : <?=$row['lat']?>,
        "lng" : <?=$row['lng']?>,
        "desc" : "<?=utf8_encode(substr($row['description'], 0, 50)."...")?>",
        "type" : "<?=$row['category']?>",
        "website" : "<?=$row['website']?>"
    }
<?php
            if($cpt < $nb_places){
                echo ",";
            }
            $cpt++;
        }
    }

    mysql_close();
?>
]