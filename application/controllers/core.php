<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Core extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('m_data');
		$this->load->helper(array('url','string'));
	}

	public function index()
	{	
		$data['iklan'] = $this->m_data->tampil_data('iklan')->result();
		$this->load->view('index.html',$data);
	}

	function member($page,$wh=NULL){
		if ($page == 'productEdit') {
			$where = array('ID_PRODUK' => $wh);
			$data['iklan'] = $this->m_data->edit_data($where,'iklan')->result();
			$this->load->view('product-edit.html',$data);
		}else if ($page == 'productAdd') {
			$data['id'] = random_string('alnum',5).time();
			$this->load->view('jual.html',$data);
		}else if ($page == 'productList') {
			$user_data = $this->session->userdata('user');
			$where = array('ID_MEMBER' => $user_data['id_member']);
			$data['iklan'] = $this->m_data->edit_data($where,'iklan')->result();
			$this->load->view('product-list.html',$data);
		}else if($page == 'account'){
			$data = $this->session->userdata('user');
			$this->load->view('user-summary.html');
		}
	}

	function page($page){
		if ($page == 'cart') {
			$this->load->view('cart.html');
		} else if ($page == 'register') {
			$this->load->view('register.html');
		}
		
	}

	function tambah_aksi(){
			$id_m = $this->input->post('id_member');
			$id = $this->input->post('id_produk');
			$nama = $this->input->post('name');
			$price = $this->input->post('price');
			$jumlah = $this->input->post('quantity');
			$kategori = $this->input->post('category');
			$deskripsi = $this->input->post('desc');
			$gambar = $this->input->post('id_produk');

			$data = array(
				'ID_PRODUK' => $id,
				'NAMA_PRODUK' => $nama,
				'HARGA' => $price,
				'JUMLAH' => $jumlah,
				'KATEGORI' => $kategori,
				'DESKRIPSI' => $deskripsi,
				'ID_MEMBER' => $id_m
				);

			$this->m_data->replaceData($data,'iklan');
			redirect('core/member/productList');		
	}

	function update(){
		$nama = $this->input->post('name');
		$price = $this->input->post('price');
		$jumlah = $this->input->post('quantity');
		$kategori = $this->input->post('category');
		$deskripsi = $this->input->post('desc');
		$id = $this->input->post('id');

		$data = array(
			'NAMA_PRODUK' => $nama,
			'HARGA' => $price,
			'JUMLAH' => $jumlah,
			'KATEGORI' => $kategori,
			'DESKRIPSI' => $deskripsi 
			);
	
		$where = array(
			'ID_PRODUK' => $id
		);

		$this->m_data->update_data($where, $data,'iklan');
		redirect('core/member/productList');
	}

	function deleteProduct($id){
		$where = array('ID_PRODUK' => $id);
		$this->m_data->hapus_data($where,'iklan');
		redirect('core/member/productList');
	}
	
	function liveSearch(){
		if (isset($_GET['term'])){
      		$q = strtolower($_GET['term']);
      		$this->m_data->getTicket($q);
    	}
	}

	function manual(){
		$unique = $this->input->get('unique');
		$where = array('ID_TIKET' => $unique);
		$data = $this->m_data->cari_data($where,'peserta')->result();
		$this->output->set_content_type('application/json');
    	$this->output->set_output(json_encode($data));
    	return $data;
	}

	public function uploadPhoto($id){
	    $this->load->library('upload');
	    $config['upload_path'] = './assets/img/upload/product/';
	    $config['allowed_types']='gif|jpg|png|jpeg|bmp';
	    $config['file_name'] = $id;
	    $config['max_size'] = '20480';
	    $config['max_width']  = '20480';
        $config['max_height']  = '20480';
        $config['overwrite'] = TRUE;

	    $this->upload->initialize($config);

	    if ($_FILES['filefoto']['name']) {
	    	if ($this->upload->do_upload('filefoto')) {
	    		$gambar = $this->upload->data();
	    		$data = array(
			        'ID_PRODUK' => $id
				);
	    		$this->m_data->replaceData($data,'iklan');
	    	}
	    } else {
	    	return "Gagal";
	    }
	}

	public function insertCart($id,$harga,$nama){
 		$data = array(
		    'id'      => $id,
		    'qty'     => 1,
		    'price'   => $harga,
		    'name'    => str_replace('%20', ' ', $nama)
	    );
	 
	    // Insert the product to cart
	    $this->cart->insert($data);
	 
	    redirect('core/page/cart');
    } 

    public function removeCart($id){
	 
	    $this->cart->remove($id);
	 
	    redirect('core/page/cart');
    }

    public function plusCart($qty,$rowid)
	{
		$new = $qty + 1;
		$data = array(
	        'rowid' => $rowid,
	        'qty'   => $new
		);

		$this->cart->update($data);	
		redirect('core/page/cart');	
	}

	public function minCart($qty,$rowid)
	{
		$new = $qty - 1;
		$data = array(
	        'rowid' => $rowid,
	        'qty'   => $new
		);

		$this->cart->update($data);
		redirect('core/page/cart');		
	}

    function registerMember()
	{
		$nama = $this->input->post('nama');
		$email = $this->input->post('email');
		$nohp = $this->input->post('nohp');		
		$password = $this->input->post('passwd');

		$data = array(
				'NAMA' => $nama,
				'EMAIL' => $email,
				'NOHP' => $nohp,
				'PASSWD' => md5($password)
				);
		$this->m_data->input_data($data,'member');
		redirect(base_url());
	}

    function do_login(){
		  $email = $this->input->post('email');
		  $password = $this->input->post('passwd');

		  $this->db->where('email', $email);
		  $this->db->where('passwd', md5($password));
		  $query = $this->db->get('member');

		  if($query->num_rows() > 0){
		    $row = $query->row();
		    $data = array(
		    	'id_member'	=> $row->ID,
		        'nama' 		=> $row->NAMA,
		        'email' 	=> $row->EMAIL,
		        'nohp' 		=> $row->NOHP,
		        'logged_in' => TRUE
		        );
		    $this->session->set_userdata('user',$data);
		    redirect(base_url());
		  }else{
		  	$this->CI->session->set_flashdata('sukses','Oops... Username/password salah');
		  	//redirect("base_url()core/login");
		 }
	}

	function do_logout()
	{
		$this->session->set_userdata("user");
		//echo "Berhasil keluar";
		redirect(base_url());		
	} 
	
}
