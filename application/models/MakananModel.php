<?php 
defined('BASEPATH') OR exit('No direct script access allowed'); 
class MakananModel extends CI_Model 
{ 
    private $table = 'food'; 
    public $id; 
    public $nama_makanan; 
    public $alamat; 
    public $jenis_makanan; 
    public $review;
    public $foto;
    public $rule = [ 
        [ 
            'field' => 'nama_makanan', 
            'label' => 'nama_makanan', 
            'rules' => 'required',
        ],
        ['field' => 'alamat', 
        'label' => 'alamat', 
        'rules' => 'required' ],
        [
            'field' => 'jenis_makanan', 
        'label' => 'jenis_makanan', 
        'rules' => 'required'
        ],
        [
            'field' => 'review', 
        'label' => 'review', 
        'rules' => 'required'
        ]
    ]; 
    public function Rules() { return $this->rule; } 
    
    public function getAll() { return 
        $this->db->get('food')->result(); 
    } 
    
    public function store($request) { 
        $this->nama_makanan = $request->nama_makanan; 
        $this->alamat = $request->alamat;
        $this->jenis_makanan = $request->jenis_makanan; 
        $this->review = $request->review;
        $this->foto = $request->foto;
        $this->username = $request->username;
        if($this->db->insert($this->table, $this)){ 
            return ['msg'=>'Berhasil','error'=>false];
        } 
        return ['msg'=>'Gagal','error'=>true]; 
    } 
    public function update($request,$id) { 
        $updateData = ['nama_makanan'=>$request->nama_makanan,'alamat' => $request->alamat,
         'jenis_makanan' =>$request->jenis_makanan, 'review' => $request->review]; 
        if($this->db->where('id',$id)->update($this->table, $updateData)){ 
            return ['msg'=>'Berhasil','error'=>false]; 
        } 
        return ['msg'=>'Gagal','error'=>true]; 
    } 
        
    public function destroy($id){ 
        if (empty($this->db->select('*')->where(array('id' => $id))->get($this->table)->row())) 
        return ['msg'=>'Id tidak ditemukan','error'=>true]; 
        if($this->db->delete($this->table, array('id' => $id))){ 
            return ['msg'=>'Berhasil','error'=>false]; 
        } 
        return ['msg'=>'Gagal','error'=>true]; 
    } 
} 
?>