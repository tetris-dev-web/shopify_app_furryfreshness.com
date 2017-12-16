<?php
class User_model extends Master_model
{
    protected $_tablename = 'user';
    function __construct() {
        parent::__construct();
    }

    private function setSessoin( $row )
    {
        $data = array(
            'username'=> $row->user_name,
            'id'=> $row->id,
            'logged_in'=>TRUE,
            'role'=>$row->role,
            'd_o_c'=>$row->d_o_c,
        );
        $this->session->set_userdata($data);
    }
    
    function auth($name,$password){
        $password = sha1($password);
        $this->db->where('user_name',$name);
        $this->db->where('password',$password);
        $this->db->where('shop',$this->_shop);
        $this->db->where('is_active',1);
        $query = $this->db->get( $this->_tablename );
        if($query->num_rows()==1){
            foreach ($query->result() as $row){
                $this->setSessoin( $row );
            }
            
            // Genearate the
            return $this->genCookie( $name, $password );
        }
        else{
            return FALSE;
        }
    }
    
    private function genCookie( $user_name, $password )
    {
        // Generate the cookie
        $cookie = md5( $user_name . $password . time() );
        
        // Set the cookie to the database
        $data = array(
            'cookie' => $cookie,
        );
        $this->db->where( 'user_name', $user_name );
        $this->db->update( $this->_tablename, $data );
        
        return $cookie;
    }
    
    /**
    * Check the Cookie value and set the session value
    * 
    * @param mixed $cookie
    */
    function checkCookie( $cookie )
    {
        $query = $this->db->get_where( $this->_tablename, 'cookie = \'' . $cookie . '\'');
        $result = $query->result();
        
        if( count( $result ) <= 0 ) return false;
        
        foreach( $result as $row )
        {
            $this->setSessoin( $row );
        }
        
        return true;
    }
    
    /**
    * Get the list of account
    * 
    * @param mixed $team_id
    */
    public function getList()
    {
        $where = 'shop = \'' . $this->_shop . '\'';
        
        return parent::getList( $where, 'id ASC' );
    }
    
    function createUser(){
        $name = $this->input->post('name');
        $password = sha1($this->input->post('cpassword'));
        $role = $this->input->post('role');
        $is_active = $this->input->post('is_active');
        $created = date("Y/m/d");
        
        $data = array(
            'user_name'=>$name,
            'password'=>$password,
            'role'=>$role,
            'is_active'=>$is_active,
            'd_o_c'=>$created,
            'shop'=>$this->_shop,
        );
        
        $this->db->insert( $this->_tablename, $data);
        if($this->db->affected_rows()>0){
            return true;
        }
        else{
            return FALSE;
        }
    }

    function install()
    {
        // Check the default user is exist
        $query = parent::getList( 'user_name = \'admin@test.com\''  );
        
        if( $query->num_rows() == 0 )
        {
            $data = array(
                'user_name' => 'admin@test.com',
                'password' => sha1('rkauqkf.'),
                'role' => 'admin',
                'is_active' => '1',
                'd_o_c'=>date("Y/m/d"),
            );
            
            parent::add($data);
        }
    }
}  
?>
