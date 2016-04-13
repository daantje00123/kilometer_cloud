<?php

class Authentication extends CI_Controller {
    public function __construct() {
        parent::__construct();

        require_once(__DIR__.'/../classes/User_obj.php');

        $this->load->library(array('form_validation'));
        $this->load->helper(array('url'));
        $this->load->model('Authentication_model');
    }

    // Authenticate user
    public function index() {
        if (isset($_SESSION['user']) && isset($_SESSION['loggedin']) && !empty($_SESSION['user']) && !empty($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
            redirect('');
            return;
        }

        // Set validation rules
        $this->form_validation->set_rules('username', 'username', 'required|callback_check_username');
        $this->form_validation->set_rules('password', 'password', 'required');

        // Show login screen if the form is not valid or the form is not yet submitted
        if ($this->form_validation->run() === false) {
            db_load_view('authentication/login');

            return;
        }

        $username = $this->input->post('username');
        $password = $this->input->post('password');

        $user = $this->Authentication_model->get_user_by_username($username);

        if ($user->get_active() != true) {
            show_error("User is not yet activated");
            return;
        }

        if ($this->Authentication_model->login($user, $password)) {
            $referer = ((isset($_SESSION['login_referer']) && !empty($_SESSION['login_referer'])) ? $_SESSION['login_referer'] : base_url());

            unset($_SESSION['login_referer']);

            header("Location: ".$referer);

            return;
        }

        redirect('authentication');
    }

    // Create a password for the user and activate the user
    public function confirm($token = '') {
        $token = (string) $token;

        if (empty($token)) {
            show_error("There is no valid token found");
            return;
        }

        $user = $this->Authentication_model->get_user_by_token($token);

        if (empty($user->get_id_user())) {
            show_error("There is no valid token found.");
            return;
        }

        $this->form_validation->set_rules('password[1]', 'password', 'required|matches[password[2]]');
        $this->form_validation->set_rules('password[2]', 'password verify', 'required|matches[password[1]]');

        if ($this->form_validation->run() === false) {
            db_load_view('authentication/confirm/password_form', array('user' => $user, 'token' => $token));
            return;
        }

        $password = $this->input->post('password');
        $password = $password[1];

        $user = $this->Authentication_model->set_password($user, $password);

        db_load_view('authentication/confirm/success', array('user' => $user));
    }

    // Log a user out
    public function logout() {
        unset(
            $_SESSION['loggedin'],
            $_SESSION['user']
        );

        echo "<script>localStorage.removeItem('username'); localStorage.removeItem('password'); window.location.href = '".base_url('authentication')."';</script>";
    }

    // CLI commands
    public function cli($action = '') {
        if (php_sapi_name() !== 'cli') {
            show_error("This is a command line only tool");
            return;
        }

        switch($action) {
            // Register a new user
            case 'register':
                echo "\033[32mRegister a new user\033[0m".PHP_EOL.PHP_EOL;
                echo "Username: ";
                $username = trim(fgets(STDIN), PHP_EOL);

                echo "Email: ";
                $email = trim(fgets(STDIN), PHP_EOL);

                echo "Firstname: ";
                $firstname = trim(fgets(STDIN), PHP_EOL);

                echo "Middlename (optional): ";
                $middlename = trim(fgets(STDIN), PHP_EOL);

                echo "Lastname: ";
                $lastname = trim(fgets(STDIN), PHP_EOL);



                if (
                    empty($username) ||
                    empty($email) ||
                    empty($firstname) ||
                    empty($lastname)
                ) {
                    echo "\033[41mData is not valid\033[0m".PHP_EOL;
                    return;
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    echo "\033[41mEmail is not valid\033[0m".PHP_EOL;
                }

                $user = $this->register_user($username, $email, $firstname, $middlename, $lastname);

                echo "Send confirm email? [y/n] ";
                $send_email = strtolower(trim(fgets(STDIN), PHP_EOL));

                if ($send_email == 'y') {
                    if ($this->Authentication_model->send_confirm_email($user)) {
                        echo "User has been created and email has been send".PHP_EOL;
                    } else {
                        echo "User has been created".PHP_EOL;
                    }
                } else {
                    echo "User has been created".PHP_EOL;
                }
                break;

            default:
                echo 'Commands:'.PHP_EOL;
                echo PHP_EOL;
                echo 'register'.PHP_EOL;
                echo PHP_EOL;
                break;
        }
    }

    // Callback function for the form_validator
    public function check_username($username = '') {
        if (strpos(uri_string(), 'check_username') !== false) {
            redirect('authentication');
            return;
        }

        if (empty($username)) {
            $this->form_validation->set_message('check_username', "The username is not valid.");
            return false;
        }

        $user = $this->Authentication_model->get_user_by_username($username);

        if (empty($user->get_id_user())) {
            $this->form_validation->set_message('check_username', "The username is not valid.");
            return false;
        }

        return true;
    }

    // Create new user object and save it to the database
    private function register_user($username, $email, $firstname, $middlename, $lastname) {
        $username = (string) $username;
        $email = (string) $email;
        $firstname = (string) $firstname;
        $middlename = (string) $middlename;
        $lastname = (string) $lastname;

        if (
            empty($username) ||
            empty($email) ||
            empty($firstname) ||
            empty($lastname)
        ) {
            show_error("Data is not valid");
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            show_error("Email is not valid");
            return;
        }

        try {
            return $this->Authentication_model->create_user($username, $email, $firstname, $middlename, $lastname);
        } catch (Exception $e) {
            show_error($e->getMessage());
        }
    }
}