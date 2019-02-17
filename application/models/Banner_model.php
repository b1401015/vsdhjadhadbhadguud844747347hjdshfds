<?php
/**
 */
Class Banner_model extends MY_Model
{
    var $table_name = 'banner';

    function get_pagination($where, $select = '*', $offset, $limit, $last_id = '')
    {
        $this->db->select($select);
        $this->db->from($this->table_name);
        $this->db->join('category', 'category._id = banner.category_id','left');
        $this->db->where($where);

        if (!empty($last_id)) {
            $this->db->where('banner._id <', $last_id);
            $this->db->limit($limit, 0);
        } else {
            $this->db->limit($limit, $offset);
        }

        $this->db->order_by('banner.ordinal desc, banner._id desc, banner.create_time desc');
        $query = $this->db->get();

        if ($query->result()) {
            return $query->result();

        } else {
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