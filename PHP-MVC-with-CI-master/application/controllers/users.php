<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->load->view('/users/index');
	}

	public function validate()
	{
		$this->load->model('User');
		$this->User->validate($this->input->post());
	}

	public function home()
	{
		$this->load->model('User');
		$data = $this->User->get_data($this->session->userdata('id'));
		$data = array("data" => $data);
		$this->load->view('/users/home', $data);
	}

	public function add($id)
	{
		$this->load->model('User');
		$this->User->add($id);
	}

	public function profile($id)
	{
		$this->load->model('User');
		$data = $this->User->get_data($id);
		$data = array("data" => $data);
		$this->load->view("/users/profile", $data);
	}

	public function delete($id)
	{
		$this->load->model('User');
		$this->User->delete($id);
	}
}

//end of main controller