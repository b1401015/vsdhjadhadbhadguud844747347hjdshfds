<?php
/**
 */
Class Category_has_record_model extends MY_Model
{
    var $table_name = 'category_has_record';

    function find_cate($where, $select = '*'){
        $this->db->select($select);
        $this->db->from($this->table_name);
        $this->db->join('category', 'category._id = category_has_record.category_id');
        $this->db->where($where);
        $query = $this->db->get();

        if($query->result()){
            return $query->result();

        }else{
            return false;
        }
    }
}