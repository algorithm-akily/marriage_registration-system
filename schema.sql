CREATE DATABASE IF NOT EXISTS marriage_registration;
USE marriage_registration;

-- Users table with password hashing
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    date_of_birth DATE,
    id_type VARCHAR(50),
    id_number VARCHAR(50),
    role ENUM('user', 'admin', 'staff') DEFAULT 'user',
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Marriage applications table
CREATE TABLE marriage_applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    application_number VARCHAR(50) UNIQUE,
    user_id INT,
    
    -- Groom details
    groom_name VARCHAR(100) NOT NULL,
    groom_dob DATE,
    groom_id_type VARCHAR(50),
    groom_id_number VARCHAR(50),
    groom_address TEXT,
    groom_phone VARCHAR(20),
    groom_email VARCHAR(100),
    
    -- Bride details
    bride_name VARCHAR(100) NOT NULL,
    bride_dob DATE,
    bride_id_type VARCHAR(50),
    bride_id_number VARCHAR(50),
    bride_address TEXT,
    bride_phone VARCHAR(20),
    bride_email VARCHAR(100),
    
    -- Marriage details
    marriage_date DATE NOT NULL,
    marriage_location VARCHAR(200),
    marriage_type ENUM('civil', 'religious', 'traditional') DEFAULT 'civil',
    has_children BOOLEAN DEFAULT FALSE,
    number_of_children INT DEFAULT 0,
    
    -- Application status
    current_status ENUM('draft', 'submitted', 'pending', 'under_review', 'approved', 'rejected') DEFAULT 'draft',
    status_notes TEXT,
    
    -- Dates
    registration_date DATE,
    review_date DATE,
    approval_date DATE,
    
    -- Documents
    id_proof_path VARCHAR(255),
    marriage_proof_path VARCHAR(255),
    affidavit_path VARCHAR(255),
    photos_path VARCHAR(255),
    
    -- Metadata
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Application status history table
CREATE TABLE application_status_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    application_id INT,
    old_status VARCHAR(50),
    new_status VARCHAR(50),
    changed_by INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES marriage_applications(id) ON DELETE CASCADE,
    FOREIGN KEY (changed_by) REFERENCES users(id)
);

-- Documents table
CREATE TABLE documents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    application_id INT,
    document_type ENUM('id_proof', 'marriage_proof', 'affidavit', 'photo', 'other'),
    file_name VARCHAR(255),
    file_path VARCHAR(255),
    file_size INT,
    mime_type VARCHAR(100),
    uploaded_by INT,
    verified BOOLEAN DEFAULT FALSE,
    verification_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES marriage_applications(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id)
);

-- System settings table
CREATE TABLE system_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE,
    setting_value TEXT,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, email, password_hash, full_name, role) 
VALUES (
    'akily', 
    'akilykaaya@gmail.com', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
    'System Administrator', 
    'admin'
);

-- Insert default settings
INSERT INTO system_settings (setting_key, setting_value, description) VALUES
('site_name', 'Marriage Registration System', 'Website name'),
('site_email', 'info@marriageregistration.com', 'System email address'),
('max_file_size', '5242880', 'Maximum file upload size in bytes (5MB)'),
('allowed_file_types', 'pdf,jpg,jpeg,png,doc,docx', 'Allowed file extensions'),
('application_fee', '0', 'Application fee amount');

-- Insert sample users with different roles
INSERT INTO users (username, email, password_hash, full_name, role, phone, address) VALUES
('jumanne', 'jumanne@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Staff Member', 'staff', '555-1001', '789 Staff St'),
('Agape', 'agape@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Regular User', 'user', '555-2001', '456 User Ave');

-- Create a function to generate application number
DELIMITER $$
CREATE FUNCTION generate_application_number() RETURNS VARCHAR(50)
BEGIN
    DECLARE app_num VARCHAR(50);
    SET app_num = CONCAT('APP-', DATE_FORMAT(NOW(), '%Y%m%d-'), LPAD(FLOOR(RAND() * 10000), 4, '0'));
    RETURN app_num;
END$$
DELIMITER ;

-- Trigger to generate application number before insert
DELIMITER $$
CREATE TRIGGER before_application_insert
BEFORE INSERT ON marriage_applications
FOR EACH ROW
BEGIN
    IF NEW.application_number IS NULL THEN
        SET NEW.application_number = generate_application_number();
    END IF;
END$$
DELIMITER ;

-- Insert sample marriage application
INSERT INTO marriage_applications (user_id, groom_name, bride_name, marriage_date,
 marriage_location, groom_phone, bride_phone, groom_address, bride_address, current_status) 
 VALUES (2, 'Adam Adam', 'Hawa Eve', '2023-06-15', 'New York City Hall',
 '555-1234', '555-5678', '123 Main St, New York, NY', '123 Main St, New York, NY', 'approved');

-- Create indexes for better performance
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_applications_user_id ON marriage_applications(user_id);
CREATE INDEX idx_applications_status ON marriage_applications(current_status);
CREATE INDEX idx_applications_app_num ON marriage_applications(application_number);
CREATE INDEX idx_documents_app_id ON documents(application_id);
CREATE INDEX idx_status_history_app_id ON application_status_history(application_id);