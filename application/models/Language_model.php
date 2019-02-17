<?php
/**
 */
Class Language_model extends MY_Model
{
    var $table_name = 'language';

    function findOne($where, $select = '*')
    {
        $this->db->select($select);
        $this->db->from($this->table_name);
        $this->db->where($where);
        $query = $this->db->get();

        if ($query->result()) {
            return $query->first_row();

        } else {
            return false;
        }
    }

    function find($where, $select = '*')
    {
        $this->db->select($select);
        $this->db->from($this->table_name);
        $this->db->where($where);
        $query = $this->db->get();

        if ($query->result()) {
            return $query->result();

        } else {
            return false;
        }
    }
}