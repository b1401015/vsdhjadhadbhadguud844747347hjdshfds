<?php
/**
 */
Class Order_item_model extends MY_Model
{
    var $table_name = 'order_item';

    function find($where, $select = '*'){
        $this->db->select($select);
        $this->db->from($this->table_name);
        $this->db->join('record', 'record._id = order_item.record_id');
        $this->db->where($where);
        $this->db->group_by('order_item.record_id');
        $query = $this->db->get();

        if($query->result()){
            return $query->result();

        }else{
            return false;
        }
    }
}