<?php
class Master_model extends CI_Model
{
    private $user_party_ids = [];
    private $user_id = '';
    function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        $user_data = $this->session->userdata('logged_in');
        $this->user_id = $user_data['user_id'];
        $user_parties = $this->user_model->user_parties($this->user_id);
        foreach ($user_parties as $key => $value) {
            array_push($this->user_party_ids, $value->party_id);
        }
    }


    function get_defaults($id)
    {
        $this->db->select('*')->from('defaults')->where('default_id', $id);
        $query = $this->db->get();
        $result = $query->row();
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }

    function get_data($type)
    {
        if ($type == "equipment_type") {
            $this->db->select("*")->from("equipment_type");
            $this->db->order_by("equipment_type");
        } else if ($type == "journal_type") {
            $this->db->select("*")->from("journal_type");
            $this->db->order_by("journal_type");
        } else if ($type == "location") {
            $this->db->select("location_id, location")->from("location");
            $this->db->order_by("location");
        } else if ($type == "party") {
            $this->db->select("party_id, party.party_type_id, party_type, party_name")
                ->join('party_type', 'party_type.party_type_id = party.party_type_id', 'left')
                ->from("party");
            $this->db->order_by("party_name");
        } else if ($type == "party_type") {
            $this->db->select("party_type_id, party_type")->from("party_type");
            $this->db->order_by("party_type");
        } else if ($type == "equipment_category") {
            $this->db->select("*")->from("equipment_category");
            $this->db->order_by("equipment_category");
        } else if ($type == "equipment_procurement_type") {
            $this->db->select("*")->from("equipment_procurement_type");
            $this->db->order_by("procurement_type");
        } else if ($type == "equipment_functional_status") {
            $this->db->select("*")->from("equipment_functional_status");
            $this->db->order_by("working_status");
        } else if ($type == "equipment_procurement_status") {
            $this->db->select("*")->from("equipment_procurement_status");
            $this->db->order_by("procurement_status");
        } else if ($type == "journal_type") {
            $this->db->select("*")->from("journal_type");
            $this->db->order_by("journal_type");
        } else if ($type == "district") {
            $this->db->select("*")->from("district");
            $this->db->order_by("district");
        } else if ($type == "state") {
            $this->db->select("*")->from("state");
            $this->db->order_by("state");
        } else if ($type == "equipment_document_type") {
            $this->db->select("*")->from("equipment_document_type");
            $this->db->order_by("document_type");
        } else if ($type == "location") {
            $this->db->select("*")->from("location");
            $this->db->order_by("location");
        }
        $query = $this->db->get();
        return $query->result();
    }

    function get_equipment_data($default_rowsperpage)
    {
        if ($this->input->post('page_no')) {
            $page_no = (int) $this->input->post('page_no');
        } else {
            $page_no = 1;
        }

        if ($this->input->post('rows_per_page')) {
            $rows_per_page = $this->input->post('rows_per_page');
        } else {
            $rows_per_page = $default_rowsperpage;
        }

        $start = ($page_no - 1) * $rows_per_page;
        if ($this->input->post('equipment_category')) {
            $this->db->where('equipment_category_id', $this->input->post('equipment_category'));
        }
        if ($this->input->post('donor_party')) {
            $this->db->where('equipment.donor_party_id', $this->input->post('donor_party'));
        }
        if ($this->input->post('procured_by_party')) {
            $this->db->where('equipment.procured_by_party_id', $this->input->post('procured_by_party'));
        }
        if ($this->input->post('supplier_party')) {
            $this->db->where('equipment.supplier_party_id', $this->input->post('supplier_party'));
        }
        if ($this->input->post('manufactured_party')) {
            $this->db->where('equipment.manufacturer_party_id', $this->input->post('manufactured_party'));
        }
        if ($this->input->post('equipment_type')) {
            $this->db->where('equipment.equipment_type_id', $this->input->post('equipment_type'));
        }
        if ($this->input->post('from_date') && $this->input->post('to_date')) {
            $from_date = date("Y-m-d", strtotime($this->input->post('from_date')));
            $to_date = date("Y-m-d", strtotime($this->input->post('to_date')));
            $this->db->where("(invoice_date BETWEEN '$from_date' AND '$to_date')");
        }
        // else if(!$this->input->post('from_date') || !$this->input->post('to_date')){
        // 	$from_date= $this->input->post('from_date')?$this->input->post('from_date') : date("Y-m-d", strtotime("-1 month"));
        // 	$to_date= $this->input->post('to_date')?$this->input->post('to_date') : date("Y-m-d");
        //     $this->db->where("(invoice_date BETWEEN '$from_date' AND '$to_date')");
        // }
        /* if($this->input->post('location')){
            $this->db->where('location_id', $this->input->post('location'));
        } */


        $this->db->select("*, supplier_party.party_name as supplier_party_name, procured_by_party.party_name as procured_by_party_name,
        donor_party.party_name as donor_party_name,manufacturer_party.party_name as manufacturer_party_name")
            ->from("equipment")
            ->join('equipment_type', 'equipment_type.equipment_type_id=equipment.equipment_type_id', 'left')
            ->join('equipment_category', 'equipment_category.id=equipment_type.equipment_category_id', 'left')
            ->join('equipment_procurement_status', 'equipment_procurement_status.equipment_procurement_status_id=equipment.procurement_status_id', 'left')
            ->join('equipment_procurement_type', 'equipment_procurement_type.equipment_procurement_type_id=equipment.equipment_procurement_type_id', 'left')
            ->join('equipment_functional_status', 'equipment_functional_status.functional_status_id=equipment.functional_status_id', 'left')
            ->join('journal_type', 'journal_type.journal_type_id=equipment.journal_type_id', 'left')
            ->join('party as supplier_party', 'supplier_party.party_id=equipment.supplier_party_id', 'left')
            ->join('party as procured_by_party', 'procured_by_party.party_id=equipment.procured_by_party_id', 'left')
            ->join('party as donor_party', 'donor_party.party_id=equipment.donor_party_id', 'left')
            ->join('party as manufacturer_party', 'manufacturer_party.party_id=equipment.manufacturer_party_id', 'left')

            ->where_in('equipment.procured_by_party_id', $this->user_party_ids)
            // ->join('equipment_location_log','equipment_location_log.equipment_id=equipment.equipment_id','inner')
            // ->join('location','location.location_id=equipment_location_log.location_id','left')
            // ->group_by('equipment_location_log.equipment_id')
            // ->order_by("delivery_date", 'desc')
            ->order_by("invoice_date", 'desc');
        $this->db->limit($rows_per_page, $start);
        $query = $this->db->get();
        return $query->result();
    }

    function get_equipment_count()
    {
        if ($this->input->post('equipment_category')) {
            $this->db->where('equipment_category_id', $this->input->post('equipment_category'));
        }
        if ($this->input->post('donor_party')) {
            $this->db->where('equipment.donor_party_id', $this->input->post('donor_party'));
        }
        if ($this->input->post('procured_by_party')) {
            $this->db->where('equipment.procured_by_party_id', $this->input->post('procured_by_party'));
        }
        if ($this->input->post('supplier_party')) {
            $this->db->where('equipment.supplier_party_id', $this->input->post('supplier_party'));
        }
        if ($this->input->post('manufactured_party')) {
            $this->db->where('equipment.manufacturer_party_id', $this->input->post('manufactured_party'));
        }
        if ($this->input->post('equipment_type')) {
            $this->db->where('equipment.equipment_type_id', $this->input->post('equipment_type'));
        }
        if ($this->input->post('from_date') && $this->input->post('to_date')) {
            $from_date = date("Y-m-d", strtotime($this->input->post('from_date')));
            $to_date = date("Y-m-d", strtotime($this->input->post('to_date')));
            $this->db->where("(invoice_date BETWEEN '$from_date' AND '$to_date')");
        }
        // else if(!$this->input->post('from_date') || !$this->input->post('to_date')){
        // 	$from_date= $this->input->post('from_date')?$this->input->post('from_date') : date("Y-m-d", strtotime("-1 month"));
        // 	$to_date= $this->input->post('to_date')?$this->input->post('to_date') : date("Y-m-d");
        //     $this->db->where("(invoice_date BETWEEN '$from_date' AND '$to_date')");
        // }
        /* if($this->input->post('location')){
            $this->db->where('location_id', $this->input->post('location'));
        } */
        $this->db->select("COUNT(*) as count")
            ->from("equipment")
            ->join('equipment_type', 'equipment_type.equipment_type_id=equipment.equipment_type_id', 'left')
            ->where_in('equipment.procured_by_party_id', $this->user_party_ids);
        // ->join('equipment_location_log','equipment_location_log.equipment_id=equipment.equipment_id','inner')
        // ->join('location','location.location_id=equipment_location_log.location_id','left')
        // ->group_by('equipment_location_log.equipment_id')
        // ->order_by("delivery_date", 'desc')	
        $query = $this->db->get();
        return $query->row();
    }

    function get_parties_by_party_type($party_type)
    {
        if ($party_type == 'donor_party') {
            $this->db->join('equipment', 'equipment.donor_party_id=party.party_id', 'inner');
        } else if ($party_type == 'procured_by_party') {
            $this->db->join('equipment', 'equipment.procured_by_party_id=party.party_id', 'inner');
        } else if ($party_type == 'supplier_party') {
            $this->db->join('equipment', 'equipment.supplier_party_id=party.party_id', 'inner');
        } else {
            // when party type is manufacturer
            $this->db->join('equipment', 'equipment.manufacturer_party_id=party.party_id', 'inner');
        }

        $this->db->select("party_id, party_name")
            ->from('party')
            ->group_by('party_id')
            ->order_by("party_name");
        $query = $this->db->get();
        return $query->result();
    }

    function get_equipment_by_id($equipment_id)
    {
        $this->db->select(
            "equipment_id, equipment_name, equipment.equipment_type_id, equipment_category_id, equipment_type, procurement_type, model, serial_number, mac_address, asset_number,purchase_order_date, 
                    donor_party_id, procured_by_party_id, supplier_party_id, manufacturer_party_id, cost, invoice_number, invoice_date, supply_date, installation_date, warranty_start_date, warranty_end_date,
                    procurement_status_id, equipment.equipment_procurement_type_id, equipment.functional_status_id, created_user.first_name as created_user_first_name, created_user.last_name  as created_user_last_name,
                    created_by, equipment.created_datetime as equipment_created_datetime, updated_user.first_name as last_updated_user_first_name , updated_user.last_name as last_updated_user_last_name , 
                    equipment.updated_datetime as equipment_last_updated_datetime , equipment_category, working_status as functional_status, procurement_status, equipment.journal_type_id,journal_type, journal_number, journal_date,note"
        )
            ->from("equipment")
            ->join('equipment_type', 'equipment_type.equipment_type_id=equipment.equipment_type_id', 'left')
            ->join('equipment_category', 'equipment_category.id=equipment_type.equipment_category_id', 'left')
            ->join('equipment_functional_status', 'equipment_functional_status.functional_status_id=equipment.functional_status_id', 'left')
            ->join('equipment_procurement_status', 'equipment_procurement_status.equipment_procurement_status_id=equipment.procurement_status_id', 'left')
            ->join('equipment_procurement_type', 'equipment_procurement_type.equipment_procurement_type_id=equipment.equipment_procurement_type_id', 'left')
            ->join('user as created_user', 'created_user.user_id=equipment.created_by', 'left')
            ->join('user as updated_user', 'updated_user.user_id=equipment.updated_by', 'left')
            ->join('journal_type', 'journal_type.journal_type_id=equipment.journal_type_id', 'left')
            ->where("equipment_id", $equipment_id)
            ->order_by("equipment_id");
        $query = $this->db->get();
        $result = $query->row();
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }

    function get_parties_of_user()
    {
        $this->db->select('party.party_id, party_name')
            ->from('party')
            ->join('user_party_link', 'user_party_link.party_id=party.party_id', 'left')
            ->where('user_id', $this->user_id)
            ->order_by('party_name', 'desc');

        $query = $this->db->get();
        $result = $query->result();
        return $result;
    }

    function get_equipment_current_location($equipment_id)
    {
        $this->db->select("*")
            ->from("equipment_location_log")
            ->join('location', 'equipment_location_log.location_id=location.location_id', 'left')
            ->join('district', 'district.district_id=location.district_id', 'left')
            ->join('state', 'state.state_id=district.state_id', 'left')
            ->where('equipment_id', $equipment_id)
            ->order_by('delivery_date', 'desc')
            ->limit(1);
        $query = $this->db->get();
        // var_dump($this->db->last_query());
        $result = $query->row();
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }

    function add_equipment()
    {
        $data = array(
            'donor_party_id' => $this->input->post('donor_party'),
            'procured_by_party_id' => $this->input->post('procured_by_party'),
            'supplier_party_id' => $this->input->post('supplier_party'),
            'manufacturer_party_id' => $this->input->post('manufactured_party'),
            'equipment_type_id' => $this->input->post('equipment_type'),
            'equipment_name' => $this->input->post('equipment_name'),
            'model' => $this->input->post('model'),
            'serial_number' => $this->input->post('serial_number'),
            'mac_address' => $this->input->post('mac_address'),
            'asset_number' => $this->input->post('asset_number'),
            'purchase_order_date' => $this->input->post('purchase_order_date'),
            'cost' => $this->input->post('cost'),
            'invoice_number' => $this->input->post('invoice_number'),
            'invoice_date' => $this->input->post('invoice_date'),
            'supply_date' => $this->input->post('supply_date'),
            'installation_date' => $this->input->post('installation_date'),
            'warranty_start_date' => $this->input->post('warranty_start_date'),
            'warranty_end_date' => $this->input->post('warranty_end_date'),
            'journal_type_id' => $this->input->post('journal_type'),
            'journal_number' => $this->input->post('journal_number'),
            'procurement_status_id' => $this->input->post('procurement_status'),
            'journal_date' => $this->input->post('journal_date'),
            'equipment_procurement_type_id' => $this->input->post('procurement_type'),
            'functional_status_id' => $this->input->post('functional_status'),
            'note' => $this->input->post('note'),
            'created_by' => $this->user_id
        );

        $this->db->trans_start(); //Transaction begins
        $this->db->insert('equipment', $data); //Insert 
        $this->db->trans_complete(); //Transaction Ends
        if ($this->db->trans_status() === TRUE)
            return true;
        else
            return false; //if transaction completed successfully return true, else false.
    }

    function update_equipment($equipment_id)
    {
        $data = array(
            'donor_party_id' => $this->input->post('donor_party'),
            'procured_by_party_id' => $this->input->post('procured_by_party'),
            'supplier_party_id' => $this->input->post('supplier_party'),
            'manufacturer_party_id' => $this->input->post('manufactured_party'),
            'equipment_type_id' => $this->input->post('equipment_type'),
            'equipment_name' => $this->input->post('equipment_name'),
            'model' => $this->input->post('model'),
            'serial_number' => $this->input->post('serial_number'),
            'mac_address' => $this->input->post('mac_address'),
            'asset_number' => $this->input->post('asset_number'),
            'purchase_order_date' => $this->input->post('purchase_order_date'),
            'cost' => $this->input->post('cost'),
            'invoice_number' => $this->input->post('invoice_number'),
            'invoice_date' => $this->input->post('invoice_date'),
            'supply_date' => $this->input->post('supply_date'),
            'installation_date' => $this->input->post('installation_date'),
            'warranty_start_date' => $this->input->post('warranty_start_date'),
            'warranty_end_date' => $this->input->post('warranty_end_date'),
            'journal_type_id' => $this->input->post('journal_type'),
            'journal_number' => $this->input->post('journal_number'),
            'procurement_status_id' => $this->input->post('procurement_status'),
            'journal_date' => $this->input->post('journal_date'),
            'equipment_procurement_type_id' => $this->input->post('procurement_type'),
            'functional_status_id' => $this->input->post('functional_status'),
            'note' => $this->input->post('note'),
            'updated_by' => $this->user_id,
            'updated_datetime' => date("Y-m-d H:i:s")
        );
        $this->db->trans_start(); //Transaction begins
        $this->db->where('equipment_id', $equipment_id);
        $this->db->update('equipment', $data); //updating the question w.r.t equipment_id
        $this->db->trans_complete(); //Transaction Ends
        if ($this->db->trans_status() === TRUE)
            return true;
        else
            return false; //if transaction completed successfully return true, else false.
    }

    function add_location()
    {
        $data = array(
            'location' => $this->input->post('location'),
            'district_id' => $this->input->post('district'),
        );
        $this->db->trans_start(); //Transaction begins
        $this->db->insert('location', $data); //Insert 
        $this->db->trans_complete(); //Transaction Ends
        if ($this->db->trans_status() === TRUE)
            return true;
        else
            return false; //if transaction completed successfully return true, else false.
    }

    function add_equipment_location_log($equipment_id)
    {
        $data = array(
            'equipment_id' => $equipment_id,
            'receiver_party_id' => $this->input->post('receiver_party_id'),
            'location_id' => $this->input->post('location'),
            'address' => $this->input->post('address'),
            'delivery_date' => $this->input->post('delivery_date'),
            'note' => $this->input->post('note'),
            'created_by' => $this->user_id
        );
        $this->db->trans_start(); //Transaction begins
        $this->db->insert('equipment_location_log', $data); //Insert 
        $this->db->trans_complete(); //Transaction Ends
        if ($this->db->trans_status() === TRUE)
            return true;
        else
            return false; //if transaction completed successfully return true, else false.
    }

    function get_equipment_location_history($equipment_id)
    {
        $this->db->select("equipment_location_log_id, equipment_location_log.equipment_id, party_name, location, address, state.state, district,
         delivery_date, equipment_location_log.note, created_user.first_name as created_user_first_name, created_user.last_name  as created_user_last_name,
         equipment_location_log.created_datetime as location_created_datetime, updated_user.first_name as last_updated_user_first_name , 
         updated_user.last_name as last_updated_user_last_name, equipment_location_log.updated_datetime as last_updated_datetime")
            ->from("equipment_location_log")
            ->join('equipment', 'equipment.equipment_id= equipment_location_log.equipment_id', 'left')
            ->join('party', 'party.party_id= equipment_location_log.receiver_party_id', 'left')
            ->join('location', 'location.location_id=equipment_location_log.location_id', 'left')
            ->join('district', 'district.district_id=location.district_id', 'left')
            ->join('state', 'state.state_id=district.state_id', 'left')
            ->join('user as created_user', 'created_user.user_id=equipment_location_log.created_by', 'left')
            ->join('user as updated_user', 'updated_user.user_id=equipment_location_log.updated_by', 'left')
            ->where('equipment_location_log.equipment_id', $equipment_id)
            ->order_by('delivery_date', 'desc');

        $query = $this->db->get();
        $result = $query->result();
        return $result;
    }

    function get_party_by_id($party_id)
    {
        $this->db->select('party_name, party.party_type_id, party_type, party_address, place, party.district_id, district, district.state_id, state.state,
                bank_account_no, bank_name, bank_branch, bank_branch_ifsc,party_email,
                party_phone, party_pan, party.note, created_user.first_name as created_user_first_name, created_user.last_name  as created_user_last_name,
                created_by, party.created_datetime as party_created_datetime, updated_user.first_name as last_updated_user_first_name, 
                updated_user.last_name as last_updated_user_last_name, party.updated_datetime as party_last_updated_datetime')
            ->from('party')
            ->join('district', 'district.district_id=party.district_id', 'left')
            ->join('state', 'state.state_id=district.state_id', 'left')
            ->join('party_type', 'party_type.party_type_id = party.party_type_id', 'left')
            ->join('user as created_user', 'created_user.user_id=party.created_by', 'left')
            ->join('user as updated_user', 'updated_user.user_id=party.updated_by', 'left')
            ->where("party_id", $party_id)
            ->order_by('party_name');
        $query = $this->db->get();
        $result = $query->row();
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }

    function add_party()
    {
        $data = array(
            'party_type_id' => $this->input->post('party_type'),
            'party_name' => $this->input->post('party_name'),
            'party_address' => $this->input->post('party_address'),
            'place' => $this->input->post('party_place'),
            'district_id' => $this->input->post('district'),
            'bank_account_no' => $this->input->post('bank_account_no'),
            'bank_name' => $this->input->post('bank_name'),
            'bank_branch' => $this->input->post('bank_branch'),
            'bank_branch_ifsc' => $this->input->post('branch_ifsc'),
            'party_email' => $this->input->post('party_email'),
            'party_phone' => $this->input->post('party_phone'),
            // 'contact_person_id'=>$this->input->post('contact_person'),
            'party_pan' => $this->input->post('party_pan'),
            'note' => $this->input->post('note'),
            'created_by' => $this->user_id,
        );
        $this->db->trans_start(); //Transaction begins
        $this->db->insert('party', $data); //Insert 
        $this->db->trans_complete(); //Transaction Ends
        if ($this->db->trans_status() === TRUE)
            return true;
        else
            return false; //if transaction completed successfully return true, else false.
    }

    function update_party($party_id)
    {
        $data = array(
            'party_name' => $this->input->post('party_name'),
            'party_type_id' => $this->input->post('party_type'),
            'party_address' => $this->input->post('party_address'),
            'place' => $this->input->post('party_place'),
            'district_id' => $this->input->post('district'),
            'bank_account_no' => $this->input->post('bank_account_no'),
            'bank_name' => $this->input->post('bank_name'),
            'bank_branch' => $this->input->post('bank_branch'),
            'bank_branch_ifsc' => $this->input->post('bank_branch_ifsc'),
            'party_email' => $this->input->post('party_email'),
            'party_phone' => $this->input->post('party_phone'),
            // 'contact_person_id'=>$this->input->post('contact_person'),
            'party_pan' => $this->input->post('party_pan'),
            'note' => $this->input->post('note'),
            'updated_by' => $this->user_id,
            'updated_datetime' => date("Y-m-d H:i:s")
        );
        $this->db->trans_start(); //Transaction begins
        $this->db->where('party_id', $party_id);
        $this->db->update('party', $data); //updating the question w.r.t equipment_id
        $this->db->trans_complete(); //Transaction Ends
        if ($this->db->trans_status() === TRUE)
            return true;
        else
            return false; //if transaction completed successfully return true, else false.
    }

    function get_locations()
    {
        if ($this->input->post('district')) {
            $this->db->where('district.district_id', $this->input->post('district'));
        }
        if ($this->input->post('state')) {
            $this->db->where('state.state_id', $this->input->post('state'));
        }

        $this->db->select('*')->from("location")
            ->join('district', 'district.district_id=location.district_id', 'left')
            ->join('state', 'state.state_id=district.state_id', 'left')
            ->order_by('state.state', 'asc')
            ->order_by('district.district', 'asc')
            ->order_by('location', 'asc');
        $query = $this->db->get();

        $result = $query->result();
        return $result;
    }
}
