<?php
class Question_model extends Master_model
{
    protected $_tablename = 'question';
    
    function __construct() {
        parent::__construct();
    }
    
    function getQuestionTree( $live = false )
    {
        $CI =& get_instance();
        $CI->load->model( 'Answer_model' );
        $CI->Answer_model->rewriteParam( $this->_shop );
        
        // Get the question list
        $where = $live ? 'is_active = 1' : '';
        $queryQuestion = $this->Question_model->getList( $where, 'sort_order ASC');
        
        // Get the answer list
        $arrQuestion = array();
        if( $queryQuestion->num_rows() > 0 )
        foreach( $queryQuestion->result() as $row )
        {
            $arrQuestion[ $row->id ] = array(
                'question' => $row->question,
                'sort_order' => $row->sort_order,
                'is_active' => $row->is_active,
                'answer' => array(),
            );
            
            $queryAnswer = $CI->Answer_model->getList( $row->id, $live );
            
            if( $queryAnswer->num_rows() > 0 )
            foreach( $queryAnswer->result() as $rowAnswer )
            {
                $arrQuestion[$row->id]['answer'][ $rowAnswer->id ] = array(
                    'answer' => $rowAnswer->answer,
                    'sort_order' => $rowAnswer->sort_order,
                    'is_active' => $rowAnswer->is_active,
                    'is_correct' => $rowAnswer->is_correct,
                );
            }
        }
        
        return $arrQuestion;        
    }
}  
?>
