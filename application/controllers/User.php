<?php 
use Restserver \Libraries\REST_Controller ; 

Class User extends REST_Controller{

    public function __construct(){ 
        header('Access-Control-Allow-Origin: *'); 
        header("Access-Control-Allow-Methods: GET, OPTIONS, POST, DELETE"); 
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding,Authorization"); 
        parent::__construct(); 
        $this->load->model('UserModel');
        $this->load->library("Phpmailer_library");
        $this->load->library('form_validation');
        $this->load->helper(['jwt','authorization']);
    } 
    public function index_get($username = null){
        $data = $this->verify_request();
        $status = parent::HTTP_OK;
        $username = $this->get('username');
        if($data['status'] == 401){
            return $this->returnData($data['msg'], true);
        }else{
            if($username == null){
                return $this->returnData($this->db->get('users')->result(), false);
            }
            else{
                return $this->returnData($this->db->select('*')->where(array('username' => $username))->get('users')->row(),false); 
            }    
        }
    }
    public function index_post($username = null){ 
        $validation = $this->form_validation; 
        $rule = $this->UserModel->rules(); 
        if($username == null){ 
            array_push($rule,[ 
                'field' => 'password', 
                'label' => 'password', 
                'rules' => 'required' 
            ],
            [ 
                'field' => 'email', 
                'label' => 'email', 
                'rules' => 'required|valid_email|is_unique[users.email]' 
            ] ); 
        } else{ 
            array_push($rule, [ 
                'field' => 'email', 
                'label' => 'email', 
                'rules' => 'required|valid_email' 
            ] ); 
        } 
        $validation->set_rules($rule); 
        if (!$validation->run()) { 
            return $this->returnData($this->form_validation->error_array(), true); 
        } 
        $user = new UserData();
        $emailnyah = $this->post('email');
        $user->username = $this->post('username'); 
        $user->password = $this->post('password'); 
        $user->email = $this->post('email');
        $user->foto = $this->_uploadImage();
        $token='1234567890qwertyuiopasdfghjklzxcvbnm';
        $token=str_shuffle($token);
        $token=substr($token,0,10);
        $user->veriftoken = $token;
        $user->isverif=0;
        if($username == null){ 
            $response = $this->UserModel->store($user);
            $mail = new PHPMailer\PHPMailer\PHPMailer();
            $linkVerif ='http://localhost:85/TUBESPAW/index.php/user/verifemail?email=' .$user->email.'&token='.$user->veriftoken;   
            //$mail->SMTPDebug = 2;
            $mail->IsSMTP();
            $mail->SMTPSecure = 'tls';
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = "Kulinerkuy.awe@gmail.com";
            $mail->Password = "Pawawe123";
            $mail->Port = 587;

            $mail->setFrom('Kulinerkuy.awe@gmail.com', 'Kuliner Kuy');
            $mail->addAddress($emailnyah, 'awe');
            $mail->Subject = "[KUYLIner] Please verify your email";
            $mail->isHTML(true);
            $mail->Body = '
                You have registered to Kuliner Kuy Website.<br/>
                Please click the link below to verify your email.
                <br/><br/>
                <a href="'.$linkVerif.'">Cick here</a>
            ';

            if($mail->send()){
                return $this->returnData("yeay","false");  
            }else{
                return $this->returnData("yeay","false");
            }
        }else{ 
            $response = $this->UserModel->update($user,$username);
            return $this->returnData($response['msg'], $response['error']);  
        }
    } 
    public function index_delete($username = null){ 
        if($username == null){ 
            return $this->returnData('Parameter Username Tidak Ditemukan', true); 
        } 
        $response = $this->UserModel->destroy($username); 
        return $this->returnData($response['msg'], $response['error']); 
    } 
    public function returnData($msg,$error){         
        $response['error']=$error;         
        $response['message']=$msg;
        return $this->response($response);     
    }
    private function verify_request()
    {
    // Get all the headers
    $headers = $this->input->request_headers();
    if(!empty($headers['Authorization'])){
        $header = $headers['Authorization'];
    }else{
        $status = parent::HTTP_UNAUTHORIZED;
        $response = ['status' => $status, 'msg' => 'Unauthorized Access!'];
        return $response;
    }
    // $token = explode(" ",$header)[1];
    try {
        // Validate the token
        // Successfull validation will return the decoded user data else returns false
        $data = AUTHORIZATION::validateToken($header);
        if ($data === false) {
            $status = parent::HTTP_UNAUTHORIZED;
            $response = ['status' => $status, 'msg' => 'Unauthorized Access!'];
            // $this->response($response, $status);
            // exit();
        } else {
            $response = ['status' => 200 , 'msg' => $data];
        }
        return $response;
    } catch (Exception $e) {
        // Token is invalid
        // Send the unathorized access message
        $status = parent::HTTP_UNAUTHORIZED;
        $response = ['status' => $status, 'msg' => 'Unauthorized Access! '];
        return $response;
    }
    }
    private function _uploadImage()
    {
        $config['upload_path']          = './asset/profile_pict/';
        $config['allowed_types']        = 'gif|jpg|png';
        $config['file_name']            = $this->post('username');
        $config['overwrite']   = true;
        $config['max_size']             = 1024; // 1MB
        // $config['max_width']            = 1024;
        // $config['max_height']           = 768;

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('foto')) {
            return $this->upload->data("file_name");
        }else{
            return "default.jpg";
        }
    }
    public function verifemail_get(){
        $email=$this->get('email');
        $token=$this->get('token');
        $response = $this->UserModel->verifemail($email,$token);

    }  
} 
Class UserData{ 
    public $username; 
    public $password; 
    public $email;
    public $foto;
    public $veriftoken;
    public $isverif;
}