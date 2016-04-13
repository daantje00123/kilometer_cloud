<?php

class Settings_model extends CI_Model {
    public function get_settings() {
        return $this->db->get('settings')->result_array();
    }

    public function get_setting($key) {
        if (empty($key)) {
            return null;
        }

        return $this->db->get_where('settings', array('se_key' => $key))->row_array();
    }

    public function edit_setting($key, $value, $desc) {
        if (
            empty($key) ||
            empty($value) ||
            empty($desc)
        ) {
            return null;
        }

        $this->db->where(array('se_key' => $key));
        $this->db->update('settings', array('se_value' => $value, 'se_desc' => $desc));
    }
}