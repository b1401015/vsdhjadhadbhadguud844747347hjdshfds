<?php
class Rank_model extends MY_Model{
    var $table_name = 'rank';
    function find($where, $select = '*'){
        $this->db->select($select);
        $this->db->from($this->table_name);
        $this->db->where($where);
        $this->db->order_by('point', 'DESC');
        $query = $this->db->get();

        if($query->result()){
            return $query->result();

        }else{
            return false;
        }
    }
}