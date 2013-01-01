<?php

class ZFExt_Model_Application {

    protected $db;
    protected $curr_db_id;

    protected $object_types;
    protected $table_aliases;
    protected $table_fields;
    protected $select_fields;//field of the tables including alias table names like 'go.id'


    public function __construct(Zend_Db_Adapter_Abstract $new_db){

        $this->db = $new_db;

        $this->object_types = array('table','lead','comment');

        $this->table_aliases = array('object' => 'go','table' => 'gt','join' => 'gj','comment' => 'gc');

        $this->table_fields = array( 
            'object' => array('id','type','x','y','width','height'),
            'table' => array('name'),
            'join' => array('leads','lead_start','table_from_id','field_from','table_to_id','field_to'),
            'comment' => array('comment')
        );

        $obj = $this;

        //make $select_fields suitable for select statements
        foreach($this->table_fields as $table => $fields){
            $this->select_fields[$table] = array_map(
                function($t) use ($obj,$table) {return $obj->table_aliases[$table].'.'.$t;},
                $this->table_fields[$table]
            );
        }

    }

    ##
    #Inserts names of the database that the user is allowed to work on.
    #Returns the number of names added
    ##
    public function insertDatabaseNames($db_names){

        $this->db->delete("table_schema");//deletes all the database names that exists already

        $rows_added = 0;
        foreach($db_names as $db_name){
            $rows_added += $this->db->insert("table_schema",array('name'  => $db_name));
        }
        return $rows_added;
    }

    ##
    #Selects all of the names of the databases inserted
    ##
    public function selectDatabaseNames(){

        #$this->db->setFetchMode(Zend_Db::FETCH_COLUMN);#support for this is very sketchy
        $select = $this->db->select()->from('table_schema');
        $ass_array = $this->db->fetchAll($select);
        $result = array();
        foreach($ass_array as $row){
            $result[] = $row['name'];
        }
        return $result;
        
    }

    ##
    #Injects the id of the database to work on
    #Uses this id for all further queries unless overridden
    #The id is the primary key from table_schema
    ##
    public function setCurrentDatabase($new_db_id){
        $this->curr_db_id = $new_db_id;
    }

    ##
    #Selects all of the grid object information for a selected database
    ##
    public function selectAllObjects($db_id = null){

        if(!isset($db_id)){$db_id = $this->curr_db_id;}//use the database already passed in if it's given

        $select = $this->db->select()
        ->from(array('go' => 'grid_object'),$this->select_fields['object'])
        ->join(array('gt' => 'grid_table'),'go.id = gt.grid_object_id', $this->select_fields['table'])
        ->join(array('gj' => 'grid_join'),'go.id = gj.grid_object_id', $this->select_fields['join'])
        ->join(array('gc' => 'grid_comment'),'go.id = gc.grid_object_id', $this->select_fields['comment'])
        ->where('go.table_schema_id = ?',$db_id);
        
        $result = $this->db->fetchAssoc($select);

        //todo: collect table_ids, just for grid_tables
        //$table_ids[$row['name']] = $row['id'];//set the real grid_object_id

        return $result;

        #$query = "
            #select
            #gj.leads,gj.lead_start,
            #gj.table_from_id,gj.field_from,gj.table_to_id,gj.field_to,
            #$go_fields
            #from grid_join gj,grid_object go
            #where go.id = gj.grid_object_id
            #and go.table_schema_id = " . $table_schema_id;
        #$result = $mysqli->query($query) or trigger_error('Query failed: ' . $mysqli->error, E_USER_ERROR);
        #if($result->num_rows > 0) {
            #while($row = $result->fetch_assoc()) {
            #$grid_info[stripslashes($row['id'])] = $row;
            #}
        #}
    }

    //these next two function are very similar.  merge them??

    ##
    #Inserts any grid object
    #Returns the number of records effected
    ##
    protected function insertObject($fields){
        if(!in_array($fields->type,$this->object_types)){
            throw Exception("type of this object is invalid, can't insert it");
        }
        unset($fields['id']);//remove it if it's set so we can detect that it gets set.
        $num_inserted = $this->db->insert('grid_object',$this->assoc_subset($fields,$this->select_fields['object']));
        $fields['id'] = $this->db->lastInsertId();
        if(!isset($fields->id)){
            throw Exception("insert object failed");
        } else {
            $num_inserted = $this->db->insert('grid_'.$fields['type'],$this->assoc_subset($fields,$this->select_fields[$fields->type]));
            if(!isset($num_inserted)){
                throw Exception("insert {$fields['type']} object failed");
            }
            return $num_inserted;
        }
    }

    ##
    #Updates any grid object
    #Returns the number of records effected
    ##
    protected function updateObject($fields){
        if(!in_array($fields['type'],$this->object_types)){
            throw Exception("type of this object is invalid, can't update it");
        }
        $num_updated = $this->db->update('grid_object',$this->assoc_subset($fields,$this->select_fields['object']));
        if(!isset($num_updated) || $num_updated <= 0){
            throw Exception("failed to update");
        } else {
            $num_updated = $this->db->update('grid_'.$fields['type'],$this->assoc_subset($fields,$this->select_fields[$fields['type']]),array('grid_object_id = ?',$fields['id']));
            if(!isset($num_updated)){
                throw Exception("update {$fields['type']} object failed");
            }
            return $num_udpated;
        }
    }

    ##
    #Saves any grid object.  This means an update if it exists and an insert if it doesn't.
    #Returns the number of records effected
    ##
    public function saveObject($fields){//if 'id' in $fields exists then updates it, else creates it
  
        if(!isset($fields['id'])){
            return $this->insertObject($fields);//creates object and adds new $field->id
        } else {
            return $this->updateObject($fields);
        }

    }


    ##
    #Utilty function to get a subset of a hash.  $picked has the names of the keys to pick out
    ##
    public function assoc_subset($hash,$picked){
        $res = array();
        foreach($picked as $key){
            if(isset($hash[$key])){
                $res[$key] = $hash[$key];
            } else {
                //something in picked that isn't in hash.
                //error?
            }
        }
        return $res;
    }

    
}

/*

      //get the grid tables
      $query = "select gt.name,$go_fields from grid_table gt,grid_object go where go.id = gt.grid_object_id and go.table_schema_id = " . $table_schema_id;
      $result = $mysqli->query($query) or trigger_error('Query failed: ' . $mysqli->error, E_USER_ERROR);
      if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
          $table_ids[$row['name']] = $row['id'];//set the real grid_object_id
          $grid_info[stripslashes($row['id'])] = $row;
        }
      }

      //get the grid comments
      $query = "select gc.comment,$go_fields from grid_comment gc,grid_object go where go.id = gc.grid_object_id and go.table_schema_id = " . $table_schema_id;
      $result = $mysqli->query($query) or trigger_error('Query failed: ' . $mysqli->error, E_USER_ERROR);
      if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
          $grid_info[stripslashes($row['id'])] = $row;
        }
      }

      $jarray = array("table_ids" => $table_ids,"grid_info" => $grid_info);
    }
*/
