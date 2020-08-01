<?php 
use Restserver \Libraries\REST_Controller ; 
Class Makanan extends REST_Controller{

    public function __construct(){ 
        header('Access-Control-Allow-Origin: *'); 
        header("Access-Control-Allow-Methods: GET, OPTIONS, POST, DELETE"); 
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding,Authorization"); 
        parent::__construct(); 
        $this->load->model('MakananModel'); 
        $this->load->library('form_validation'); 
        $this->load->helper(['jwt','authorization']);
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
    
    
    public function index_get($username = null){ 
        $data = $this->verify_request();
        $status = parent::HTTP_OK;
        $username = $this->get('username');
        if($data['status'] == 401){
            return $this->returnData($data['msg'], true);
        }else{
            if($username == null){
                return $this->returnData($this->db->get('food')->result(), false); 
            }
            else{
                // $this->db->select('*');
                // $this->db->from('food');
                // $this->db->where('username',$username);
                // $querry= $this->db->get()->result();
                return $this->returnData($this->db->get_where('food', array('username' => $username))->result(),false);
            }
        }
    } 
    public function index_post($id = null){ 
        $validation = $this->form_validation; 
        $rule = $this->MakananModel->rules(); 
        if($id == null){ 
            array_push($rule); 
        }  
        $validation->set_rules($rule); 
        if (!$validation->run()) { 
            return $this->returnData($this->form_validation->error_array(), true); 
        } 
        $user = new MakananData(); 
        $user->username = $this->post('username');
        $user->nama_makanan = $this->post('nama_makanan');
        $user->alamat = $this->post('alamat');
        $user->jenis_makanan = $this->post('jenis_makanan'); 
        $user->review = $this->post('review');
        $user->foto = $this->_uploadImage(); 
        if($id == null){ 
            $response = $this->MakananModel->store($user);
        }else{ 
            $response = $this->MakananModel->update($user,$id); 
        } 
        return $this->returnData($response['msg'], $response['error']); 
    } 
    public function index_delete($id = null){ 
        if($id == null){ 
            return $this->returnData('Parameter Id Tidak Ditemukan', true); 
        } 
        $response = $this->MakananModel->destroy($id); 
        return $this->returnData($response['msg'], $response['error']); 
    } 
    public function returnData($msg,$error){ 
        $response['error']=$error; 
        $response['message']=$msg; 
        return $this->response($response); 
    }
    private function _uploadImage()
    {
        $config['upload_path']          = './asset/food_pict/';
        $config['allowed_types']        = 'gif|jpg|png';
        $config['file_name']            = $this->post('nama_makanan');
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
    
} 
Class MakananData{ 
    public $nama_makanan; 
    public $alamat; 
    public $jenis_makanan; 
    public $review;
    public $foto;
    public $username;
}