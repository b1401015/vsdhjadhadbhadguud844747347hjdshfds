<?php
/**
 */
Class Record_model extends MY_Model
{
    var $table_name = 'record';

    function get_pagination($where, $select = '*', $offset, $limit, $last_id = ''){
        $this->db->select($select);
        $this->db->from($this->table_name);
        $this->db->join('category_has_record', 'category_has_record.record_id = record._id');
        $this->db->join('category', 'category._id = category_has_record.category_id');
        $this->db->join('account', 'account._id = record.account_id');
        $this->db->where($where);

        if(!empty($last_id)) {
            $this->db->where('record._id <', $last_id);
            $this->db->limit($limit, 0);
        }
        else{
            $this->db->limit($limit, $offset);
        }

        $this->db->order_by('record.ordinal desc, record._id desc, record.create_time desc');
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
        $this->db->join('category_has_record', 'category_has_record.record_id = record._id');
        $this->db->join('category', 'category._id = category_has_record.category_id');
        $this->db->join('account', 'account._id = record.account_id');
        $this->db->join('account_info', 'account_info.account_id = record.account_id');
        $this->db->where($where);
        $query = $this->db->get();

        if($query->result()){
            return $query->first_row();

        }else{
            return false;
        }
    }

    function find($where, $select = '*'){
        $this->db->select($select);
        $this->db->from($this->table_name);
        $this->db->where($where);
        $query = $this->db->get();

        if($query->result()){
            return $query->result();

        }else{
            return false;
        }
    }

    function count_total($where){
        $this->db->select('count(*) as count');
        $this->db->from($this->table_name);
        $this->db->join('category_has_record', 'category_has_record.record_id = record._id');
        $this->db->join('category', 'category._id = category_has_record.category_id');
        $this->db->where($where);
        $query = $this->db->get();

        if($query->result()){
            $result = $query->row_array();
            return $result['count'];
        }else{
            return 0;
        }
    }

    function count_total_all($where){
        $this->db->select('count(*) as count');
        $this->db->from($this->table_name);
      //  $this->db->join('category_has_record', 'category_has_record.record_id = record._id');
       // $this->db->join('category', 'category._id = category_has_record.category_id');
        $this->db->where($where);
        $query = $this->db->get();

        if($query->result()){
            $result = $query->row_array();
            return $result['count'];
        }else{
            return 0;
        }
    }

    function get_pagination_chage($where, $select = '*', $offset, $limit, $last_id = ''){
        $this->db->select($select);
        $this->db->from($this->table_name);
        $this->db->join('category_has_record', 'category_has_record.record_id = record._id','left');
        //$this->db->join('category', 'category._id = category_has_record.category_id');
        $this->db->where($where);

        if(!empty($last_id)) {
            $this->db->where('record._id <', $last_id);
            $this->db->limit($limit, 0);
        }
        else{
            $this->db->limit($limit, $offset);
        }
        $this->db->group_by('record._id');
        $this->db->order_by('record.ordinal desc, record._id desc, record.create_time desc');
        $query = $this->db->get();

        if($query->result()){
            return $query->result();

        }else{
            return false;
        }
    }

}