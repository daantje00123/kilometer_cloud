<?php

class Authentication_model extends CI_Model {
    public function __construct() {
        parent::__construct();

        require_once(__DIR__.'/../classes/User_obj.php');
    }

    public function create_user($username, $email, $firstname, $middlename, $lastname, $role = 'ROLE_USER') {
        if (
            empty($username) ||
            empty($email) ||
            empty($firstname) ||
            empty($lastname) ||
            empty($role)
        ) {
            return new User_obj(array());
        }

        $user = $this->get_user_by_username($username);

        if (!empty($user->get_id_user())) {
            throw new Exception("Username already in use");
        }

        $confirm_token = sha1(microtime(true).mt_rand(10000,90000));

        $this->db->insert('auth_users', array(
            'username' => $username,
            'email' => $email,
            'firstname' => $firstname,
            'middlename' => $middlename,
            'lastname' => $lastname,
            'confirm_token' => $confirm_token,
            'active' => false,
            'role' => $role
        ));

        $id_user = $this->db->insert_id();

        return $this->get_user($id_user);
    }

    public function send_confirm_email(User_obj $user, $copy = '') {
        if (empty($user->get_id_user())) {
            return false;
        }

        $this->load->library('email');

        $this->email->from('test@daanvanberkel.nl', 'Daan van Berkel');
        $this->email->to($user->get_email(), $user->get_fullname());
        if (!empty($copy)) {
            $this->email->bcc($copy);
        }

        $this->email->subject('Uw account activeren');
        $this->email->message(db_load_view('authentication/email/confirm', array('user' => $user), true, false, false));

        return $this->email->send();
    }

    public function set_password($user, $password) {
        if (
            empty($user) ||
            empty($password)
        ) {
            return new User_obj(array());
        }

        $secret = sha1(microtime(true).mt_rand(10000,90000));
        $password .= $secret;
        $password = password_hash($password, PASSWORD_DEFAULT);

        $this->db->where(array('id_user' => $user->get_id_user()));
        $this->db->update('auth_users', array(
            'password' => $password,
            'secret' => $secret,
            'confirm_token' => '',
            'active' => 1
        ));

        return $this->get_user($user->get_id_user());
    }

    public function get_user($id) {
        if (empty($id)) {
            return new User_obj(array());
        }

        $result = $this->db->get_where('auth_users', array('id_user' => $id));

        if (empty($result->result_array())) {return new User_obj(array());}

        return new User_obj($result->result_array()[0]);
    }

    public function get_user_by_username($username) {
        if (empty($username)) {
            return new User_obj(array());
        }

        $result = $this->db->get_where('auth_users', array('username' => $username));

        if (empty($result->result_array())) {return new User_obj(array());}

        return new User_obj($result->result_array()[0]);
    }

    public function get_user_by_token($token) {
        if (empty($token)) {
            return new User_obj(array());
        }

        $result = $this->db->get_where('auth_users', array('confirm_token' => $token));

        if (empty($result->result_array())) {return new User_obj(array());}

        return new User_obj($result->result_array()[0]);
    }

    public function get_users() {
        $result = $this->db->get('auth_users');

        if (empty($result->result_array())) {return array();}

        $output = array();

        foreach($result->result_array() as $row) {
            $output[] = new User_obj($row);
        }

        return $output;
    }

    public function login(User_obj $user, $password) {
        if (
            empty($user->get_id_user()) ||
            empty($password)
        ) {
            return false;
        }

        if ($user->get_active() != true) {
            return false;
        }

        $this->db->select('password, secret');
        $result = $this->db->get_where('auth_users', array('id_user' => $user->get_id_user()));

        $row = $result->row_array();
        $db_password = $row['password'];
        $db_secret = $row['secret'];

        if (!password_verify($password.$db_secret, $db_password)) {
            return false;
        }

        $this->session->set_userdata('loggedin', true);
        $this->session->set_userdata('user', serialize($user));

        return true;
    }
}