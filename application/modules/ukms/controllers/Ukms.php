<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class Ukms extends MX_Controller {

    public $flag = true;
    public $_version = '';

	public function __construct() {
		parent::__construct();
        $this->load->model('ukm');

        if ($this->flag) {
			$this->_version = '_v2.php';
		}else{
			$this->_version = '';
		}
	}
    
	public function index(){
		$this->template->write_view('index');
    }
    
    public function ukmDetailProfile(){
        $ukmName = $this->input->get('ukmName');

        $profileUkm = $this->ukm->fetch_table('*','ukm','name = "'.$ukmName.'"','','','','',TRUE);
    	$this->template->write_view('detail'. $this->_version);
    }
    
    public function getUkmDataDetailProfile(){
        $ukmName = $this->input->get('ukmName');

        $profileUkm = $this->ukm->fetch_table('*','ukm','name = "'.$ukmName.'"','','','','',TRUE);
        if (count($profileUkm) == 0) {
            $response = array(
                'code' => 204,
                'message' => 'Ukm tidak ada'
            );
            echo json_encode($response, JSON_PRETTY_PRINT);
            die();
        }

        $value = array(
            'views' => $profileUkm[0]['views'] + 1
        );
        $update = $this->ukm->update_table('ukm', $value, 'id', $profileUkm[0]['id']);

        $productUkm = $this->ukm->fetch_table('*','ukm_product','ukm_id = "'.$profileUkm[0]['id'].'"','sold_count','desc','',5,TRUE);
        $commentUkm = $this->ukm->fetch_table('*','ukm_comment','ukm_id = "'.$profileUkm[0]['id'].'"','','','','',TRUE);

        $response = array(
            'code' => 200,
            'message' => 'Ukm ditemukan',
            'data' => array(
                'profile' => $profileUkm,
                'product' => $productUkm,
                'comment' => $commentUkm,
            )
        );
        echo json_encode($response, JSON_PRETTY_PRINT);
        die();
    }

    function processAddComent(){
        $this->form_validation->set_rules('name', 'Nama', 'trim|required|xss_clean');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean');
		// $this->form_validation->set_rules('subject', 'Subject Date is required', 'trim|required|xss_clean');
		$this->form_validation->set_rules('message', 'Pesan', 'trim|required|xss_clean');

		/* CONDITION FORM STATMENT */
		if($this->form_validation->run() == FALSE){
			$form_error = $this->form_validation->error_array();
			$response =  array(
				'code' => 401,
				'message' => 'Form tidak lengkap',
				'error' => $form_error,
			);
			echo json_encode($response, JSON_PRETTY_PRINT);
			die();
        }

        $ukmName = $this->input->get('ukmName');
        $profileUkm = $this->ukm->fetch_table('*','ukm','name = "'.$ukmName.'"','','','','',TRUE);

        if(count($profileUkm) == 0){
			$response =  array(
				'code' => 401,
				'message' => 'Berhasil mengambil data UMKM'
			);
			echo json_encode($response, JSON_PRETTY_PRINT);
			die();
        }

        $value = array(
            'ukm_id' => $profileUkm[0]['id'],
            'name' => $this->input->post('name'),
            'email' => $this->input->post('email'),
            'subject' => null,
            'message' => $this->input->post('message')
        );

        $this->ukm->insert_table('ukm_comment', $value);

        $response = array(
            'code' => 200,
            'message' => 'Komentar ditambahkan.',
        );
        echo json_encode($response, JSON_PRETTY_PRINT);
        die();
    }

}
?>