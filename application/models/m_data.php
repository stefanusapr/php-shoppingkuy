<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_data extends CI_Model {
	function tampil_data($table){
		return $this->db->get($table);
	}

	function data($number,$offset){
		return $query = $this->db->get('peserta',$number,$offset)->result();
	}

	function datafinish($number,$offset,$where){
		$this->db->where($where);
		return $query = $this->db->get('peserta',$number,$offset)->result();
	}

	function jumlah_data_finish($where){
		return $this->db->get_where('peserta',$where)->num_rows();
	}

	function jumlah_data(){
		return $this->db->get('peserta')->num_rows();
	}

	function cari_data($where,$table){
		return $this->db->get_where($table,$where);
	}

	function input_data($data,$table){
		$this->db->insert($table,$data);
	}
	
	function hapus_data($where,$table){
		$this->db->where($where); //meneyeleksi record
		$this->db->delete($table); //menghapus record yang di select
	}

	function edit_data($where,$table){
		return $this->db->get_where($table,$where);
	}

	function update_data($where, $data, $table){
		$this->db->where($where);
		$this->db->update($table,$data);
	}

	function getTicket($q){
	    $this->db->select('ID_TIKET');
	    $this->db->like('ID_TIKET', $q);
	    $query = $this->db->get('peserta');
	    if($query->num_rows() > 0){
	      foreach ($query->result_array() as $row){
	        	$row_set[] = htmlentities(stripslashes($row['ID_TIKET'])); //build an array
      		}
      		echo json_encode($row_set); //format the array into json data
    	}
  	}

  	function setPhoto($id){
  		$query = $this->db->query("INSERT INTO GAMBAR (ID_GAMBAR) 
  			VALUES ('$id') ON DUPLICATE KEY UPDATE  ID_GAMBAR='$id'");
  		return $query;
  	}

  	function replaceData($data,$table){
  		$this->db->replace($table, $data);
  	}
}
?>