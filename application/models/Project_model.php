<?php

/**
 */
Class Project_model extends MY_Model
{
    var $table_name = 'project';
    /*
     * Shown list project on map with radius = 5km
     * @param lat,long
     */
    function get_nearby_with_pagination($where, $lng, $lat, $radius, $offset, $limit)
    {
        $select = " 
            project._id,
            project.img_src,
            project.name as name_project,           
            project.address,           
            project.latitude,
            project.longitude,
            project.type,
            investor.name as name_investor,
            project_has_type.duration_price,
            project_has_type.min_acreage,
            project_has_type.max_acreage,
            project_type.name as project_type,
            ( ACOS( COS( RADIANS({$lat}))
            * COS( RADIANS( project.latitude ) )
            * COS( RADIANS( project.longitude ) - RADIANS( {$lng} ))
            + SIN( RADIANS( {$lat} ))
            * SIN( RADIANS( project.latitude ))
            ) * 6371 ) AS distance,
            ";

        $this->db->select($select);
        $this->db->from($this->table_name);
        $this->db->join('project_has_type','project_has_type.project_id = project._id');
        $this->db->join('project_type','project_has_type.project_type_id = project_type._id');
        $this->db->join('investor','project.investor_id = investor._id');
        $this->db->where($where);

        $this->db->having("distance <= {$radius}");
        $this->db->group_by('project._id');
        $this->db->order_by('distance', 'ASC');
        $this->db->limit($limit, $offset);
        $query = $this->db->get();

        if ($query->result()) {
            return $query->result();

        } else {
            return false;
        }
    }

    function get_list_project($where, $lng, $lat, $offset, $limit)
    {
        $select = " 
            project._id,
            project.img_src,
            project.name as name_project,           
            project.address,           
            project.latitude,
            project.longitude,
            project.type,
            investor.name as name_investor,
            project_has_type.duration_price,
            project_has_type.min_acreage,
            project_has_type.max_acreage,
            project_type.name as project_type,
            ( ACOS( COS( RADIANS({$lat}))
            * COS( RADIANS( project.latitude ) )
            * COS( RADIANS( project.longitude ) - RADIANS( {$lng} ))
            + SIN( RADIANS( {$lat} ))
            * SIN( RADIANS( project.latitude ))
            ) * 6371 ) AS distance,
            ";

        $this->db->select($select);
        $this->db->from($this->table_name);
        $this->db->join('project_has_type','project_has_type.project_id = project._id');
        $this->db->join('project_type','project_has_type.project_type_id = project_type._id');
        $this->db->join('investor','project.investor_id = investor._id');
        $this->db->where($where);
        $this->db->order_by('distance', 'ASC');
        $this->db->limit($limit, $offset);
        $query = $this->db->get();

        if ($query->result()) {
            return $query->result();

        } else {
            return false;
        }
    }

    function get_project_with_pagination($where, $select = '*', $offset, $limit)
    {
        $this->db->select($select);
        $this->db->from($this->table_name);
        $this->db->join('project_has_type','project_has_type.project_id = project._id');
        $this->db->join('project_type','project_has_type.project_type_id = project_type._id');
        $this->db->join('investor','project.investor_id = investor._id');
        $this->db->where($where);

        $this->db->group_by('project._id');
        $this->db->order_by('ordinal', 'desc');
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
     * Date 2018/11/21
     */
    function get_project_pagination($where, $offset, $limit)
    {
        $select = "
            project._id,
            project.img_src,
            project.name as name_project,
            project.type,
            investor.name as name_investor,
            project_has_type.duration_price,
            project_type.name as project_type";

        $this->db->select($select);
        $this->db->from($this->table_name);
        $this->db->join('project_has_type','project_has_type.project_id = project._id','left');
        $this->db->join('project_type','project_has_type.project_type_id = project_type._id','left');
        $this->db->join('investor','project.investor_id = investor._id','left');
        $this->db->join('account_has_project','project._id = account_has_project.project_id');
        $this->db->join('account','account._id = account_has_project.account_id');
        $this->db->where($where);

        //$this->db->having("distance <= {$radius}");
        //$this->db->group_by('_id', 'ASC');
//        $this->db->order_by('distance', 'ASC');
        $this->db->limit($limit, $offset);
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
        $this->db->join('project_has_type','project_has_type.project_id = project._id','left');
        $this->db->join('project_type','project_has_type.project_type_id = project_type._id','left');
        $this->db->join('investor','project.investor_id = investor._id','left');
        $this->db->where($where);
        $query = $this->db->get();

        if($query->result()){
            return $query->first_row();

        }else{
            return false;
        }
    }
    function findOne_Appointment($where, $select = '*'){
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
    /*
     * Nam.Pham
     */
    function get_project_with_pagination_search($where, $select = '*', $offset, $limit)
    {
        $this->db->select($select);
        $this->db->from($this->table_name);

        $this->db->join('province','province._id = project.province_id');
        $this->db->join('district','district._id = project.district_id');
        $this->db->join('project_has_type','project._id = project_has_type.project_id');
        $this->db->join('project_type','project_type._id = project_has_type.project_type_id');
        //$this->db->join('rank','project_type._id = project_has_type.project_type_id');
        if($where != '') {
            $this->db->where($where);
        }
        $this->db->group_by('project.name');
        //$this->db->order_by('ordinal', 'desc');
        $this->db->limit($limit, $offset);
        $query = $this->db->get();

        if ($query->result()) {
            return $query->result();

        } else {
            return false;
        }
    }

}
