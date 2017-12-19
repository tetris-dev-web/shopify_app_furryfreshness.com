<?php
class Result_model extends Master_model
{
    protected $_tablename = 'result';
    
    function __construct() {
        parent::__construct();
    }
    
    public function getStatis( $from = '', $to = '' )
    {
        $arrReturn = array(
            'list' => array(),
            'statis' => array(),
            'numOrder' => 0,
            'numQA' => 0,
        );
        
        // Get the data from popup show
        $this->db->where( 'shop', $this->_shop );
        if( $from != '' ) $this->db->where( 'checkout_date >= \'' . $from . '\'' );
        if( $to != '' ) $this->db->where( 'checkout_date <= \'' . $to . ' 23:59:59\'' );
        
        $this->db->order_by( 'checkout_date DESC' );
        $query = $this->db->get( $this->_tablename );
        
        // Get the number of orders
        $arrReturn['numOrder'] = $query->num_rows();
        
        // Get the statis data
        if( $query->num_rows() > 0 )
        foreach( $query->result() as $row  )
        {
            $qa = json_decode( json_encode( json_decode( $row->qa ) ), true );

            // Keep the data array
            $arrReturn['list'][] = array(
                'order_id' => $row->order_id,
                'order_name' => $row->order_name,
                'customer_name' => $row->customer_name,
                'coupon' => $row->coupon,
                'checkout_date' => $row->checkout_date,
                'qa' => $qa,
            );
            
            // Get the statis
            if( is_array( $qa ) )
            {
                // Get the number of qas
                $arrReturn['numQA'] ++;
                
                foreach( $qa as $question_id => $answer_id )
                {
                    if( isset($arrReturn['statis'][$question_id][$answer_id]) )
                        $arrReturn['statis'][$question_id][$answer_id] ++;
                    else
                        $arrReturn['statis'][$question_id][$answer_id] = 1;
                }
            }
        }
        
        // Sort by date
        ksort($arrReturn['statis']);

        return $arrReturn;
    }
    
    /**
    * Update database with qa
    *     
    * @param mixed $order_id
    * @param mixed $qa
    */
    public function addQA( $order_id, $coupon, $qa )
    {
        $this->db->where( 'shop', $this->_shop );
        $this->db->where( 'order_id', $order_id );
        
        $data = array(
            'coupon' => json_encode( $coupon ),
            'qa' => json_encode( $qa ),
        );
        
        $this->db->update( $this->_tablename, $data );
    }
}  
?>