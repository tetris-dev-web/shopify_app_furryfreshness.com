<?php
class Master_model extends CI_Model
{
    protected $_tablename = "";
    protected $_shop = '';
    
    function __construct() {
        parent::__construct();
        
        $this->_shop = $this->session->userdata('shop');
        if( $this->_shop == '' ) $this->_shop = $this->config->item('PRIVATE_SHOP');
    }
    
    public function rewriteParam( $shop )
    {
        $this->_shop = $shop;
    }
    
    /** Check the record is exist
    * $arr = array(
    *   'table1' => 'where cluase 1' ),
    *   'table2' => 'where cluase 2' ),
    *   'table3' => 'where cluase 3' ),
    * );
    * 
    * return : if one of them is exist, return true
    */
    protected function checkExist( $arrCheck )
    {
        $return = true;
        
        if( is_array( $arrCheck ) )
        {
            foreach( $arrCheck as $tablename => $item )
            {
                $sql = 'SELECT id FROM ' . $tablename . ' WHERE ' . $item;
                $query = $this->db->query($sql);

                // if any of one is exist, return true
                if( $query->num_rows() > 0 )
                {
                    $return = $tablename;
                    break;
                }
            }
        }
        
        return $return;
    }
    
    public function add( $data )
    {
        $data['shop'] = $this->_shop;
        $this->db->insert( $this->_tablename, $data);
        if($this->db->affected_rows()>0)
        {    
           return true;
        }
        else
        {
           return false;
        }
    }

    public function delete( $id )
    {
        $this->db->where('id', $id);
        $this->db->delete( $this->_tablename, array( 'id' => $id ) );
        if( $this->db->affected_rows() > 0 )
            return true;
        else
            return false;
    }

    public function update( $id, $data )
    {
        $this->db->where('id', $id);
        $this->db->update( $this->_tablename, $data);        
        
        if( $this->db->affected_rows() > 0 )
            return true;
        else
            return false;
    }

    /**
    * Get the list with the dedicated where and order by clase
    * 
    * @param mixed $where : where clause as string
    * @param mixed $order_by : order by clause
    */
    public function getList( $where = '', $order_by = '', $select = '*' )
    {
        // Build the sql statement
        $sql = 'SELECT ' . $select . ' FROM ' . $this->_tablename;
        $sql .= ' WHERE shop = \'' . $this->_shop . '\'';
        if( $where != '') $sql .= ' AND ' . $where;
        if( $order_by != '' )
            $sql .= ' ORDER BY ' . $order_by;
            
        $query = $this->db->query($sql);

        return $query;
    }
    
    /**
    * get the one record for relevant id
    *     
    * @param mixed $id
    */
    public function getInfo( $id )
    {
        $this->db->where( 'id', $id );
        $query = $this->db->get( $this->_tablename );
        $result = $query->result();
        
        return $result[0];
    }
    
    function uninstall()
    {
        $this->db->where( 'shop', $this->_shop );
        $this->db->delete( $this->_tablename );
    }
}  
?>
