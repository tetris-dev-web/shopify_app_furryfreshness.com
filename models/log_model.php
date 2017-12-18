<?php
class Log_model
{
    private $_tablename = 'log';
    
    function __construct() {
    }

    /**
    * Get the Style information and error message
    * 
    */
    public function getList(  )
    {
        $row = array();
        
        $sql = 'SELECT * FROM ' . $this->_tablename;
        $sql .= ' ORDER BY id DESC';
        
        $res = db_query( $sql );
        
        return $res;
    }
    
    /**
    * Add the log entry
    * 
    * @param mixed $post
    */
    public function add( $type, $action, $input, $message )
    {
        $sql = 'INSERT INTO ' . $this->_tablename . ' SET type = \'' . $type . '\'';
        $sql .= ', action = \'' . $action . '\'';
        $sql .= ', input = \'' . $input . '\'';
        $sql .= ', message = \'' . str_replace( "'", '', $message ) . '\'';
        $sql .= ', log_date = \'' . date(CONST_DATE_FORMAT) . '\'';
        
        // Apply to database        
        db_query( $sql );
    }
}
?>
