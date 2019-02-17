<?php
/**
 */
Class Account_jwt_model extends MY_Model
{
    var $table_name = 'account_jwt';

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
    function get_total($select = '*'){
        $this->db->select($select);
        $this->db->from($this->table_name);
        $query = $this->db->get();

        if($query->result()){
            return $query->result();

        }else{
            return false;
        }
    }

}
