<?php
/**
 */
Class Appointment_model extends MY_Model
{
    var $table_name = 'appointment';

    function findOne($where, $select = '*'){
        $this->db->select($select);
        $this->db->from($this->table_name);
        $this->db->where($where);
        $query = $this->db->get();

        if($query->result()){
            return $query->first_row();

        }else{
            return false;
        }
    }
    function find_and_paginate($where, $select = '*',$offset,$limit){
        $this->db->select($select);
        $this->db->from($this->table_name);
        $this->db->where($where);
        $this->db->order_by('time_booking', 'ASC');
        $this->db->limit($limit, $offset);
        $query = $this->db->get();

        if ($query->result()) {
            return $query->result();

        } else {
            return false;
        }
    }

}
