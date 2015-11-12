<?php

class CBMPSettings {
    public static $DEFAULT_TITLE = "<Title.page>";
    public static $DEFAULT_MAP_LAYER_SOURCE = "osm";
    
    private $params;
    
    public function __construct(){
        //init things
        $this->loadValuesFromDB();
    }
    
    private function loadValuesFromDB(){
        $sql = "SELECT id, name, value FROM config";
        $result = mysql_query($sql);
        
        $this->params = array();
        
        if($result && mysql_num_rows($result)>0 ){
            while($row = mysql_fetch_assoc($result)){
                $this->params[$row['name']] = array();
                $this->params[$row['name']][0] = $row["id"];
                $this->params[$row['name']][1] = $row["value"];                
            }
        }    
    }

    /*
     * Fonction permettant de vérifier l'existence d'une clé dans la liste des paramètres du site
     */
    public function exists($key){
        return isset($this->params[$key]);
    }
    
    
    /*
     * Fonction permettant de créer un paramètre (clé, valeur) en base
     */
    public function createSetting($key, $value){
        $name = trim(mysql_real_escape_string($key));
        $value = trim(mysql_real_escape_string($value));
        if($name!=''){
            $sql= "INSERT INTO config(name, value) VALUES('$name', '$value')";
            
            mysql_query($sql);
            
            //on recharge les valeurs pour être sûr de la synchro DB/RAM: nécessaire?
            $this->loadValuesFromDB();
        }
    }
    
    /*
     * fonction permettant de mettre à jour la valeur d'un paramètre dans la base de données
     */
    public function update($id, $value){
        $id=intval($id);
        $value=trim(mysql_real_escape_string($value));
        $sql="UPDATE config SET setting_value='$value' WHERE id=$id";
        mysql_query($sql);
        
        $this->loadValuesFromDB();
        
    }
    
    public function updateSettingByKey($key, $value){
        $key = trim(mysql_real_escape_string($key));
        $value = trim(mysql_real_escape_string($value));
        $sql = "UPDATE config SET value='$value' WHERE name='$key'";
        
        mysql_query($sql);
        
        $this->loadValuesFromDB();
    }
    
    public function delete($param_id){
        $param_id = trim(mysql_real_escape_string($param_id));
        $sql= "DELETE FROM config where id=$param_id";
        mysql_query($sql);
        
        $this->loadValuesFromDB();
    }
    
    /*
     * Fonction permettant de récupérer la liste de tous les paramètres du site 
     */
    public function getSettings(){
        return $this->params;
    }
    
    /*
     * Fonction permettant de récupérer la valeur du paramètre passé en paramètre
     */
    public function getSettingValue($parameter_name){
        if($this->exists($parameter_name)){
            return $this->params[$parameter_name][1];
        }
        else return "";
    }
    
    
}
?>