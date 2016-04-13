<?php

class User_obj {
    private $id_user;
    private $username;
    private $email;
    private $firstname;
    private $middlename;
    private $lastname;
    private $confirm_token;
    private $active;
    private $role;

    public function __construct(array $data) {
        foreach($data as $key => $value) {
            if (property_exists($this, $key)) {
                $function = 'set_'.$key;
                $this->$function($value);
            }
        }
    }

    public function get_id_user()       {return (int) $this->id_user;}
    public function get_username()      {return (string) $this->username;}
    public function get_email()         {return (string) $this->email;}
    public function get_firstname()     {return (string) $this->firstname;}
    public function get_middlename()    {return (string) $this->middlename;}
    public function get_lastname()      {return (string) $this->lastname;}
    public function get_confirm_token() {return (string) $this->confirm_token;}
    public function get_active()        {return (bool) $this->active;}
    public function get_role()          {return (string) $this->role;}

    public function get_fullname()      {return (string) $this->firstname . ' ' . (!empty($this->middlename) ? $this->middlename.' ' : '') . $this->lastname;}

    public function set_id_user($v)     {$this->id_user = (int) $v; return $this;}
    public function set_username($v)    {$this->username = (string) $v; return $this;}
    public function set_email($v)       {$this->email = (string) $v; return $this;}
    public function set_firstname($v)   {$this->firstname = (string) $v; return $this;}
    public function set_middlename($v)  {$this->middlename = (string) $v; return $this;}
    public function set_lastname($v)    {$this->lastname = (string) $v; return $this;}
    public function set_confirm_token($v) {$this->confirm_token = (string) $v; return $this;}
    public function set_active($v)      {$this->active = (bool) $v; return $this;}
    public function set_role($v)        {$this->role = (string) $v; return $this;}
}
