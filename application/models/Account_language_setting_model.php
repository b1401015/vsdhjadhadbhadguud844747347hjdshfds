<?php
/**
 */
Class Account_language_setting_model extends MY_Model
{
    var $table_name = 'account_language_setting';

    function findOne($where, $select = 'language.id, language.code, language.name, language_translation._value, language_translation.extra_value'){
        $this->db->select($select);
        $this->db->from($this->table_name);
        $this->db->join('language', 'language._id = account_language_setting.language_id');
//        $this->db->join('language_translation', 'language_translation.keyword = language.keyword');
        $this->db->join('language_translation', 'language_translation.keyword = language._key');
        $this->db->where($where);
        $query = $this->db->get();

        if($query->result()){

            return $query->first_row();

        }else{
            return false;
        }
    }
}
