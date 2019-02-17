<?php
/**
 */
Class Account_get_notification_model extends MY_Model
{
    var $table_name = 'account_get_notification';

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

    function get_pagination($where, $select = '*', $offset, $limit, $last_id = ''){
        $this->db->select($select);
        $this->db->from($this->table_name);
        $this->db->join('notification', 'notification._id = account_get_notification.notification_id');
        $this->db->where($where);

        if(!empty($last_id)) {
            $this->db->where('notification._id <', $last_id);
            $this->db->limit($limit, 0);
        }
        else{
            $this->db->limit($limit, $offset);
        }

        $this->db->order_by('account_get_notification.create_time desc, account_get_notification._id desc');
        $this->db->group_by('account_get_notification.notification_id');
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
        $this->db->where($where);
//        $this->db->group_by('account_get_notification.notification_id');
        $query = $this->db->get();

        if($query->result()){
            $result = $query->row_array();
            return $result['count'];
        }else{
            return 0;
        }
    }

}
