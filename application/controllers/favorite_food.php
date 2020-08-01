<?php 
use Restserver \Libraries\REST_Controller ; 
Class favorite_food extends REST_Controller{

    public function __construct(){ 
        header('Access-Control-Allow-Origin: *'); 
        header("Access-Control-Allow-Methods: GET, OPTIONS, POST, DELETE"); 
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding"); 
        parent::__construct(); 
        // $this->load->model('favorite_food_Model'); 
        $this->load->library('form_validation'); 
        $this->load->helper(['jwt','authorization']);
    } 
    public function index_get($username = null){
        $this->db->select('f.nama_makanan , f.alamat , f.jenis_makanan , f.review , f.id , f.foto');
        $this->db->from('food f');
        $this->db->join('favorite_food u','u.id = f.id');
        $this->db->where('u.username',$username);
        $query = $this->db->get()->result();
        return $this->returnData($query, false); 
    } 
    public function index_post(){ 
        $username = $this->post('username');
        $id = $this->post('id');
        $data = ['username'=>$username,'id'=>$id];
        $this->db->insert('favorite_food', $data);
    } 
    public function index_delete($id){ 
        $username = $this->get('username');
        $id = $this->get('id');
        $where=['id'=>$id,'username'=>$username];
        $query=$this->db->select('*')->where($where)->get('favorite_food')->row();
        return $this->returnData($id, false);
    }
    public function multi_get(){
        $username = $this->get('username');
        $id = $this->get('id');
        $where=['id'=>$id,'username'=>$username];
        $query=$this->db->delete('favorite_food',$where);
        return $this->returnData('berasil', false);

    } 
    public function returnData($msg,$error){ 
        $response['error']=$error; 
        $response['message']=$msg; 
        return $this->response($response); 
    } 
    
}