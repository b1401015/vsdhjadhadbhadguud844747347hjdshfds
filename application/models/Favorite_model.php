<?php
/**
 */
Class Favorite_model extends MY_Model
{
    var $table_name = 'favorite';

    function get_pagination_project($where, $select = '*', $offset, $limit, $last_id = '')
    {
        $this->db->select($select);
        $this->db->from($this->table_name);
        $this->db->join('project','project._id = favorite.project_id');
        $this->db->join('project_has_type','project_has_type.project_id = favorite.project_id');
        $this->db->join('project_type','project_has_type.project_type_id = project_type._id');
        $this->db->join('investor','project.investor_id = investor._id');
        $this->db->where($where);
        if (!empty($last_id)) {
            $this->db->where('_id <', $last_id);
            $this->db->limit($limit, 0);
        } else {
            $this->db->limit($limit, $offset);
        }
        $this->db->order_by('_id desc');
        $query = $this->db->get();
        if ($query->result()) {
            return $query->result();
        } else {
            return false;
        }
    }
    function get_pagination_sale($where, $select = '*', $offset, $limit, $last_id = '')
    {
        $this->db->select($select);
        $this->db->from($this->table_name);
        $this->db->join('account','account._id = favorite.seller_id');
        $this->db->join('account_info','account_info.account_id = favorite.seller_id');
        $this->db->where($where);
        if (!empty($last_id)) {
            $this->db->where('_id <', $last_id);
            $this->db->limit($limit, 0);
        } else {
            $this->db->limit($limit, $offset);
        }
        $this->db->order_by('_id desc');
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
}
