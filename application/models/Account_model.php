<?php
/**
 */
Class Account_model extends MY_Model
{
    var $table_name = 'account';

    function findOne($where, $select = '*', $join = ''){
        $this->db->select($select);
        $this->db->from($this->table_name);
        $this->db->join('account_info', 'account_info.account_id = account._id');
        $this->db->join('user', 'user.account_id = account._id');
        $this->db->join('user_get_type', 'user_get_type.account_id = account._id');
        $this->db->where($where);
        $query = $this->db->get();

        if($query->result()){
            return $query->first_row();

        }else{
            return false;
        }
    }
    function findOnePhone($where, $select = '*', $join = ''){
        $this->db->select($select);
        $this->db->from($this->table_name);
        $this->db->join('account_info', 'account_info.account_id = account._id');
        $this->db->join('user', 'user.account_id = account._id');
        $this->db->join('user_get_type', 'user_get_type.account_id = account._id');
        $this->db->join('phone_area_code', 'phone_area_code.short_name = account_info.short_name_phone');
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
    function findCodeIntroClient($where, $select = '*'){
        $this->db->select($select);
        $this->db->from($this->table_name);
        $this->db->join('user', 'user.account_id = account._id');
        $this->db->join('user_get_type', 'user_get_type.account_id = account._id');
        $this->db->where($where);
        $this->db->limit(1, 0);
        $this->db->order_by('account._id desc');
        $query = $this->db->get();

        if($query->result()){
            return $query->first_row();

        }else{
            return false;
        }
    }

    function get_pagination($where, $select = '*', $offset, $limit, $last_id = ''){
        $this->db->select($select);
        $this->db->from($this->table_name);
        $this->db->join('user', 'user.account_id = account._id');
        $this->db->join('account_info', 'account_info.account_id = account._id');
        $this->db->join('user_get_type', 'user_get_type.account_id = account._id');
        $this->db->where($where);

        if(!empty($last_id)) {
            $this->where('account._id <', $last_id);
            $this->db->limit($limit, 0);
        }
        else{
            $this->db->limit($limit, $offset);
        }

        $this->db->order_by('account._id desc');
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
    /*
     * Nam.Pham
     */
    function get_sale_with_pagination_search($where, $select = '*', $offset, $limit)
    {
        $this->db->select($select);
        $this->db->from($this->table_name);

        $this->db->join('account_has_project','account_has_project.account_id = account._id');
        $this->db->join('account_info','account_info.account_id = account._id');
        $this->db->join('project','project._id = account_has_project.project_id');
        $this->db->join('province','province._id = project.province_id');
        $this->db->join('district','district._id = project.district_id');
        $this->db->join('project_has_type','project._id = project_has_type.project_id');
        $this->db->join('project_type','project_type._id = project_has_type.project_type_id');
        //$this->db->join('rank','project_type._id = project_has_type.project_type_id');
        if($where != '') {
            $this->db->where($where);
        }
        $this->db->group_by('account.fullname');
        //$this->db->order_by('ordinal', 'desc');
        $this->db->limit($limit, $offset);
        $query = $this->db->get();

        if ($query->result()) {
            return $query->result();

        } else {
            return false;
        }
    }
    /*
     * Nam.Pham
     */
    function get_pagination_search($where, $select = '*', $offset, $limit){
        $this->db->select($select);
        $this->db->from($this->table_name);
        $this->db->join('user', 'user.account_id = account._id');
        $this->db->join('account_info', 'account_info.account_id = account._id');
        $this->db->join('user_get_type', 'user_get_type.account_id = account._id');
        if($where != '') {
            $this->db->where($where);
        }
        $this->db->limit($limit, $offset);
        //$this->db->order_by('account._id desc');
        $query = $this->db->get();

        if($query->result()){
            return $query->result();

        }else{
            return false;
        }
    }

}
