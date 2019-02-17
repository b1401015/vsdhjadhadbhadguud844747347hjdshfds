<?php
/**
 */
Class Account_register_model extends MY_Model
{
    var $table_name = 'account_register';
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
    function get_panigate($where, $select = '*',$offset,$limit){
        $this->db->select($select);
        $this->db->from($this->table_name);
        $this->db->join('account', 'account._id = account_register.account_id');
        $this->db->join('account_info', 'account._id = account_info.account_id');
        $this->db->where($where);
        $this->db->limit($offset,$limit);
        $query = $this->db->get();
        if($query->result()){
            return $query->result();

        }else{
            return false;
        }
    }
}
