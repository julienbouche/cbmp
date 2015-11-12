<?php

class CBMPSettings {
    public function __construct(){
        //init things
        $this->reloadValuesFromDB();
    }
    
    private function loadDatasFromDB(){
        $sql = "SELECT id_param, param_name, param_value FROM config ";
        $result = mysql_query($sql);
        
        $this->params = array();
        
        if($result && mysql_num_rows($result)>0 ){
            while($row = mysql_fetch_assoc($result)){
                $this->params[$row['param_name']] = array();
                $this->params[$row['param_name']][0] = $row["id_param"];
                $this->params[$row['param_name']][1] = $row["param_value"];
            }
        }    
    }
    
    private function save(){
    }
}
?>