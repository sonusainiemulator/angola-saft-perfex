<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Angola_saft_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get hash for a specific document
     */
    public function get_hash($rel_id, $rel_type = 'invoice')
    {
        $this->db->where('rel_id', $rel_id);
        $this->db->where('rel_type', $rel_type);
        return $this->db->get(db_prefix() . 'saft_ao_hashes')->row();
    }

    /**
     * Get the chain history
     */
    public function get_chain($limit = 50)
    {
        $this->db->order_by('id', 'DESC');
        $this->db->limit($limit);
        return $this->db->get(db_prefix() . 'saft_ao_hashes')->result_array();
    }
}
