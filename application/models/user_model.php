<?php
class User_model extends CI_Model {
    function __construct() {
        parent::__construct();
    }
    
    //save_user_signin() accepts the username and loginstatus, and store the login activity
	function save_user_signin($username, $loginstatus){
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
       } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_FORWARDED'])) {
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } else if (isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = 'UNKNOWN';
          }
        
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $os_platform  = "Unknown OS Platform";

        $os_array     = array(
                      '/windows nt 10/i'      =>  'Windows 10',
                      '/windows nt 6.3/i'     =>  'Windows 8.1',
                      '/windows nt 6.2/i'     =>  'Windows 8',
                      '/windows nt 6.1/i'     =>  'Windows 7',
                      '/windows nt 6.0/i'     =>  'Windows Vista',
                      '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                      '/windows nt 5.1/i'     =>  'Windows XP',
                      '/windows xp/i'         =>  'Windows XP',
                      '/windows nt 5.0/i'     =>  'Windows 2000',
                      '/windows me/i'         =>  'Windows ME',
                      '/win98/i'              =>  'Windows 98',
                      '/win95/i'              =>  'Windows 95',
                      '/win16/i'              =>  'Windows 3.11',
                      '/macintosh|mac os x/i' =>  'Mac OS X',
                      '/mac_powerpc/i'        =>  'Mac OS 9',
                      '/linux/i'              =>  'Linux',
                      '/ubuntu/i'             =>  'Ubuntu',
                      '/iphone/i'             =>  'iPhone',
                      '/ipod/i'               =>  'iPod',
                      '/ipad/i'               =>  'iPad',
                      '/android/i'            =>  'Android',
                      '/blackberry/i'         =>  'BlackBerry',
                      '/webos/i'              =>  'Mobile'
                );

        foreach ($os_array as $regex => $value)
            if (preg_match($regex, $user_agent))
                    $os_platform = $value;
            
            $browser        = "Unknown Browser";

        $browser_array = array(
                        '/msie/i'      => 'Internet Explorer',
                        '/firefox/i'   => 'Firefox',
                        '/safari/i'    => 'Safari',
                        '/chrome/i'    => 'Chrome',
                        '/edge/i'      => 'Edge',
                        '/opera/i'     => 'Opera',
                        '/netscape/i'  => 'Netscape',
                        '/maxthon/i'   => 'Maxthon',
                        '/konqueror/i' => 'Konqueror',
                        '/mobile/i'    => 'Handheld Browser'
                 );

        foreach ($browser_array as $regex => $value)
            if (preg_match($regex, $user_agent))
                $browser = $value;
        $token = '84d9300a2bfb6a';
        $details = "IP Address: ".$ipaddress;
        $details = $details.", Browser: ".$browser;
        $details = $details.", OS: ".$os_platform;
        
        if($ipaddress!='127.0.0.1' && $ipaddress!='localhost'){
            $json     = file_get_contents("https://ipinfo.io/$ipaddress/geo?token=$token");
            $json     = json_decode($json, true);
            if( isset( $json['country'] ) ){
                $details = $details.", Country: ".$json['country'];
            }
            if( isset( $json['region'] ) ){
                $details = $details.", Region: ".$json['region'];
            }
            if( isset( $json['city'] ) ){
                $details = $details.", City: ".$json['city'];
            }
            if( isset( $json['loc'] ) ){
                $details = $details.", Location: ".$json['loc'];
            }
            if( isset( $json['timezone'] ) ){
                $details = $details.", Timezone: ".$json['timezone'];
            }
        }

        $data=array(
            'username'=>$username,
            'signin_date_time'=>date("Y-m-d H:i:s"),
            'is_success'=>$loginstatus,
            'details'=>$details
        );
        $this->db->trans_start();
        $this->db->insert('user_signin',$data);
        $this->db->trans_complete();
        if($this->db->trans_status() === FALSE){
            return false;
        }
        else return true;
    }

    function login($username, $password){
        $this->db->select('*')
            ->from('user')
            ->where('username', $username)
            ->where('password', MD5($password));
        $query = $this->db->get();
        if($query)
        {
          return $query->row();
        }
        else
        {
          return false;
        }
    }

    function change_password($user_id) {
        //select the old password from the database
       $this->db->select('password')->from('user')->where('user_id',$user_id);
       $query=$this->db->get();
       $password=$query->row();
       $form_password=$this->input->post('old_password'); //get the old password from the form
       if($password->password==md5($form_password)){ //match both the old passwords
           $this->db->where('user_id',$user_id); //search for the user in db
           if($this->db->update('user',array('password'=>md5($this->input->post('password'))))){ 
               //if the user table has been updated successfully, return true else false.
               return true;
               }
           else return false;
       }
       else return false; //if the old password entered doesn't match the database password, return false.
    }

    //user_function() takes user ID as parameter and returns a list of all the functions the user has access to.
	function user_function($user_id){
		$this->db->select('user_function_id,user_function,add,edit,view,remove')
            ->from('user')
            ->join('user_function_link','user.user_id=user_function_link.user_id')
            ->join('user_function','user_function_link.function_id=user_function.user_function_id')
            ->where('user_function_link.user_id',$user_id)
            ->where('user_function_link.active','1');
		$query=$this->db->get();
		
		return $query->result();
	}

    // get all parites user associated with
    function user_parties($user_id) {
        $this->db->select('party_id')
            ->where('user_id', $user_id)
            ->from('user_party_link');
		$query=$this->db->get();
		
		return $query->result();
    }
}