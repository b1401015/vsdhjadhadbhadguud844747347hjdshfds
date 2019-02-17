<?php
/**
 */
Class Record_file_model extends MY_Model
{
    var $table_name = 'record_file';

    function find($where, $select = '*'){
        $this->db->select($select);
        $this->db->from($this->table_name);
        $this->db->join('record', 'record._id = record_file.record_id');
        $this->db->where($where);
        $this->db->order_by('record_file.ordinal asc, record_file._id desc, record_file.create_time desc');
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
        $this->db->join('record', 'record._id = record_file.record_id');
        $this->db->where($where);
        $this->db->order_by('record_file.ordinal asc, record_file._id desc, record_file.create_time desc');
        $query = $this->db->get();

        if($query->result()){
            return $query->first_row();

        }else{
            return false;
        }
    }
}