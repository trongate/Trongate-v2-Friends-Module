<?php
/**
 * Friends Controller - Manages friend birthday records with full CRUD operations
 * 
 * Demonstrates proper form handling, date input patterns, pagination,
 * and validation in Trongate v2 framework.
 */
class Friends extends Trongate {

    private int $default_limit = 20;
    private array $per_page_options = [10, 20, 50, 100];

    /**
     * Default entry point - redirects to manage page
     * 
     * @return void
     */
    public function index(): void {
        redirect('friends/manage');
    }

    /**
     * Display paginated list of friends with per-page selector
     * 
     * Shows friends in a table with pagination controls. Includes
     * dropdown for selecting number of records per page.
     * 
     * @return void
     */
    public function manage(): void {
        $this->trongate_security->_make_sure_allowed();
        
        $limit = $this->get_limit();
        $offset = $this->get_offset();
        $rows = $this->model->fetch_records($limit, $offset);
        $rows = $this->model->prepare_records_for_display($rows);
        
        $data = [
            'rows' => $rows,
            'pagination_data' => $this->get_pagination_data($limit),
            'view_module' => 'friends',
            'view_file' => 'manage',
            'per_page_options' => $this->per_page_options,
            'selected_per_page' => $this->get_selected_per_page()
        ];
        
        $this->templates->admin($data);
    }

    /**
     * Display form for creating or editing a friend
     * 
     * Shows friend form with appropriate headline and action URL.
     * Automatically repopulates form with submitted data on validation errors.
     * 
     * @return void
     */
    public function create(): void {
        $this->trongate_security->_make_sure_allowed();
        
        $update_id = segment(3, 'int');
        $data = $this->model->get_form_data($update_id);

        // Add view-specific data
        $data['headline'] = ($update_id > 0) ? 'Update Friend Record' : 'Create New Friend Record';
        $data['cancel_url'] = ($update_id > 0) 
            ? BASE_URL.'friends/show/'.$update_id 
            : BASE_URL.'friends/manage';
        $data['form_location'] = BASE_URL.'friends/submit/'.$update_id;
        $data['view_module'] = 'friends';
        $data['view_file'] = 'create';
        
        $this->templates->admin($data);
    }

    /**
     * Handle form submission for creating/updating friends
     * 
     * Validates input (including date validation) and saves to database.
     * Includes automatic CSRF validation and proper date handling.
     * 
     * @return void
     */
    public function submit(): void {
        $this->trongate_security->_make_sure_allowed();
        
        $submit = post('submit', true);
        
        if ($submit === 'Submit') {
            // Set validation rules
            $this->validation->set_rules('first_name', 'first name', 'required|min_length[2]|max_length[50]');
            $this->validation->set_rules('last_name', 'last name', 'required|min_length[2]|max_length[50]');
            $this->validation->set_rules('email_address', 'email address', 'required|valid_email|max_length[100]');
            $this->validation->set_rules('birthday', 'birthday', 'required|valid_date');
            
            if ($this->validation->run() === true) {
                $update_id = segment(3, 'int');
                $data = $this->model->get_post_data_for_database();
                
                if ($update_id > 0) {
                    $this->db->update($update_id, $data, 'friends');
                    $flash_msg = 'Friend record updated successfully';
                } else {
                    $update_id = $this->db->insert($data, 'friends');
                    $flash_msg = 'Friend record created successfully';
                }
                
                set_flashdata($flash_msg);
                redirect('friends/show/'.$update_id);
            } else {
                $this->create();
            }
        } else {
            redirect('friends/manage');
        }
    }

    /**
     * Display detailed view of a single friend
     * 
     * Shows all friend details with edit/delete options.
     * Automatically handles missing records with 404 page.
     * 
     * @return void
     */
    public function show(): void {
        $this->trongate_security->_make_sure_allowed();
        
        $update_id = segment(3, 'int');
        
        if ($update_id === 0) {
            $this->not_found();
            return;
        }

        $record = $this->db->get_where($update_id, 'friends');
        
        if (empty($record)) {
            $this->not_found();
            return;
        }
        
        $data = (array) $record;
        $data = $this->model->prepare_for_display($data);
        
        // Add view data
        $data['update_id'] = $update_id;
        $data['headline'] = 'Friend Details';
        $data['back_url'] = $this->get_back_url();
        $data['view_module'] = 'friends';
        $data['view_file'] = 'show';
        
        $this->templates->admin($data);
    }

