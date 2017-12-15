<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Coupon extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('Coupon_model');
    }
    
    public function index(){
        $this->is_logged_in();

        $this->manage();
    }

    function manage(){
        // Check the login
        $this->is_logged_in();

        $data['query'] =  $this->Coupon_model->getList();
        
        $this->load->view('view_header');
        $this->load->view('view_coupon', $data);
        $this->load->view('view_footer');
    }
   
    function del(){
        $id = $this->input->get_post('del_id');
        $returnDelete = $this->Coupon_model->delete( $id );
        if( $returnDelete === true ){
            $this->session->set_flashdata('falsh', '<p class="alert alert-success">One coupon is deleted successfully</p>');    
        }
        else{
            $this->session->set_flashdata('falsh', '<p class="alert alert-danger">Sorry! deleted unsuccessfully : ' . $returnDelete . '</p>');    
        }
        
        redirect('coupon');
        exit;
    }
   
    function add(){
        $this->form_validation->set_rules('code', 'Coupon Code', 'required');
        
        if ($this->form_validation->run() == FALSE){       
            echo validation_errors('<div class="alert alert-danger">', '</div>');
            exit;
        }
        else{
            if($this->Coupon_model->add()){
                echo '<div class="alert alert-success">This Coupon is added successfully</div>';
                exit;
            }
            else{
                echo '<div class="alert alert-danger">Sorry ! something went wrong </div>';
                exit;
            }
        }
    }
        
    function update( $type, $pk ){
        $data = array();
        
        switch( $type )
        {
            case 'code' : $data['code'] = $this->input->post('value'); break;
        }
        $this->Coupon_model->update( $pk, $data );
    }
}             