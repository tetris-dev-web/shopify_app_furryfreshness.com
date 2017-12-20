<?php
class Coupon_model extends Master_model
{
    protected $_tablename = 'coupon';
    protected $_tablename_usage = 'result';
    
    function __construct() {
        parent::__construct();
    }
    
    public function add(){
        $data = array(
            'code' => $this->input->post('code'),
            'create_date' => date( $this->config->item('CONST_DATE_FORMAT')),
        );
        
        return parent::add( $data );
    }
    
    function getCoupon()
    {
        $return = '';
        $arr = array();
        
        $query = parent::getList( '', '', 'code' );
        
        // Get the query
        if( $query->num_rows() > 0 )
        foreach( $query->result() as $row ) $arr[] = $row->code;
        
        // Get the random value
        if( count( $arr ) > 0 ) $return = $arr[ rand( 0, count( $arr ) - 1 ) ];
        
        return $return;
    }
    
    function getList()
    {
        $this->db->select( $this->_tablename . '.*, count( ' . $this->_tablename_usage . '.id ) as cnt' );
        $this->db->from( $this->_tablename );
        $this->db->join( $this->_tablename_usage, $this->_tablename . '.code = ' . $this->_tablename_usage . '.coupon', 'LEFT' );
        $this->db->group_by( $this->_tablename . '.code' );
        $this->db->where( $this->_tablename . '.shop', $this->_shop );
        
        return $this->db->get();
    }
}  
?>