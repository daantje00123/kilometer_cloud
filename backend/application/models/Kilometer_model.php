<?php

class Kilometer_model extends CI_Model {
    public function get_kilometers() {
        $result = $this->db->get("routes")->result_array();

        $output = array();

        foreach($result as $row) {
            $output[] = $this->prepare_row($row);
        }

        return $output;
    }

    public function get_kilometer($id) {
        $id = (int) $id;

        if (empty($id)) {
            return null;
        }

        $row = $this->db->get_where('routes', array("id_route" => $id))->row_array();

        return $this->prepare_row($row);
    }

    public function delete_kilometer($id) {
        $id = (int) $id;

        if (empty($id)) {
            return null;
        }

        $this->db->where(array('id_route' => $id));
        $this->db->delete('routes');
    }

    public function edit_kilometer($id, $omschrijving, $betaald) {
        $id = (int) $id;
        $omschrijving = (string) $omschrijving;
        $betaald = (bool) $betaald;

        if (empty($id) || empty($omschrijving)) {
            return null;
        }

        $this->db->where(array('id_route' => $id));
        $this->db->update('routes', array('omschrijving' => $omschrijving, 'betaald' => $betaald));
    }

    private function prepare_row($row) {
        $datetime = explode(' ', $row['date']);
        $date = explode('-', $datetime[0]);
        $time = explode(':', $datetime[1]);
        $row['datum']['eind'] = array(
            'day' => $date[2],
            'month' => $date[1],
            'year' => $date[0]
        );

        $row['tijd']['eind'] = array(
            'hours' => $time[0],
            'minutes' => $time[1],
            'seconds' => $time[2]
        );

        $datetime = explode(' ', $row['start_date']);
        $date = explode('-', $datetime[0]);
        $time = explode(':', $datetime[1]);
        $row['datum']['start'] = array(
            'day' => $date[2],
            'month' => $date[1],
            'year' => $date[0]
        );

        $row['tijd']['start'] = array(
            'hours' => $time[0],
            'minutes' => $time[1],
            'seconds' => $time[2]
        );

        $reis = (strtotime($row['date']) - strtotime($row['start_date']));

        $row['tijd']['reis'] = $this->sec2hms($reis);
        $row['gemiddelde'] = $row['kms']/($reis/60/60);

        if (!empty($row['route'])) {
            $row['route'] = json_decode($row['route']);
        }

        return $row;
    }

    private function sec2hms ($sec, $padHours = false)
    {

        // start with a blank string
        $hms = "";

        // do the hours first: there are 3600 seconds in an hour, so if we divide
        // the total number of seconds by 3600 and throw away the remainder, we're
        // left with the number of hours in those seconds
        $hours = intval(intval($sec) / 3600);

        // add hours to $hms (with a leading 0 if asked for)
        $hms .= ($padHours)
            ? str_pad($hours, 2, "0", STR_PAD_LEFT). ":"
            : $hours. ":";

        // dividing the total seconds by 60 will give us the number of minutes
        // in total, but we're interested in *minutes past the hour* and to get
        // this, we have to divide by 60 again and then use the remainder
        $minutes = intval(($sec / 60) % 60);

        // add minutes to $hms (with a leading 0 if needed)
        $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ":";

        // seconds past the minute are found by dividing the total number of seconds
        // by 60 and using the remainder
        $seconds = intval($sec % 60);

        // add seconds to $hms (with a leading 0 if needed)
        $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);

        // done!
        return $hms;

    }
}