# Trongate v2 Friends Birthday Tracker Module

A complete **friends** module for **Trongate v2** that demonstrates a full-featured **CRUD** (Create, Read, Update, Delete) application for tracking friends' birthdays.

This repository provides a ready-to-use example of building a birthday management system using the Trongate PHP framework (version 2). It includes pagination, form validation, secure admin access, native HTML5 date input handling, and clean separation of concerns.

## Features

- ✅ Paginated friend listing with selectable records per page (10, 20, 50, 100)
- ✅ Create new friend records with birthday tracking
- ✅ View detailed friend information
- ✅ Update existing friend records (with form repopulation on validation errors)
- ✅ Safe delete with confirmation page
- ✅ **Native HTML5 date picker** for birthday selection (zero JavaScript required)
- ✅ **Proper date handling** using ISO 8601 format (YYYY-MM-DD)
- ✅ **MySQL DATE column type** for database storage
- ✅ Beautiful date formatting for display
- ✅ Form validation including email and date validation
- ✅ CSRF protection on all forms
- ✅ Admin security checks on all actions
- ✅ Responsive back navigation and flash messages
- ✅ Clean, well-commented code following Trongate v2 best practices

## Database Table

The `friends.sql` file creates a `friends` table with the following columns:
- `id` (INT, AUTO_INCREMENT, PRIMARY KEY)
- `first_name` (VARCHAR 50)
- `last_name` (VARCHAR 50)
- `email_address` (VARCHAR 100)
- `birthday` (DATE) - stores dates in YYYY-MM-DD format

## Prerequisites

- Trongate v2 framework (latest version recommended)
- PHP 8.0+
- MySQL/MariaDB database
- Web server with URL rewriting enabled

Visit the official site: [trongate.io](https://trongate.io)

## Installation

1. **Install Trongate v2** (if not already done):
   - Download or clone the official framework from GitHub: [https://github.com/trongate/trongate-framework](https://github.com/trongate/trongate-framework)
   - For full documentation and guides, visit: [trongate.io/documentation](https://trongate.io/documentation)

2. **Add the module**:
   - Copy the `friends` folder into your project's `modules` directory:
     ```
     modules/
       friends/
         controllers/
           Friends.php
         models/
           Friends_model.php
         views/
           create.php
           manage.php
           show.php
           delete_conf.php
           not_found.php
     ```

3. **Create the database table**:
   - Import `friends.sql` into your database (e.g., via phpMyAdmin or command line).

4. **Access the module**:
   - Log in to your Trongate admin panel.
   - Visit: `https://your-domain.com/friends` or `https://your-domain.com/friends/manage`

## URL Routes

- List friends: `/friends` or `/friends/manage` (with pagination: `/friends/manage/{page}`)
- Create friend: `/friends/create`
- View friend: `/friends/show/{id}`
- Edit friend: `/friends/create/{id}`
- Delete confirmation: `/friends/delete_conf/{id}`
- Set records per page: `/friends/set_per_page/{option_index}`

## Module Structure

```
friends/
├── controllers/
│   └── Friends.php          # Main controller with CRUD operations
├── models/
│   └── Friends_model.php    # Data layer with formatting methods
└── views/
    ├── create.php           # Create/Edit form
    ├── manage.php           # Paginated list view
    ├── show.php             # Detail view
    ├── delete_conf.php      # Delete confirmation
    └── not_found.php        # 404 error page
```

## Key Features Explained

### Native HTML5 Date Input

This module uses the **native HTML5 date picker** via Trongate's `form_date()` helper:

```php
echo form_date('birthday', $birthday);
```

**Benefits:**
- ✅ Zero JavaScript required
- ✅ Works on all modern browsers (Chrome, Firefox, Safari, Edge)
- ✅ Native mobile keyboards and pickers
- ✅ Accessible by default
- ✅ Always submits in ISO 8601 format (YYYY-MM-DD)
- ✅ Browser displays in user's locale format automatically

### Date Storage

Dates are stored in MySQL's `DATE` type, which uses `YYYY-MM-DD` format - the same format as HTML5 date inputs. This means **no conversion is needed** between the form and database.

### Date Display Formatting

The model includes a `prepare_for_display()` method that formats dates for human-readable display:

```php
// Database: 2025-12-27
// Display: December 27, 2025
```

### Validation Rules

The module demonstrates proper validation including:
- Required fields
- String length limits
- Email format validation
- **Date format validation** using `valid_date` rule

```php
$this->validation->set_rules('birthday', 'birthday', 'required|valid_date');
```

## Development Patterns Demonstrated

### 1. The Three-Method Form Pattern
- `create()` - Display form
- `submit()` - Process submission
- `show()` - Display success/result

### 2. Create/Update Pattern
- Single form for both creating and editing
- Logic determines whether to load from database or POST data
- Proper segment type-casting: `segment(3, 'int')`

### 3. POST-Redirect-GET Pattern
- Prevents duplicate submissions on refresh
- Uses `set_flashdata()` for success messages
- Clean URL after form submission

### 4. Data Conversion
- Model handles conversion between database and display formats
- Separation of raw data and formatted data
- Display-ready fields added without modifying originals

### 5. Pagination Implementation
- Session-based per-page selection
- Proper offset calculation
- Clean pagination helper integration

## Customization

### Changing Date Display Format

Edit the `prepare_for_display()` method in `Friends_model.php`:

```php
// Current format: "December 27, 2025"
$data['birthday_formatted'] = $date->format('F j, Y');

// European format: "27/12/2025"
$data['birthday_formatted'] = $date->format('d/m/Y');

// Short format: "Dec 27, 2025"
$data['birthday_formatted'] = $date->format('M j, Y');
```

### Adding Age Calculation

Add this to the `prepare_for_display()` method:

```php
if (isset($data['birthday']) && $data['birthday'] !== null) {
    $birthday = new DateTime($data['birthday']);
    $today = new DateTime();
    $age = $today->diff($birthday)->y;
    $data['age'] = $age;
}
```

### Making Birthday Optional

Change the validation rule in `Friends.php`:

```php
// From:
$this->validation->set_rules('birthday', 'birthday', 'required|valid_date');

// To:
$this->validation->set_rules('birthday', 'birthday', 'valid_date');
```

## Browser Compatibility

The native HTML5 date input is supported by:
- ✅ Chrome (all versions)
- ✅ Firefox (all versions)
- ✅ Safari 14.1+
- ✅ Edge (all versions)
- ✅ Mobile browsers (iOS Safari, Android Chrome)

**Note:** Very old browsers (IE 11 and earlier) will render date inputs as text fields. Users can still type dates manually in YYYY-MM-DD format, and validation will ensure correctness.

## Security Features

- ✅ CSRF token validation on all forms
- ✅ Admin authentication checks on all methods
- ✅ SQL injection prevention via prepared statements
- ✅ XSS prevention via `out()` function in views
- ✅ Email format validation
- ✅ Date format validation
- ✅ Delete confirmation to prevent accidental deletion

## Contributing

Issues, suggestions, and pull requests are welcome! Feel free to fork and improve this example module.

## License

Released under the same open-source license as the Trongate framework (MIT-style - permissive and free to use).

## Learn More

- [Trongate Framework](https://trongate.io)
- [Trongate Documentation](https://trongate.io/documentation)

Happy coding with Trongate! 🎂🎉
