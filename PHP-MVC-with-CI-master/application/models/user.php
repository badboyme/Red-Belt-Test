<?php 

class User extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    date_default_timezone_set('America/Los_Angeles');
  }

  public function validate($post)
  {
  	// If registration form was submitted
  	if ($post['action'] == "register")
  	{
  		// Validate input fields
	  	$this->load->library('form_validation');
	  	$this->form_validation->set_rules('name', "Name", 'required');
	  	$this->form_validation->set_rules('alias', "Alias", 'required');
	  	$this->form_validation->set_rules('email', "Email", 'required|valid_email');
	  	$this->form_validation->set_rules('password', "Password", 'required|min_length[8]');
	  	$this->form_validation->set_rules('confirm_password', "Confirm PW", 'required|matches[password]');
	  	$this->form_validation->set_rules('date', "Date of Birth", 'required');	
	  	if ($this->form_validation->run() == FALSE)
	  	{
	  		$this->session->set_flashdata('registration_error', validation_errors());
	  		redirect('/users/index');
	  	}
	  	else
	  	{
	  		$test = $this->db->query("SELECT * FROM users WHERE email = ?", array($post['email']))->row_array();
	  		if (count($test) > 0)
	  		{
	  			$this->session->set_flashdata('registration_error', "Email is already registered!");
	  			redirect('/users/index');
	  		}
	  		else
	  		{
	  			// If no errors and email is available
	  			$query = "INSERT INTO users (name, alias, email, password, birthdate, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)";
	  			$values = array($post['name'], $post['alias'], $post['email'], md5($post['password']), $post['date'], date('Y-m-d H:i:s'), date('Y-m-d H:i:s'));
	  			$this->db->query($query, $values);
	  			$this->session->set_userdata('id', $this->db->insert_id());
	  			redirect('/users/home');
	  		}
	  	}
	}
	// If login form was submitted
	elseif ($post['action'] == "login")
	{
		$query = "SELECT * FROM users WHERE email = ? AND password = ?";
		$values = array($post['email'], md5($post['password']));
		$test = $this->db->query($query, $values)->row_array();

		if (count($test) > 0)
		{
			$this->session->set_userdata('id', $test['id']);
			redirect('/users/home');
		}
		else
		{
			$this->session->set_flashdata('login_error', "<p>Invalid email and password combination.</p>");
			redirect('/users/index');
		}
	}
	// All else (malicious attempts, logout)
	else
	{
		$this->session->sess_destroy();
		redirect('/users/index');
	}
  }

  public function get_data($id)
  {
  	// Select user's data & user's friends' data
  	$query = "SELECT friends.*, user1.name AS user_name, user1.alias AS user_alias, user1.id AS user_id, user1.email AS user_email, user2.name AS friend_name, 	user2.alias AS friend_alias, user2.id AS friend_id
  			FROM friends LEFT JOIN users AS user1 ON user_id_1 = user1.id
  			LEFT JOIN users AS user2 ON user_id_2 = user2.id
  			WHERE user_id_1 = ?";
  	$values = array($id);
  	$friend_data = $this->db->query($query, $values)->result_array();

  	// Gather friends' ids
  	$friend_ids = "";
  	for ($i=0; $i<count($friend_data);$i++)
  	{
  		if ($i == count($friend_data) - 1)
  		{
   			$friend_ids .= $friend_data[$i]['friend_id'];
  		}
  		else
  		{
  			$friend_ids .= $friend_data[$i]['friend_id'].", ";
  		}
  	}

  	// If user has no friends...
  	if (!$friend_ids)
  	{
  		// Select their own data for display
  		$friend_data = $this->db->query("SELECT users.name AS user_name, users.email AS user_email, users.id AS self, users.alias AS user_alias FROM users WHERE id = $id")->result_array();
  		$friend_ids = $id;
  	}

  	// Select all non-friend users
  	$id = $this->session->userdata('id');
  	$query = "SELECT * FROM users WHERE id NOT IN ($friend_ids, $id)";
  	$values = array($friend_ids);
  	$all_data = $this->db->query($query, $values)->result_array();

  	// Return data
  	return array("all_data" => $all_data, "friend_data" => $friend_data);
  }

  public function add($id)
  {
  	$query = "INSERT INTO friends (user_id_1, user_id_2, created_at, updated_at) VALUES (?, ?, ?, ?)";
  	$values = array($this->session->userdata('id'), $id, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'));
  	$this->db->query($query, $values);

  	$query = "INSERT INTO friends (user_id_1, user_id_2, created_at, updated_at) VALUES (?, ?, ?, ?)";
  	$values = array($id, $this->session->userdata('id'), date('Y-m-d H:i:s'), date('Y-m-d H:i:s'));
  	$this->db->query($query, $values);

  	redirect("/users/home");
  }

  public function delete($id)
  {
  	$query = "DELETE FROM friends WHERE user_id_1 = ? AND user_id_2 = ?";
  	$values = array($this->session->userdata('id'), $id);
  	$this->db->query($query, $values);

  	$query = "DELETE FROM friends WHERE user_id_1 = ? AND user_id_2 = ?";
  	$values = array($id, $this->session->userdata('id'));
  	$this->db->query($query, $values);

  	redirect('/users/home');
  }
}