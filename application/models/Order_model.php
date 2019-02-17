<?php
/**
 */
Class Order_model extends MY_Model
{
    var $table_name = 'order';

    function get_pagination($where, $select = '*', $offset, $limit, $last_id = ''){
        $this->db->select($select);
        $this->db->from($this->table_name);
        $this->db->join('record', 'record._id = order.record_id');
       $this->db->join('account', 'account._id = record.account_id');
        $this->db->join('account_info', 'account_info.account_id = record.account_id');
        $this->db->where($where);

        if(!empty($last_id)) {
            $this->where('order._id <', $last_id);
            $this->db->limit($limit, 0);
        }
        else{
            $this->db->limit($limit, $offset);
        }

        $this->db->order_by('order._id desc, order.create_time desc');
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
        $this->db->join('account', 'account._id = order.account_id');
        $this->db->join('account_info', 'account_info.account_id = order.account_id');
        $this->db->join('user', 'user.account_id = order.account_id');
        $this->db->join('order_item', 'order_item.order_id = order._id');
        $this->db->join('record', 'order_item.order_id = order._id');
        $this->db->where($where);
        $this->db->order_by('order._id desc, order.create_time desc');
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