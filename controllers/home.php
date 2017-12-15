<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MY_Controller {

   public function __construct() {
       parent::__construct();
       $this->load->model('User_model');
   }

   public function index(){
        
      // APP Redirect
      if( $this->config->item('PUBLIC_MODE')  )
      {
        // Set the shop domain from session or get
        $shop_domain = $this->session->userdata( 'shop' ) != '' ? $this->session->userdata( 'shop' ) : '';
        if( $this->input->get('shop') != '' ) $shop_domain = $this->input->get('shop');
        
        // Get the access token from database
        $this->load->model( 'Shopify_model' );
        $access_token = $this->Shopify_model->getAccessToken( $shop_domain );

        // Check the cookie / Check the token is valid!!!
        if( $access_token == '' )
        {
          // If the acess_token is missing from database, then install the app
          if( $shop_domain == '' )
          {
            $data['shop'] = '';
            $this->load->view('view_newstore', $data );   
          }
          else
          {
            redirect( 'newstore/register?shop=' . $shop_domain );
          }
        }
        else
        {
          // Save the access token and shop domain
          $this->session->set_userdata( array( 
            'shop' => $shop_domain, 
            'access_token' => $access_token 
          ));
        }
      }
      else
      {
        $this->session->set_userdata( array( 
            'shop' => $this->config->item('PRIVATE_SHOP'), 
        ));
      }      
      
      if( $this->session->userdata( 'shop' ) != '' )
      {
        // Check Login
        $this->is_logged_in();

        redirect('statis');          
      }
   }
    
   public function login(){
        $this->load->helper('cookie');
        
        $this->form_validation->set_rules('username','Username','required');
        $this->form_validation->set_rules('password','Password','required');

        if ($this->form_validation->run() == FALSE){
            echo validation_errors ('<div class="alert alert-dismissable alert-danger"><small>', '</small></div>' );
        } 
        else
        {
            $name = $this->input->post('username');
            $password = $this->input->post('password');

            $loginCheck = $this->User_model->auth($name, $password);
            if( $loginCheck !== false ){
                
                // Set the cookie
                $this->input->set_cookie( $this->config->item('loginCookie'), $loginCheck );
            }
            else{
                echo'<div class="alert alert-dismissable alert-danger"><small>Please Check User Email or Password</small></div>' . $name;
            }
        }
    }
    
    public function logout()
    {
        $this->session->sess_destroy();
        delete_cookie();
        
        redirect('home' ,'refresh');
        exit;
    }
        
    public function sign_in()
    {
        $this->load->view('view_login');   
    }
        
    public function sign_up()
    {
      $this->load->view('view_register');  
    }
    
    public function sync()
    {
        $this->load->model( 'Shopify_model' );
        $this->load->model( 'Coupon_model' );
        $this->load->model( 'Order_model' );

        $cntNewOrder = 0;
        
        // Get the lastest day
        $last_day = $this->Order_model->getLastOrderDate();
        
        $param = 'status=any';
        if( $last_day != '' ) $param .= '&processed_at_min=' . urlencode( substr($last_day, 0, 10 ) );
        $action = 'orders.json?' . $param;

        // Retrive Data from Shop
        $orderInfo = $this->Shopify_model->accessAPI( $action );
        
        foreach( $orderInfo->orders as $order )
        {
            if( $this->Order_model->add( $order ) )
            {
                $this->Coupon_model->addUsage( $order );
                
                $cntNewOrder ++;
            }
        }

//        echo '<div class="alert alert-success">' . $cntNewOrder . ' order(s) are downloaded successfully</div>';
        return;
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */