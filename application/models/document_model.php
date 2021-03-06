<?php
class Document_model extends CI_Model{
	function __construct(){
		parent::__construct();
	}

	function add_document($filename, $equipment_id){
		$data = array(
			'equipment_id'=>$equipment_id,
			'document_date'=>$this->input->post('document_date'),
			'document_type_id'=>$this->input->post('document_type'),
			'note'=>$this->input->post('note'),
			'document_link' => $filename,
			'created_by'=>$this->session->userdata('logged_in')['user_id']
		);

		$this->db->trans_start(); //Transaction begins
        $this->db->insert('equipment_documents',$data); //Insert 
        $this->db->trans_complete(); //Transaction Ends
		if($this->db->trans_status()===TRUE) return true; else return false; //if transaction completed successfully return true, else false.
	}

	function delete_document($id){
		$data = array(
			'deleted_by' => $this->session->userdata('logged_in')['user_id'],
			'deleted_datetime' => date("Y-m-d H:i:s")
		);
		$this->db->trans_start(); //Transaction begins
		$this->db->where('id',$id);
		$this->db->update('equipment_documents',$data);
		$this->db->trans_complete(); //Transaction Ends
		if($this->db->trans_status()===TRUE) return -1; else return 1; //if transaction completed successfully return true, else false.
	}

	function get_document_by_id($id){
		$this->db->select('id,document_link')
			->from('equipment_documents')
			->where("id",$id);
		$query = $this->db->get();
		$result =  $query->row();
		if($result){
            return $result;       
        }else{
            return false;
        }
	}

	function get_documents_by_equipment_id($equipment_id){
		$this->db->select('id, document_type, document_date, document_link, equipment_documents.note as equipment_document_note, 
		created_user.first_name as created_user_first_name, created_user.last_name  as created_user_last_name, equipment_documents.created_datetime as doc_created_datetime,  
		updated_user.first_name as last_updated_user_first_name, updated_user.last_name as last_updated_user_last_name, equipment_documents.updated_datetime as doc_last_updated_datetime')
		->from('equipment_documents')
		->join('equipment_document_type','equipment_document_type.document_type_id = equipment_documents.document_type_id','left')
		->join('user as created_user','created_user.user_id=equipment_documents.created_by','left')
		->join('user as updated_user','updated_user.user_id=equipment_documents.updated_by','left')
		->where("equipment_id",$equipment_id)
		->where('deleted_by', NULL, false)
		->order_by('equipment_documents.created_datetime');
		$query = $this->db->get();
        $result =  $query->result();
		return $result;
	}
    
}