    /**
     * Display confirmation page before deleting a friend
     * 
     * Shows confirmation dialog with friend details to prevent accidental deletion.
     * 
     * @return void
     */
    public function delete_conf(): void {
        $this->trongate_security->_make_sure_allowed();
        
        $update_id = segment(3, 'int');
        
        if ($update_id === 0) {
            $this->not_found();
            return;
        }
        
        $data = $this->model->get_data_for_edit($update_id);
        
        if (empty($data)) {
            $this->not_found();
            return;
        }
        
        // Prepare for display (for showing friend name in confirmation)
        $data = $this->model->prepare_for_display($data);
        
        $data['update_id'] = $update_id;
        $data['headline'] = 'Delete Friend Record';
        $data['cancel_url'] = BASE_URL.'friends/show/'.$update_id;
        $data['form_location'] = BASE_URL.'friends/submit_delete/'.$update_id;
        $data['view_module'] = 'friends';
        $data['view_file'] = 'delete_conf';
        
        $this->templates->admin($data);
    }

    /**
     * Handle friend deletion after confirmation
     * 
     * Verifies confirmation and deletes record from database.
     * Includes safety checks to prevent unauthorized deletion.
     * 
     * @return void
     */
    public function submit_delete(): void {
        $this->trongate_security->_make_sure_allowed();
        
        $submit = post('submit', true);
        
        if ($submit === 'Yes - Delete Now') {
            $update_id = segment(3, 'int');
            
            if ($update_id === 0) {
                redirect('friends/manage');
                return;
            }
            
            $record = $this->model->get_data_for_edit($update_id);
            
            if (empty($record)) {
                redirect('friends/manage');
                return;
            }
            
            $this->db->delete($update_id, 'friends');
            
            set_flashdata('The record was successfully deleted');
            redirect('friends/manage');
        } else {
            redirect('friends/manage');
        }
    }

    /**
     * Set number of records per page for pagination
     * 
     * Stores user preference in session for consistent pagination across requests.
     * 
     * @return void
     */
    public function set_per_page(): void {
        $this->trongate_security->_make_sure_allowed();
        
        $selected_index = segment(3, 'int');
        
        if (!isset($this->per_page_options[$selected_index])) {
            $selected_index = 1;
        }
        
        $_SESSION['selected_per_page'] = $selected_index;
        redirect('friends/manage');
    }

    /**
     * Generate pagination configuration data
     * 
     * @param int $limit Number of records per page
     * @return array Pagination configuration for template
     */
    private function get_pagination_data(int $limit): array {
        return [
            'total_rows' => $this->db->count('friends'),
            'page_num_segment' => 3,
            'limit' => $limit,
            'pagination_root' => 'friends/manage',
            'record_name_plural' => 'friends',
            'include_showing_statement' => true
        ];
    }
    
    /**
     * Get current pagination limit from session
     * 
     * @return int Number of records to display per page
     */
    private function get_limit(): int {
        if (isset($_SESSION['selected_per_page'])) {
            return $this->per_page_options[$_SESSION['selected_per_page']];
        }
        return $this->default_limit;
    }
    
    /**
     * Calculate pagination offset based on page number
     * 
     * @return int Database offset for current page
     */
    private function get_offset(): int {
        $page_num = segment(3, 'int');
        return ($page_num > 1) ? ($page_num - 1) * $this->get_limit() : 0;
    }
    
    /**
     * Get selected per-page index from session
     * 
     * @return int Index of selected per-page option
     */
    private function get_selected_per_page(): int {
        return $_SESSION['selected_per_page'] ?? 1;
    }
    
    /**
     * Determine appropriate back URL for navigation
     * 
     * Uses previous URL if it was the manage page, otherwise defaults to manage.
     * 
     * @return string URL for back button
     */
    private function get_back_url(): string {
        $previous_url = previous_url();
        if ($previous_url !== '' && strpos($previous_url, BASE_URL . 'friends/manage') === 0) {
            return $previous_url;
        }
        return BASE_URL . 'friends/manage';
    }
    
    /**
     * Display 404-style not found page for missing friends
     * 
     * Shows user-friendly error message with navigation back.
     * 
     * @return void
     */
    private function not_found(): void {
        $data = [
            'headline' => 'Friend Not Found',
            'message' => 'The friend you\'re looking for doesn\'t exist or has been deleted.',
            'back_url' => $this->get_back_url(),
            'back_label' => 'Go Back',
            'view_module' => 'friends',
            'view_file' => 'not_found'
        ];
        $this->templates->admin($data);
    }
}
