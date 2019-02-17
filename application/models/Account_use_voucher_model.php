<?php
/**
 */
Class Account_use_voucher_model extends MY_Model
{
    var $table_name = 'account_use_voucher';

    function findOne($where, $select = '*'){
        $this->db->select($select);
        $this->db->from($this->table_name);
        $this->db->join('voucher', 'voucher._id = account_use_voucher.voucher_id');
        $this->db->where($where);
        $query = $this->db->get();

        if($query->result()){
            return $query->first_row();

        }else{
            return false;
        }
    }
}