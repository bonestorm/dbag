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
            'object' => array('id','table_schema_id','type','x','y','width','height'),
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
    public function getDatabaseNames(){

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

    protected function getTableSchemaId($database){

      //get the table_schema_id from the database name
      $select = $this->db->select('table_schema_id')->from('table_schema')->where('id = ?',$database);
      $table_schema_id = $this->db->fetchOne($select);
      if(!isset($table_schema_id)){
        throw Exception("failed to find the database id from a database name");
      }
      return $table_schema_id;

    }

    ##
    #Selects all of the grid object information for a selected database
    ##
    public function getAllObjects($db_id = null){

        if(!isset($db_id)){$db_id = $this->curr_db_id;}//use the database already passed in if it's given

        if(!is_int($db_id)){
          $db_id = $this->getTableSchemaId($db_id);
        }

        $select = $this->db->select()
        ->from(array('go' => 'grid_object'),$this->select_fields['object'])
        ->joinLeft(array('gt' => 'grid_table'),'go.id = gt.grid_object_id', $this->select_fields['table'])
        ->joinLeft(array('gj' => 'grid_join'),'go.id = gj.grid_object_id', $this->select_fields['join'])
        ->joinLeft(array('gc' => 'grid_comment'),'go.id = gc.grid_object_id', $this->select_fields['comment'])
        ->where('go.table_schema_id = ?',$db_id);
        
        $result = $this->db->fetchAssoc($select);

        #removes all the empty entries due to the outer joins.  leaves fields in the grid_object and grid_<type>
        #this would be so much easier in Perl
        $short_result = array();
        foreach($result as $row){
            $short_row = array();
            foreach($row as $key => $val){
                if(in_array($key,$this->table_fields['object']) || in_array($key,$this->table_fields[strtolower($row['type'])])){//isset($val)){// && !is_null($val)){
                    $short_row[$key] = $val;
                }
            }
            $short_result[] = $short_row;
        }

        return $short_result;//$select->__toString();

        //todo: collect table_ids, just for grid_tables
        //$table_ids[$row['name']] = $row['id'];//set the real grid_object_id


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
    #Returns the id of the new grid object
    ##
    public function insertObject($fields){
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
            return $fields['id'];
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
    #Deletes any grid object
    #Returns the number of records effected
    ##
    protected function deleteObject($fields){

        //get type if not passed in
        if(!isset($fields['type'])){
          $select = $this->db->select('type')->from('grid_object')->where('id = ?',$fields['id']);
          $fields['type'] = $this->db->fetchOne($select);
        }

        if(!isset($fields['type'])){
            throw Exception("type is a required field to delete a grid object, it is not provided");
        }

        if(!in_array($fields['type'],$this->object_types)){
            throw Exception("type of this object is invalid, can't delete it");
        }
        $num_deleted = $this->db->delete('grid_'.$fields['type'],"grid_object_id = '".$this->db->quote($fields['id'])."'");
        if(!isset($num_delete) || $num_delete <= 0){
            throw Exception("failed to delete object");
        } else {
            $num_delete = $this->db->delete('grid_object',"id = '".$this->db->quote($fields['id'])."'");
            if(!isset($num_delete)){
                throw Exception("delete {$fields['type']} object failed");
            }
            return $num_delete;
        }
    }


    ##
    #Saves any grid object.  This means an update if it exists and an insert if it doesn't.
    #Returns the number of records effected
    ##
    public function saveObject($fields,$database = null){

        if(isset($fields['type'])){
          $fields['type'] = strtoupper($field['type']);
        }

        //'id' is the grid object id,'table_schema_id' is the database id

        //if 'id' in $fields exists then updates it, else creates it

        if(!isset($fields['id']) && !is_null($fields['id'])){

            //see if we need to get the table_schema_id, get it if we do
            if(!isset($fields['table_schema_id'])){
              if(!isset($database)){
                throw Exception("not given any database information for the grid object insert");
              } else {
                $fields['table_schema_id'] = $this->getTableSchemaId($database);
              }
            }

            //insert it
            $new_id = $this->insertObject($fields);//creates object and adds new $field->id
            return array('action' => 'inserted', 'id' => $new_id);

        } else {

            //update it
            //!!should check this return value
            $this->updateObject($fields);
            return array('action' => 'updated', 'id' => $fields['id']);

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
