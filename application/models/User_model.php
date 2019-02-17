<?php
/**
 */
Class User_model extends MY_Model
{
    var $table_name = 'user';

    function findOne($where, $select = '*'){
        $this->db->select($select);
        $this->db->from($this->table_name);
        $this->db->join('account', 'account._id = user.account_id','left');
        $this->db->join('account_info', 'account_info.account_id = account._id','left');
        $this->db->join('user_get_type', 'user_get_type.account_id = user.account_id','left');
        $this->db->where($where);
        $query = $this->db->get();

        if($query->result()){
            return $query->first_row();

        }else{
            return false;
        }
    }
    function findOneUser($where, $select = '*'){
        $this->db->select($select);
        $this->db->from($this->table_name);
        $this->db->join('account', 'account._id = user.account_id','left');
        $this->db->join('account_info', 'account_info.account_id = account._id','left');
        $this->db->join('user_get_type', 'user_get_type.account_id = user.account_id','left');
        $this->db->where($where);
        $query = $this->db->get();

        if($query->result()){
            return $query->first_row();

        }else{
            return false;
        }
    }

    function get_pagination($where, $offset, $limit, $last_id = ''){
        $this->db->select(array(
            'account._id',
            'user.username',
            'user.email',
            'account.fullname',
            'account.is_active',
            'account.is_delete',
            'account_info.address',
            'account_info.gender',
            'account_info.phone',
            'account_info.birthday',
            'user.last_login'
        ));
        $this->db->from($this->table_name);
        $this->db->join('account', 'account._id = user.account_id');
        $this->db->join('account_info', 'account._id = account_info.account_id');
        $this->db->where($where);

        if(!empty($last_id)) {
            $this->db->where('account._id <', $last_id);
            $this->db->limit($limit, 0);
        }
        else{
            $this->db->limit($limit, $offset);
        }

        $this->db->order_by('account._id desc, account.create_time desc');
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
        $query = $this->db->get();

        if($query->result()){
            $result = $query->row_array();
            return $result['count'];
        }else{
            return 0;
        }
    }

}

?>
