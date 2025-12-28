<?php
/**
 * Friends Model - Handles data operations for friend birthday records
 * 
 * Demonstrates proper date handling, data conversion patterns,
 * and separation of concerns between database operations and presentation logic.
 */
class Friends_model extends Model {
    
    /**
     * Fetch paginated friend records from database
     * 
     * Retrieves friends with proper limit and offset for pagination.
     * This is the primary method for listing friends in manage view.
     * 
     * @param int $limit Maximum number of records to return
     * @param int $offset Number of records to skip (for pagination)
     * @return array Array of friend record objects
     */
    public function fetch_records(int $limit, int $offset): array {
        return $this->db->get('id', 'friends', $limit, $offset);
    }
    
    /**
     * Get form-ready data based on current context
     * 
     * Determines whether to return existing record data (for editing)
     * or POST data/default values (for new forms or validation errors).
     * This is the main method called by controller's create() method.
     * 
     * @param int $update_id Record ID to edit, or 0 for new records
     * @return array Form data ready for view display
     * @example get_form_data(5) returns friend #5 data for editing
     * @example get_form_data(0) returns POST data or defaults for new friend
     */
    public function get_form_data(int $update_id = 0): array {
        if ($update_id > 0 && REQUEST_TYPE === 'GET') {
            return $this->get_data_for_edit($update_id);
        } else {
            return $this->get_data_from_post_or_defaults();
        }
    }

    /**
     * Get existing record data for editing
     * 
     * Fetches a single record from database and prepares it for form display.
     * Date is already in YYYY-MM-DD format from database, perfect for form_date().
     * 
     * @param int $update_id The record ID to fetch
     * @return array Record data ready for form
     * @throws No explicit throws, but returns empty array if record not found
     */
    public function get_data_for_edit(int $update_id): array {
        $record = $this->db->get_where($update_id, 'friends');
        
        if (empty($record)) {
            return [];
        }
        
        return (array) $record;
    }
    
    /**
     * Get form data from POST or use defaults
     * 
     * Used for new forms or when redisplaying form after validation errors.
     * Returns empty strings as defaults for a clean new form.
     * 
     * @return array Form data with proper types for view
     */
    private function get_data_from_post_or_defaults(): array {
        return [
            'first_name' => post('first_name', true) ?? '',
            'last_name' => post('last_name', true) ?? '',
            'email_address' => post('email_address', true) ?? '',
            'birthday' => post('birthday', true) ?? ''
        ];
    }
    
    /**
     * Prepare POST data for database storage
     * 
     * Converts form submission data to database-ready format.
     * Date comes from form_date() in YYYY-MM-DD format, which is perfect for MySQL DATE type.
     * 
     * @return array Database-ready data with proper types
     */
    public function get_post_data_for_database(): array {
        return [
            'first_name' => post('first_name', true),
            'last_name' => post('last_name', true),
            'email_address' => post('email_address', true),
            'birthday' => post('birthday', true) // Already in YYYY-MM-DD format
        ];
    }
    
    /**
     * Prepare raw database data for display in views
     * 
     * Adds formatted versions of fields while preserving raw data.
     * This is where you add display-friendly versions of data.
     * 
     * @param array $data Raw data from database
     * @return array Enhanced data with formatted fields
     * @example Converts birthday=2025-12-27 to birthday_formatted='December 27, 2025'
     */
    public function prepare_for_display(array $data): array {
        // Format birthday for display if present
        if (isset($data['birthday']) && $data['birthday'] !== null && $data['birthday'] !== '') {
            try {
                $date = new DateTime($data['birthday']);
                $data['birthday_formatted'] = $date->format('F j, Y'); // e.g., "December 27, 2025"
                $data['birthday_short'] = $date->format('M j'); // e.g., "Dec 27"
            } catch (Exception $e) {
                $data['birthday_formatted'] = 'Invalid Date';
                $data['birthday_short'] = 'N/A';
            }
        } else {
            $data['birthday_formatted'] = 'Not specified';
            $data['birthday_short'] = 'N/A';
        }
        
        // Format full name for convenience
        if (isset($data['first_name']) && isset($data['last_name'])) {
            $data['full_name'] = trim($data['first_name'] . ' ' . $data['last_name']);
        }
        
        return $data;
    }
    
    /**
     * Prepare multiple records for display in list views
     * 
     * Processes an array of records through prepare_for_display().
     * Maintains object structure for consistency with Trongate patterns.
     * 
     * @param array $rows Array of record objects from database
     * @return array Array of objects with formatted display fields
     */
    public function prepare_records_for_display(array $rows): array {
        $prepared = [];
        foreach ($rows as $row) {
            $row_array = (array) $row;
            $prepared[] = (object) $this->prepare_for_display($row_array);
        }
        return $prepared;
    }
}
