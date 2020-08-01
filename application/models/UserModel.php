<?php 
use PHPMailer \PHPMailer\PHPMailer;
use PHPMailer \PHPMailer\Exception;
// use PHPMailer \PHPMailer\SMTP;
    require 'vendor/autoload.php';
    // require '../../vendor/autoload.php';
    // require '../../vendor/phpmailer/phpmailer/src/Exception.php';
    // require '../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
    // require '../../vendor/phpmailer/phpmailer/src/SMTP.php';
class UserModel extends CI_Model 
{ 
    private $table = 'users';
    public $username; 
    public $email; 
    public $password;
    public $foto; 
    public $rule = [ 
        [ 
            'field' => 'email', 
            'label' => 'email', 
            'rules' => 'required' 
        ], 
    ]; 
    public function Rules() { return $this->rule; } 
    
    public function getAll() { 
        return $this->returnData($this->db->get('users')->result(), false);
    }
    public function getbyid($username){
        return $this->db->select('*')->where(array('username' => $username))->get($this->table)->row();
    } 
    
    public function store($request) { 
        $this->username = $request->username; 
        $this->email = $request->email; 
        $this->password = password_hash($request->password, PASSWORD_BCRYPT);
        $this->foto = $request->foto;
        $this->veriftoken = $request->veriftoken;
        $this->isverif=$request->isverif;
        if($this->db->insert($this->table, $this)){ 
            return ['msg'=>'Berhasil','error'=>false];
        } 
        return ['msg'=>'Gagal','error'=>true]; 
    } 
    public function update($request,$username) { 
        $updateData = ['email' => $request->email, 'password' =>password_hash($request->password, PASSWORD_BCRYPT),'foto'=>$request->foto]; 
        if($this->db->where('username',$username)->update($this->table, $updateData)){ 
            return ['msg'=>'Berhasil','error'=>false]; 
        } 
        return ['msg'=>'Gagal','error'=>true]; 
    } 
        
    public function destroy($username){ 
        if (empty($this->db->select('*')->where(array('username' => $username))->get($this->table)->row())) 
        return ['msg'=>'username tidak ditemukan','error'=>true]; 
        if($this->db->delete($this->table, array('username' => $username))){ 
            return ['msg'=>'Berhasil','error'=>false]; 
        } 
        return ['msg'=>'Gagal','error'=>true]; 
    }
    public function verify($request){
        $user = $this->db->select('*')->where(array('username' => $request->username))->get($this->table)->row_array();
        if(!empty($user) && password_verify($request->password , $user['password']) && $user['isverif'] != 0) {
            return $user;
        } else {
            return false;
        }
    }
    public function verifemail($email,$token){
        $where = ['email'=>$email,'veriftoken'=>$token];
        $update = ['isverif'=>1];
        if($this->db->where($where)->update($this->table,$update)){
            
        }
    }
    
} 
?>