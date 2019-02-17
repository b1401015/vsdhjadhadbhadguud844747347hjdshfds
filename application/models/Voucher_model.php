<?php
/**
 */
Class Voucher_model extends MY_Model
{
    var $table_name = 'voucher';

    function get_pagination($where, $offset, $limit, $last_id = ''){
        $this->db->select(array(
            'voucher.*'
        ));
        $this->db->from($this->table_name);
        $this->db->where($where);
        if(!empty($last_id)) {
            $this->db->where('voucher._id <', $last_id);
        }
        else{
            $this->db->limit($limit, $offset);
        }

        $this->db->order_by('voucher._id desc, voucher.create_time desc');
        $query = $this->db->get();

        if($query->result()){
            return $query->result();

        }else{
            return false;
        }
    }

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

    function count_total($where){
        $this->db->select('count(*) as count');
        $this->db->from($this->table_name);
        $this->db->where($where);
        $query = $this->db->get();

        if($query->result()){
            $result = $query->row_array();
            return $result['count'];
        }else{
            return 0;
        }
    }
}