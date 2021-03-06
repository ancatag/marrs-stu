<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Agent extends CI_Controller {

	function __construct () {
			parent::__construct ();
	}
	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

// Show login page
	public function index() {
		// $this->load->view('agentDashboardView',$data);
		if ($this->session->userdata('loggedIn') !== FALSE) {
		$data = $this->session->all_userdata();
		log_message('info',print_r($data,TRUE));
			$this->load->view('agentDashboardView',$data);
		}else{
			$this->load->view('agentLoginView');
		} 
	}
	public function registration($agn_parent_id = NULL) {
		// if($this->session->userdata('loggedIn') === TRUE){
		// 	$this->index();
		// }
		if(!isset($agn_parent_id)){
			$data['agn_parent_id'] = 1;
		}elseif($this->validateAgent($agn_parent_id) ==TRUE){
			$data['agn_parent_id'] = $agn_parent_id;
			$this->load->view('agentSignupView',$data);
		}else{
			$this->load->view('agentNotFoundView');
		}
	}
	public function createAgent() {
		$newAgentData = $this->input->post(NULL, TRUE);
		//$this->input->post(array('agnUsername', 'agnPassword', 'agn_phone', 'agn_city', 'agn_country', 'agn_region'));
		//log_message('info','post'.$this->input->post('agnUsername'));
		log_message('info',print_r($newAgentData,TRUE));
		$rsp['status'] = $this->data->insertAgent($newAgentData);
		if ($rsp['status'] == TRUE){
			$rsp['msg'] =  "Agent Created";
			$rsp['statusID'] = 1;
			$rsp['data'] = NULL;
		}elseif($rsp['status'] == FALSE)
		{
			$rsp['status'] = FALSE;
			$rsp['msg'] =  "Agent Not Created";
			$rsp['statusID'] = 0;
			$rsp['data'] = NULL;
		}
		echo json_encode ($rsp);
	}
	public function validateAgent($agn_parent_id) {
		$rsp['status'] = $this->data->checkAgent($agn_parent_id);
		if ($rsp['status'] == TRUE){
			$rsp['msg'] =  "Agent Valid";
			$rsp['statusID'] = 1;
			$rsp['data'] = NULL;
		}elseif($rsp['status'] == FALSE)
		{
			$rsp['status'] = FALSE;
			$rsp['msg'] =  "Agent Invalid";
			$rsp['statusID'] = 0;
			$rsp['data'] = NULL;
		}
		return $rsp['status'];
		echo json_encode ($rsp);
	}
	public function getAgent() {
		$authAgentData = $this->input->post(NULL, TRUE);
		$rsp['data'] = $this->data->getAgent($authAgentData);
		if ($rsp['status'] == TRUE){
			$rsp['msg'] =  "Agent Valid";
			$rsp['statusID'] = 1;
			$rsp['data'] = NULL;
		}elseif($rsp['status'] == FALSE)
		{
			$rsp['status'] = FALSE;
			$rsp['msg'] =  "Agent Not Created";
			$rsp['statusID'] = 0;
			$rsp['data'] = NULL;
		}
		return $rsp['status'];
		echo json_encode ($rsp);
	}	
	public function authAgent() {
		$rsp = array();
		$authAgentData = $this->input->post(NULL, TRUE);		
		$rsp = $this->data->authAgentDb($authAgentData);
		if ($rsp['authStatus'] == TRUE){
			$rsp['status'] = TRUE;
			$rsp['msg'] =  "Agent Valid";
			$rsp['statusID'] = 010;
			$rsp['data'] = NULL;

			$newdata = array(
				'agn_id'  => $rsp['agn_id'],
				'loggedIn' => TRUE
			);
			log_message('info',print_r($newdata,TRUE));
			$this->session->set_userdata($newdata);

		}elseif($rsp['authStatus'] == FALSE){
			$rsp['status'] = FALSE;
			$rsp['msg'] =  "Sorry, this agent doesn't exist";
			$rsp['statusID'] = 000;
			$rsp['data'] = NULL;
		}
		echo json_encode ($rsp);
	}
	public function logout() {
		// Destroy session data
		$this->session->sess_destroy();
		$this->index();
	}
		

	// // Check for user login process
	// public function userLoginProcess() {
	// 	$data = $this->Init->initPath ('/admin');
	// 	$data += $this->Init->dbCustom();
	// 			// set variables from the form
	// 			$username = $this->input->post('username');
	// 			$password = $this->input->post('password');
	// 			// log_message('info','$username '.$username);
	// 			// log_message('info','$password '.$password);
	// 			$userData = $this->User->checkUser($username, $password);
	// 			// log_message('info',print_r($userData,TRUE));


	// 			if ($userData['authStatus'] == FALSE) {
	// 				echo $userData['authStatus'].","."incorrect username or password";
	// 				exit;
	// 			}elseif ($userData['authStatus'] == TRUE) {
	// 				// set session user datas
	// 				$newdata = array(
	// 					'uid'  => $userData['uid'],
	// 					'name'  => $userData['name'],
	// 	        'username'  => $userData['username'],
	// 	        'loggedIn' => TRUE
	// 				);

	// 				$this->session->set_userdata($newdata);

	// 				log_message('info',print_r($_SESSION,TRUE));
	// 				log_message('info',print_r($userData,TRUE));
	// 				// user login ok
	// 				// $this->load->view('pages/dashboardview',$data);
	// 				// header('Location: ' . base_url() . '/admin');
	// 				exit;
	// 			}
	// }

	// // Logout from promo page
	// public function getPromoList() {
	// 	$data = $this->Init->initPath ('/admin');
	// 	$data += $this->Init->dbCustom();
	// 	$data += $this->session->userdata();

	// 	$response                         = array();
	// 	$response['status']               = false;
	// 	$response['message']              = "Invalid request";
	// 	$response['iTotalRecords']        = 0;
	// 	$response['iTotalDisplayRecords'] = 0;

	// 	$start              = $this->input->post ('start');
	// 	$length             = $this->input->post ('length');
	// 	$searchArray        = $this->input->post ('search');
	// 	$search             = $searchArray['value'];
	// 	$filter['start']    = $start;
	// 	$filter['length']   = $length;
	// 	// $filter['search']   = $search;
	// 	// log_message('info',print_r($filter,TRUE));
	// 	// log_message('info','$start '.$start);
	// 	// log_message('info','$length '.$length);
	// 	// log_message('info',print_r($searchArray,TRUE));
	// 	$totalCount         = $this->Data->getPromoListCount ($filter);
	// 	$promoListData = $this->Data->getPromoList ($filter);
	// 	// $PromoListData = $this->Data->getPromoList ();
	// 	$response['iTotalRecords']        = $totalCount;
	// 	$response['iTotalDisplayRecords'] = $totalCount;
	// 	$draw                             = $this->input->post ('draw');
	// 	// log_message('info','$totalCount '.$totalCount);
	// 	// log_message('info',print_r($PromoListData,TRUE));
	// 	// log_message('info',print_r($response,TRUE));


	// 	if (is_numeric ($draw)){
	// 		$response['sEcho'] = $draw;
	// 		$response['status']  = true;
	// 		$response['message'] = "Promo List Data Follows";
	// 		$response['data']    = $promoListData;
	// 		echo json_encode ($response);
	// 		// echo json_encode ($PromoListData);
	// 		return 1;
	// 	}
	// 	//End of getConfigList
	// }

	// public function promoStatusChanger() {
	// 	//Post Data
	// 	$data = $this->session->userdata();
	// 	$promoID = $this->input->post('id');
	// 	$promoStatus = $this->input->post('status');
	// 	log_message('info','$promoID '.$promoID);
	// 	log_message('info','$promoStatus '.$promoStatus);
	// 	$statusUpdate = $this->Data->promoStatusChangerDB ($promoID, $promoStatus);
	// 	log_message('info','$statusUpdate '.$statusUpdate);
	// 		if ($statusUpdate == TRUE){
	// 			$rsp['status'] = TRUE;
	// 			$rsp['msg'] =  "Status Changed";
	// 			$rsp['statusID'] = 1;
	// 			$rsp['data'] = NULL;
	// 			log_message('info','$statusUpdate true rsp'.print_r($rsp,TRUE));
	// 		}elseif($statusUpdate == FALSE)
	// 		{
	// 			$rsp['status'] = FALSE;
	// 			$rsp['msg'] =  "Database Error";
	// 			$rsp['statusID'] = 0;
	// 			$rsp['data'] = NULL;
	// 			log_message('info','$statusUpdate false rsp '.print_r($rsp,TRUE));
	// 		}
	// 		echo json_encode ($rsp);
	// }

	// public function updatePromo(){
	// 	//Post Data
	// 	$data = $this->session->userdata();
	// 	$promoID = $this->input->post('id');
	// 	$promoCode = $this->input->post('code');
	// 	$promoCodeStartDate = $this->input->post('start_date');
	// 	$promoCodeEndDate = $this->input->post('end_date');
	// 	$promoCodeDiscount = $this->input->post('discount');
	// 	$promoStatusPromo = $this->input->post('statusPromo');

	// 	$promoCodeUpdate = $this->Data->updatePromoDB ($promoID, $promoCode, $promoCodeStartDate, $promoCodeEndDate, $promoCodeDiscount, $promoStatusPromo);
	// 		if ($promoCodeUpdate == TRUE){
	// 			$rsp['status'] = TRUE;
	// 			$rsp['msg'] =  "Promo code update";
	// 			$rsp['statusID'] = 1;
	// 			$rsp['data'] = NULL;
	// 			// log_message('info','$statusUpdate true rsp'.print_r($rsp,TRUE));
	// 		}elseif($promoCodeUpdate == FALSE)
	// 		{
	// 			$rsp['status'] = FALSE;
	// 			$rsp['msg'] =  "Database Error";
	// 			$rsp['statusID'] = 0;
	// 			$rsp['data'] = NULL;
	// 			// log_message('info','$statusUpdate false rsp '.print_r($rsp,TRUE));
	// 		}
	// 		echo json_encode ($rsp);
	// }

	// public function promoCodeDelete(){
	// 	$data = $this->session->userdata();
	// 	$promoID = $this->input->post('id');
	// 	$promoDelete = $this->Data->promoCodeDeleteDB ($promoID);
	// 		if ($promoDelete == TRUE){
	// 			$rsp['status'] = TRUE;
	// 			$rsp['msg'] =  "Promo Deleted";
	// 			$rsp['statusID'] = 1;
	// 			$rsp['data'] = NULL;
	// 		}elseif($promoDelete == FALSE){
	// 			$rsp['status'] = FALSE;
	// 			$rsp['msg'] =  "Database Error";
	// 			$rsp['statusID'] = 0;
	// 			$rsp['data'] = NULL;
	// 		}
	// 		echo json_encode ($rsp);
	// 	}

	// // Logout from admin page
	// public function logout() {
	// $data = $this->Init->initPath ('/admin');
	// $data += $this->Init->dbCustom();
	// // Destroy session data
	// $this->session->sess_destroy();
	// redirect(base_url() . 'admin', 'refresh');
	// // header('Location: ' . base_url() . 'admin');
	// // exit;
	// }

	// public function getBookingList() {
	// 	$data = $this->Init->initPath ('/mainctrl');
	// 	$data += $this->Init->dbCustom();
	// 	$data += $this->session->userdata();

	// 	$response                         = array();
	// 	$response['status']               = false;
	// 	$response['message']              = "Invalid request";
	// 	$response['iTotalRecords']        = 0;
	// 	$response['iTotalDisplayRecords'] = 0;

	// 	$start              = $this->input->post ('start');
	// 	$length             = $this->input->post ('length');
	// 	$searchArray        = $this->input->post ('search');
	// 	$search             = $searchArray['value'];
	// 	$filter['start']    = $start;
	// 	$filter['length']   = $length;
	// 	$filter['search']   = $search;
	// 	// log_message('info',print_r($filter,TRUE));
	// 	// log_message('info','$start '.$start);
	// 	// log_message('info','$length '.$length);
	// 	// log_message('info',print_r($searchArray,TRUE));
	// 	$totalCount         = $this->Data->getBookDataListCount ($filter);
	// 	$bookDataList = $this->Data->getBookDataList ($filter);
	// 	// $PromoListData = $this->Data->getPromoList ();
	// 	$response['iTotalRecords']        = $totalCount;
	// 	$response['iTotalDisplayRecords'] = $totalCount;
	// 	$draw                             = $this->input->post ('draw');
	// 	// log_message('info','$totalCount '.$totalCount);
	// 	// log_message('info',print_r($PromoListData,TRUE));
	// 	// log_message('info',print_r($response,TRUE));


	// 	if (is_numeric ($draw)){
	// 		$response['sEcho'] = $draw;
	// 		$response['status']  = true;
	// 		$response['message'] = "Booking List Data Follows";
	// 		$response['data']    = $bookDataList;
	// 		echo json_encode ($response);
	// 		// echo json_encode ($PromoListData);
	// 		return 1;
	// 		}
	// 	}

	// 	public function getSelectedBookingList() {
	// 		$data = $this->Init->initPath ('/mainctrl');
	// 		$data += $this->Init->dbCustom();
	// 		$data += $this->session->userdata();

	// 		$bookingID = $this->input->post('bid');

	// 		$bookSelectedDataList = $this->Data->getSelectedBookDataList ($bookingID);
	// 		// $PromoListData = $this->Data->getPromoList ();

	// 		// log_message('info','$totalCount '.$totalCount);
	// 		// log_message('info',print_r($PromoListData,TRUE));
	// 		// log_message('info',print_r($response,TRUE));


	// 		if ($bookSelectedDataList){
	// 			$response['status']  = true;
	// 			$response['message'] = "Booking Selected List Data Follows";
	// 			$response['data']    = $bookSelectedDataList;
	// 			echo json_encode ($response);
	// 			// echo json_encode ($PromoListData);
	// 			return 1;
	// 			}
	// }

	// public function getSelectedPromoList() {
	// 	$data = $this->Init->initPath ('/mainctrl');
	// 	$data += $this->Init->dbCustom();
	// 	$data += $this->session->userdata();

	// 	$promoID = $this->input->post('id');

	// 	$promoSelectedDataList = $this->Data->getSelectedPromoList ($promoID);
	// 	// $PromoListData = $this->Data->getPromoList ();

	// 	// log_message('info','$totalCount '.$totalCount);
	// 	// log_message('info',print_r($PromoListData,TRUE));
	// 	// log_message('info',print_r($response,TRUE));


	// 	if ($promoSelectedDataList){
	// 		$response['status']  = true;
	// 		$response['message'] = "Promo Selected List Data Follows";
	// 		$response['data']    = $promoSelectedDataList;
	// 		echo json_encode ($response);
	// 		// echo json_encode ($PromoListData);
	// 		return 1;
	// 		}
	// }

	// public function bookingDelete(){
	// 	$data = $this->session->userdata();
	// 	$bookingID = $this->input->post('bid');
	// 	$bookingDelete = $this->Data->bookingDeleteDB ($bookingID);
	// 		if ($bookingDelete == TRUE){
	// 			$rsp['status'] = TRUE;
	// 			$rsp['msg'] =  "Booking Deleted";
	// 			$rsp['statusID'] = 1;
	// 			$rsp['data'] = NULL;
	// 		}elseif($bookingDelete == FALSE){
	// 			$rsp['status'] = FALSE;
	// 			$rsp['msg'] =  "Database Error";
	// 			$rsp['statusID'] = 0;
	// 			$rsp['data'] = NULL;
	// 		}
	// 		echo json_encode ($rsp);
	// 	}

	// 	public function download(){
	// 			$this->Data->exportCSV();
	// 	}

}
