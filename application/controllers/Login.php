<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Login extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->common->set_timezone();
        $this->load->model('user/m_login', 'objlogin');

        $this->load->library('user_agent');
    }

    public function index() {
        $login_type = $this->session->userdata('userType');
        if ($login_type == 'user') {
            redirect('home');
        }

        $this->load->view('landing-page');
        //$this->load->view('header-no-menu-bar');
        //$this->load->view('login');
        //$this->load->view('footer');
    }

    public function form() {
        $login_type = $this->session->userdata('userType');
        if ($login_type == 'user') {
            redirect('home');
        }

        $this->load->view('login-new');
    }

    public function landingPage() {
        $this->load->view('landing-page');
    }

    public function authentication() {
        $username = $this->input->post('email');
        $password = $this->input->post('password');


        if (strlen(trim(preg_replace('/\xb2\xa0/', '', $username))) == 0 || strlen(trim(preg_replace('/\xb2\xa0/', '', $password))) == 0) {
            $this->session->set_flashdata('msg', '<div class="col-md-12 text-red" style="padding: 0 0 10px 0;">Please enter Username or Password</div><br>');
            redirect('login');
        } else {
            $arr = array(
                'email' => $username,
                'password' => base64_encode($password)
            );
            $data = $this->objlogin->user_login($username,base64_encode($password));
            if ($data) {

                //check for the cookie
                if (!isset($_COOKIE["yc_trusted_device_id"]) ||
                    (isset($_COOKIE["yc_trusted_device_id"]) && !($this->checkTrustedBrowsers($data['cust_id'], $_COOKIE["yc_trusted_device_id"]))))
                {
                    $phone = $this->getMaskedPhone($data['cust_id']);

                    if (!$phone){
                        $response = array(
                            'status' => 'new_browser',
                            'user_id' => $data['cust_id'],
                            'masked_reg_mobile' => 'unset'
                        );
                    }else{
                        $response = array(
                            'status' => 'new_browser',
                            'user_id' => $data['cust_id'],
                            'masked_reg_mobile' => $phone
                        );
                    }



                    echo json_encode($response);
                    return;
                }


                $token = $this->objlogin->update_user_token($data['cust_id']);
                $session = array(
                    'cid' => $data['cust_id'],
                    'cname' => $data['first_name'],
                    'fullname' => $data['first_name'] . ' ' . $data['last_name'],
                    'email' => $data['email'],
                    'token' => $token,
                    'userType' => 'user'
                );
                $this->session->set_userdata($session);

                $response = array(
                    'status' => 'success'
                );

            } else {
                $response = array(
                    'status' => 'failed',
                    'msg' => 'Incorrect username or password!'
                );

                //$this->session->set_flashdata('msg', '<div class="col-md-12 text-red" style="padding: 0 0 10px 0;">Username or Password is Wrong.</div><br>');
                //redirect('login');
            }

            echo json_encode($response);
            return;
        }
    }

    function register_login($cust_id) {
        $data = $this->objlogin->register_login($cust_id);
        if ($data) {
            $token = $this->objlogin->update_user_token($data['cust_id']);
            $session = array(
                'cid' => $data['cust_id'],
                'cname' => $data['first_name'],
                'fullname' => $data['first_name'] . ' ' . $data['last_name'],
                'email' => $data['email'],
                'token' => $token,
                'userType' => 'user'
            );
            $this->session->set_userdata($session);
            redirect('home');
        } else {
            redirect('login');
        }
    }

    function logout() {
        $this->session->unset_userdata('cid');
        $this->session->unset_userdata('cname');
        $this->session->unset_userdata('fullname');
        $this->session->unset_userdata('email');
        $this->session->unset_userdata('token');
        $this->session->unset_userdata('userType');
        header('location:' . base_url() . 'login');
    }

    function cco_authentication() {
        $token = $this->input->get('token');
        $response_array = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', $token)[1]))));

        if (isset($response_array) && !empty($response_array)) {
            $identifier = $response_array->identity->identifier;
            $member_id = substr($identifier, 4);
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://www.clinicaloptions.com/api/external?memberid=" . $member_id . "&SecurityToken=OUqrB8i6Bc002GZGtZHod49QVBdPjEo4qu1vxnHWmnhe5MSf7kW1v62yXhINaal7JK3tuC3Z0gBuGEpwh8l5SQ%3D%3D",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => FALSE,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            $member_array = json_decode($response);
            if (isset($member_array) && !empty($member_array)) {
                $or_where = '(email = "' . $member_array->emailAddress . '")';
                $this->db->where($or_where);
                $customer = $this->db->get('customer_master');

                if ($customer->num_rows() > 0) {
                    $user_details = $customer->row();
                    $set_update_array = array(
                        'first_name' => $member_array->firstName,
                        'last_name' => $member_array->lastName,
                        'email' => $member_array->emailAddress,
                        'specialty' => $member_array->specialty,
                        'degree' => $member_array->degree,
                        'city' => $response_array->identity->city,
                        'zipcode' => $response_array->identity->zip,
                        'state' => $response_array->identity->state,
                        'country' => $response_array->identity->country,
                        'topic' => $member_array->topics,
                        'identifier_id' => $response_array->identity->identifier,
                        'customer_session' => $response_array->session,
                        'iat' => $response_array->iat,
                        'exp' => $response_array->exp,
                        'aud' => json_encode($response_array->aud),
                        'jti' => $response_array->jti
                    );
                    $this->db->update("customer_master", $set_update_array, array("cust_id" => $user_details->cust_id));
                    $token = $this->objlogin->update_user_token($user_details->cust_id);
                    $session = array(
                        'cid' => $user_details->cust_id,
                        'cname' => $user_details->first_name,
                        'fullname' => $user_details->first_name . " " . $user_details->last_name,
                        'email' => $user_details->email,
                        'token' => $token,
                        'userType' => 'user'
                    );
                    $this->session->set_userdata($session);
                    $sessions = $this->db->get_where('sessions', array('sessions_id' => $response_array->session));
                    if ($sessions->num_rows() > 0) {
                        redirect('sessions/attend/' . $response_array->session);
                    } else {
                        redirect('home');
                    }
                } else {
                    $this->db->order_by("cust_id", "desc");
                    $row_data = $this->db->get("customer_master")->row();
                    if (!empty($row_data)) {
                        $reg_id = $row_data->cust_id;
                        $register_id = date("Y") . '-20' . $reg_id;
                    } else {
                        $register_id = date("Y") . '-200';
                    }
                    $set = array(
                        "register_id" => $register_id,
                        'first_name' => $member_array->firstName,
                        'last_name' => $member_array->lastName,
                        'email' => $member_array->emailAddress,
                        'password' => base64_encode(123),
                        'specialty' => $member_array->specialty,
                        'degree' => $member_array->degree,
                        'city' => $response_array->identity->city,
                        'zipcode' => $response_array->identity->zip,
                        'state' => $response_array->identity->state,
                        'country' => $response_array->identity->country,
                        'topic' => $member_array->topics,
                        'identifier_id' => $response_array->identity->identifier,
                        'customer_session' => $response_array->session,
                        'iat' => $response_array->iat,
                        'exp' => $response_array->exp,
                        'aud' => json_encode($response_array->aud),
                        'jti' => $response_array->jti,
                        'address' => "",
                        'register_date' => date("Y-m-d h:i")
                    );
                    $this->db->insert("customer_master", $set);
                    $cust_id = $this->db->insert_id();
                    $user_details = $this->db->get_where("customer_master", array("cust_id" => $cust_id))->row();
                    if (!empty($user_details)) {
                        $token = $this->objlogin->update_user_token($user_details->cust_id);
                        $session = array(
                            'cid' => $user_details->cust_id,
                            'cname' => $user_details->first_name,
                            'fullname' => $user_details->first_name . " " . $user_details->last_name,
                            'email' => $user_details->email,
                            'token' => $token,
                            'userType' => 'user'
                        );
                        $this->session->set_userdata($session);
                        $sessions = $this->db->get_where('sessions', array('sessions_id' => $response_array->session));
                        if ($sessions->num_rows() > 0) {
                            redirect('sessions/attend/' . $response_array->session);
                        } else {
                            redirect('home');
                        }
                    }
                }
            }else{
                echo "User details not recieved from CCO";
            }
        }
    }

    private function checkTrustedBrowsers($user_id, $unique_id)
    {
        $this->db->select('*');
        $this->db->from('trusted_devices');
        $this->db->where("user_id ", $user_id);
        $this->db->where("unique_id", $unique_id);
        $result = $this->db->get();
        if ($result->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    private function setTrustedBrowsers($user_id, $unique_id)
    {
        $data = array(
            'user_id' => $user_id,
            'unique_id' => $unique_id
        );

        $this->db->insert('trusted_devices', $data);
    }

    private function getMaskedPhone($user_id)
    {
        $this->db->select('phone');
        $this->db->from('customer_master');
        $this->db->where("cust_id ", $user_id);
        $result = $this->db->get();
        if ($result->num_rows() > 0) {

            $number = $result->result()[0]->phone;

            if ($number == null || $number == '')
                return false;

            $middle_string ="";
            $length = strlen($number);

            if( $length < 9 ){

                return false;
                //return $length == 1 ? "*" : "*". substr($number,  - 1);

            }
            else{
                $part_size = floor( $length / 3 ) ;
                $middle_part_size = $length - ( $part_size * 2 );
                for( $i=0; $i < $middle_part_size ; $i ++ ){
                    $middle_string .= "*";
                }

                return  substr($number, 0, $part_size ) . $middle_string  . substr($number,  - $part_size );
            }

        } else {
            return false;
        }
    }

    private function getPhone($user_id)
    {
        $this->db->select('phone');
        $this->db->from('customer_master');
        $this->db->where("cust_id ", $user_id);
        $result = $this->db->get();
        if ($result->num_rows() > 0) {

            $phone = $result->result()[0]->phone;

            if ($phone == null || $phone == '' || strlen($phone < 9))
                return false;

            return $phone;

        } else {
            return false;
        }
    }

    public function sendLoginOtp($user_id, $method='sms')
    {
        $phone = $this->getPhone($user_id);

        if ($method == 'sms')
        {
            $otp = rand ( 1000 , 9999 );

            $this->load->library('twilio');

            $from = '+12065128449';
            $to = $phone;
            $message = $otp.' is your Your Conference authentication code for FauxSKO21 login.';

            $response = $this->twilio->sms($from, $to, $message);


            if($response->IsError)
            {
                $response = array(
                    'status' => 'failed',
                    'msg' => $response->ErrorMessage
                );

                echo json_encode($response);
            }else{

                $this->db->set('otp', $otp);
                $this->db->set('otp_created', date('Y-m-d H:i:s'));
                $this->db->where('cust_id', $user_id);
                $this->db->update('customer_master');

                $response = array(
                    'status' => 'success',
                    'msg' => "OTP was sent!"
                );
                echo json_encode($response);
            }

            return;
        }
    }

    public function verifyOtp()
    {
        $user_id_input = $this->input->post()['user_id'];
        $otp_input = $this->input->post()['otp'];
        $trust_check_input = $this->input->post()['trust_check'];

        $response = array();

        $this->db->select('*');
        $this->db->from('customer_master');
        $this->db->where("cust_id ", $user_id_input);
        $result = $this->db->get();
        if ($result->num_rows() > 0) {

            $otp = $result->result()[0]->otp;
            $otp_created = $result->result()[0]->otp_created;


            if ($otp == $otp_input)
            {
                $otp_created_on = DateTime::createFromFormat('Y-m-d H:i:s', $otp_created);
                $now = new Datetime('now');

                $otp_created_before_min = abs( $otp_created_on->getTimestamp() - $now->getTimestamp() ) / 60;

                if ($otp_created_before_min < 6)
                {
                    $response['trusted_browser'] = 'false';

                    if ($trust_check_input == 'yes')
                    {
                        //generate unique id
                        $unique_id = bin2hex (openssl_random_pseudo_bytes (64));

                        setcookie ("yc_trusted_device_id", $unique_id);

                        $data = array(
                            'user_id' => $result->result()[0]->cust_id,
                            'unique_id' => $unique_id,
                            'browser' => $this->agent->browser(),
                            'os' => $this->agent->platform(),
                            'ip' => $this->input->ip_address(),
                            'date_time' => date('Y-m-d H:i:s')
                        );

                        $this->db->insert('trusted_devices', $data);

                        $response['trusted_browser'] = 'true';
                    }

                    $this->db->set('otp', null);
                    $this->db->set('otp_created', null);
                    $this->db->where('cust_id', $result->result()[0]->cust_id);
                    $this->db->update('customer_master');

                    $token = $this->objlogin->update_user_token($result->result()[0]->cust_id);
                    $session = array(
                        'cid' => $result->result()[0]->cust_id,
                        'cname' => $result->result()[0]->first_name,
                        'fullname' => $result->result()[0]->last_name,
                        'email' => $result->result()[0]->email,
                        'token' => $token,
                        'userType' => 'user'
                    );
                    $this->session->set_userdata($session);


                    $response['status'] = 'success';

                }else{
                    $response['status'] = 'failed';
                    $response['msg'] = 'OTP expired!';
                }
            }else{
                $response['status'] = 'failed';
                $response['msg'] = 'Incorrect OTP!';
            }

        } else {
            $response['status'] = 'failed';
            $response['msg'] = 'User not found!';
        }

        echo json_encode($response);
        return;
    }

}
