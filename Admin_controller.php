<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class admin_controller extends CI_Controller {

 public function __construct()
 {
  parent::__construct();
  $this->load->model('Admin_model');  
  $this->load->helper('url');
  $this->load->helper('form');
  $this->load->database();
  $this->load->library('session');
 }

	public function index()
	{
		
		$this->load->view('login-signup');
	}
	public function login_signup()
	{
		$this->load->view('login-signup');
	}

	public function mychildren()
	{

		$parent_id = $this->session->userdata('parentid'); 			
		$data['childdetails'] = $this->Admin_model->select_child($parent_id);
		$this->load->view('childrens',$data);
	}

	public function programs()
	{
		if($this->session->userdata('parentid'))
			{
				$program_details = $this->Admin_model->select_programs();
				$data['programs_deatils'] = $program_details;
				$this->load->view('programs',$data);
			}
		else
		   {
		  		$this->load->view('login-signup');
		   }
	}

	public function dashboard()
	{
	  if($this->session->userdata('parentid')){

		$parent_id = $this->session->userdata('parentid');
		$parent_details = $this->Admin_model->select_parant($parent_id);
		$data['userdetails'] = $parent_details;
		$this->load->view('dashboard',$data);
	  }
	  else
	  {
	  	$this->load->view('login-signup');
	  }
	}



	public function Login()
	{
		
		$user_name = trim($this->input->post('username'));
 		$password  = md5(trim($this->input->post('password')));

 		if($user_name != '' && $password != '')
 		{
 			$user_details = $this->Admin_model->select_user($user_name,$password);

 			if(count($user_details) > 0 )
 			{
 				foreach ($user_details as $key) 

 				 	$parent_id = $key->id;
 				 	$usertype = $key->user_type;
 				 if($usertype == 'admin')
 				 {
 				 	$data['subscription_list'] = $this->Admin_model->select_subscriptions();
					$this->load->view('subscription_list',$data);
 				 	// $this->load->view('dashboard',$data);
 				 }
 				 else
 				 {
 				 	$this->session->set_userdata('parentid', $parent_id);
	 				$parent_id = $this->session->userdata('parentid');
		 			
		 			$child_details = $this->Admin_model->select_child($parent_id);
		 			$program_details = $this->Admin_model->select_programs();
		 			
		 			if(count($child_details) > 0 )
		 			{
		 				$data['childdetails'] = $child_details;		
		 			}
		 			$data['programs_deatils'] = $program_details;
	 				$data['userdetails'] = $user_details;
	 				$this->load->view('dashboard',$data);
 				 }
	 				
 			}
 			else
 			{
 				$data['error'] = "Invalid username or password";
 				$this->load->view('login-signup',$data);
 			}

 		}


	}

	public function registration_page()
	{
		$this->load->view('login-signup');
	}

	public function registration()
	{

		$email   =  $this->input->post('email');
		$mobile  = $this->input->post('contact_number');

		$email_details = $this->Admin_model->select_email($email);
		$mobile_details = $this->Admin_model->select_mobile($mobile);

		if(count($email_details) > 0 )
		{
			$data['email_error'] = 'Email already exist'; 
		}

		if(count($mobile_details) > 0 )
		{
			$data['mobile_error'] = "Mobile already exist";
		}

		if(empty($data))
		{
			$name = $this->input->post('name');
			$age       = $this->input->post('age');
			$gender    = $this->input->post('gender');
			$nationality = $this->input->post('nationality');
			$email        =  $this->input->post('email');
			$user_type = $this->input->post('user_type');
			if($user_type == 'Institution')
				{ $institution  = $this->input->post('institution'); }
			else { $institution = ' '; }
			
			
			$occupation   = $this->input->post('occupation');
			$contact_number  = $this->input->post('contact_number');
			$password        = md5($this->input->post('password'));
			$confirmpassword = $this->input->post('confrim_password');
				
			$user_details = array('name' => $name,
									'user_type' => $user_type,
									'age' => $age,
									'gender' => $gender,
									'nationality' => $nationality,
									'institution' => $institution,
									'email' => $email,
									'occupation' => $occupation,
									'mobile_number' => $contact_number,
									'password' => $password);

			$userid = $this->Admin_model->submit_registration($user_details);
			
			if(isset($userid) && strlen($userid) > 0)
			{
				$this->load->view('login-signup');
			}
		}
		else
		{
			$this->load->view('login-signup',$data);
		}
	}




	public function view_children()
	{
		if($this->session->userdata('parentid'))
			{
				$parent_id = $this->session->userdata('parentid');
	 			$child_details = $this->Admin_model->select_child($parent_id);
	 			if(count($child_details) > 0 )
	 			{
	 				$data['childdetails'] = $child_details;
	 				$this->load->view('dashboard',$data);
	 			}
	 		 }
			  else
			  {
			  	$this->load->view('login-signup');
			  }
	}

	public function add_children()
	{
		if($this->session->userdata('parentid'))
			{

				$parent_id = $this->session->userdata('parentid');
				$name = $this->input->post('name');
				$dob = $this->input->post('dob');
				
				$gender = $this->input->post('gender');
				$institution = $this->input->post('institution');
									
				$child_details = array('parent_id' => $parent_id,
										'children_name' => $name,
										'dob' => $dob,
										'gender' => $gender,
										'institution' => $institution );
				$childid = $this->Admin_model->submit_child($child_details);


				if(isset($childid) && strlen($childid) > 0 )
				{
					$parent_id = $this->session->userdata('parentid');

					$child_details = $this->Admin_model->select_child($parent_id);
					$parent_details = $this->Admin_model->select_parant($parent_id);
					$program_details = $this->Admin_model->select_programs();

					if(count($child_details) > 0  && count($parent_details) > 0 )
					{
						echo "child added successfully";
						$data['childdetails'] = $child_details;
						$data['userdetails'] = $parent_details;
						$data['programs_deatils'] = $program_details;

						$this->load->view('dashboard',$data);
					}
				}

			  }
			  else
			  {
			  	$this->load->view('login-signup');
			  }
		

		
	}

	public function child_profile()
	{
		if($this->session->userdata('parentid'))
			{		
				$child_id =  $this->input->get('child_id');
				$child_details = $this->Admin_model->select_child_details($child_id);

				if(count($child_details) > 0 )
				{

					$data['child_details'] = $child_details;
					$this->load->view('children_profile',$data);
				}
			}
			else
			{
			  	$this->load->view('login-signup');
			}

	}

	public function child_back()
	{
		if($this->session->userdata('parentid'))
			{	
			    $parent_id = $this->session->userdata('parentid');

				$child_details = $this->Admin_model->select_child($parent_id);
				$parent_details = $this->Admin_model->select_parant($parent_id);
				$program_details = $this->Admin_model->select_programs();
				$data['programs_deatils'] = $program_details;

				if(count($child_details) > 0  && count($parent_details) > 0 )
				{
					$data['childdetails'] = $child_details;
					$data['userdetails'] = $parent_details;
					$this->load->view('dashboard',$data);
				}
			}
			else
			{
			  	$this->load->view('login-signup');
			}
	}

	public function subscribe_now()
	{
		if($this->session->userdata('parentid'))
			{
				$parent_id = $this->session->userdata('parentid');
			}
			else
			{
			  	$this->load->view('login-signup');
			}
	}


	public function subscription()
	{
		if($this->session->userdata('parentid'))
			{
				$parent_id = $this->session->userdata('parentid');
				$program_id = $this->input->post('program_id');
				

				$data['program_details'] = $this->Admin_model->select_program($program_id);
				$data['parent_details'] = $this->Admin_model->select_parant($parent_id);
				$this->load->view('payment',$data);
			}
			else
			{
			  	$this->load->view('login-signup');
			}

	}

	public function paybill()
	{
		if($this->session->userdata('parentid'))
			{
				$program_name = $this->input->post('program_name');
				$program_id = $this->input->post('program_id');
				$parent_id = $this->input->post('parent_id');
				$delivery_address = $this->input->post('delivery_address');

				$date = date('Y-m-d');

				$subscription = array('program_name' => $program_name,
									  'program_id' => $program_id,
									  'parent_id' => $parent_id,
									  'date' => $date,
									  'delivery_address' => $delivery_address
									   );

				$user_details = $this->Admin_model->select_subscriptions();
				if(count($user_details) < 250)
				{
						$subscription_id = $this->Admin_model->save_subscription($subscription);

						if(isset($subscription_id))
						{
							$parent_id = $this->session->userdata('parentid');
							$user_details = $this->Admin_model->select_parant($parent_id);
					        
					        $child_details = $this->Admin_model->select_child($parent_id);
				 			$program_details = $this->Admin_model->select_programs();
				 			
				 			if(count($child_details) > 0 )
				 			{
				 				$data['childdetails'] = $child_details;
				 			}
				 				$data['programs_deatils'] = $program_details;
								$data['userdetails'] = $user_details;
								$data['msg'] = $program_name." Subscribed successfully";
								$this->load->view('programs',$data);
						} 
				}
				else
				{
					$subscription_id = $this->Admin_model->save_waiting_subscription($subscription);

					if(isset($subscription_id))
						{
							$parent_id = $this->session->userdata('parentid');
							$user_details = $this->Admin_model->select_parant($parent_id);
					        
					        $child_details = $this->Admin_model->select_child($parent_id);
				 			$program_details = $this->Admin_model->select_programs();
				 			
				 			if(count($child_details) > 0 )
				 			{
				 				$data['childdetails'] = $child_details;
				 			}
				 				$data['programs_deatils'] = $program_details;
								$data['userdetails'] = $user_details;
								$data['msg'] = $program_name." Subscription in waiting list";
								$this->load->view('programs',$data);
						} 
				}
			}
			else
			{
			  	$this->load->view('login-signup');
			}

		
	}

	public function logout()
	{
		$this->session->unset_userdata('parentid');
		$this->load->view('login-signup');
	}

	public function check_email_exit()
	{
		if($this->session->userdata('parentid'))
			{
				$email = trim($this->input->post('email'));
				$email_details = $this->Admin_model->select_email($email);
				if(count($email_details) > 0 )
				{
					echo '1';
				}
				else
				{
					echo '0';
				}
			}
		else
			{
				$this->load->view('login-signup');
			}

	}

	public function check_mobile_exit()
	{
		$mobile = trim($this->input->post('mobile'));

		$mobile_details = $this->Admin_model->select_mobile($mobile);
		if(count($mobile_details) > 0 )
		{
			echo '1';
		}
		else
		{
			echo '0';
		}
	}

	public function subscription_list()
	{

		swetha
		
	}

}
