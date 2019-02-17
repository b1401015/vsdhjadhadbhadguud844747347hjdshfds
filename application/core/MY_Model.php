<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//Parent model which contains common queries
class MY_Model extends CI_Model {
	var $table_name = '';
	var $order = '';

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

	/**
	 * Add new record
	 */
	function create($doc) {
        if (!$doc || count($doc) == 0) {
            return FALSE;
        }
		if($this->db->insert($this->table_name, $doc)) {
		   return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * update record
	 */
	function update_by_condition($where, $data) {
        if (!$where || !$data || count($data) == 0) {
            return FALSE;
        }
		
	 	$this->db->where($where);
	 	$this->db->update($this->table_name, $data);

	 	return TRUE;
	}

    /**
     * Update or Insert record
     */
    function upsert_by_condition($where, $data_update, $data_insert) {
        if (!$where || !$data_update || count($data_update) == 0 || !$data_insert || count($data_insert) == 0) {
            return FALSE;
        }

        if (!$where || count($where) == 0) {
            return FALSE;
        }
        $this->db->where($where);
        $query = $this->db->get($this->table_name);

        $exists = $query->row();


        if(!empty($exists)) {
            //update
            $this->db->where($where);
            $this->db->update($this->table_name, $data_update);

            return TRUE;
        }
        else {
            //insert
            if ($this->db->insert($this->table_name, $data_insert)) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
    }

	/**
	 * delete row based on some conditions
	 */
	function delete_by_condition($where) {
		if (!$where || count($where) == 0) {
			return FALSE;
		}
		
	 	$this->db->where($where);
		$this->db->delete($this->table_name);
	 
		return TRUE;
	}
	
	/**
	 * custom query
	 */
	function custom_query($sql) {
        if (!$sql || count($sql) == 0) {
            return FALSE;
        }
		$rows = $this->db->query($sql);
		return $rows->result;
	}

	/**
	 * get no. of records by total
	 */
	function get_total($where) {
        if (!$where || count($where) == 0) {
            return FALSE;
        }
        $this->db->where($where);
		$query = $this->db->get($this->table_name);
		
		return $query->num_rows();
	}
	/**
	 * get 1 row
	 */
	function read_row($where) {
        if (!$where || count($where) == 0) {
            return FALSE;
        }
        $this->db->where($where);
		$query = $this->db->get($this->table_name);
		
		return $query->row();
	}
	/**
	 * check whether condition is existed
	 */
    function is_existed($where) {
        if (!$where || count($where) == 0) {
            return FALSE;
        }
	    $this->db->where($where);
		$query = $this->db->get($this->table_name);
		if($query->num_rows() > 0){
			return TRUE;
		} else {
			return FALSE;
		}
	}

    /**
     * get all with condition
     */
    function get_all($where = [], $select = '*') {
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

    /**
     * get all with condition
     */
    function get_first_row($where = [], $select = '*') {
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


}