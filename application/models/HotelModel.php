<?php 
defined('BASEPATH') OR exit('No direct script access allowed'); 
class HotelModel extends CI_Model 
{ 
    private $table = 'hotel'; 
    public $id; 
    public $nama_hotel; 
    public $alamat; 
    public $bintang_hotel; 
    public $review;
    public $foto;
    public $rule = [ 
        [ 
            'field' => 'nama_hotel', 
            'label' => 'nama_hotel', 
            'rules' => 'required',
        ],
        ['field' => 'alamat', 
        'label' => 'alamat', 
        'rules' => 'required' ],
        [
            'field' => 'bintang_hotel', 
        'label' => 'bintang_hotel', 
        'rules' => 'numeric'
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
        $this->nama_hotel = $request->nama_hotel; 
        $this->alamat = $request->alamat;
        $this->bintang_hotel = $request->bintang_hotel; 
        $this->review = $request->review;
        $this->foto = $request->foto;
        $this->username = $request->username;
        if($this->db->insert($this->table, $this)){ 
            return ['msg'=>'Berhasil','error'=>false];
        } 
        return ['msg'=>'Gagal','error'=>true]; 
    } 
    public function update($request,$id) { 
        $updateData = ['nama_hotel'=>$request->nama_hotel,'alamat' => $request->alamat,
         'bintang_hotel' =>$request->bintang_hotel, 'review' => $request->review,'foto' =>$request->foto]; 
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