-- Users
INSERT INTO Users (user_id, username, password_hash, email) VALUES 
(1, 'admin', '$2y$10$bZfVkRNmfNXVr2hXG/jys.owu2YJfUM/6CdzEgEcyOopFS9zqCRrO', 'ak301824@student.polsl.pl'),
(2, 'owner', '$2y$10$bZfVkRNmfNXVr2hXG/jys.owu2YJfUM/6CdzEgEcyOopFS9zqCRrO', 'owner@example.com'),
(3, 'tenant', '$2y$10$bZfVkRNmfNXVr2hXG/jys.owu2YJfUM/6CdzEgEcyOopFS9zqCRrO', 'tenant@example.com'),
(4, 'admin2', '$2y$10$bZfVkRNmfNXVr2hXG/jys.owu2YJfUM/6CdzEgEcyOopFS9zqCRrO', 'admin2@example.com'),
(5, 'owner2', '$2y$10$bZfVkRNmfNXVr2hXG/jys.owu2YJfUM/6CdzEgEcyOopFS9zqCRrO', 'owner2@example.com'),
(6, 'tenant2', '$2y$10$bZfVkRNmfNXVr2hXG/jys.owu2YJfUM/6CdzEgEcyOopFS9zqCRrO', 'tenant2@example.com'),
(7, 'tenant3', '$2y$10$bZfVkRNmfNXVr2hXG/jys.owu2YJfUM/6CdzEgEcyOopFS9zqCRrO', 'tenant3@example.com'),
(8, 'tenant4', '$2y$10$bZfVkRNmfNXVr2hXG/jys.owu2YJfUM/6CdzEgEcyOopFS9zqCRrO', 'tenant4@example.com'),
(9, 'admin3', '$2y$10$bZfVkRNmfNXVr2hXG/jys.owu2YJfUM/6CdzEgEcyOopFS9zqCRrO', 'admin3@example.com'),
(10, 'owner3', '$2y$10$bZfVkRNmfNXVr2hXG/jys.owu2YJfUM/6CdzEgEcyOopFS9zqCRrO', 'owner3@example.com'),
(11, 'tenant5', '$2y$10$bZfVkRNmfNXVr2hXG/jys.owu2YJfUM/6CdzEgEcyOopFS9zqCRrO', 'tenant5@example.com'),
(12, 'tenant6', '$2y$10$bZfVkRNmfNXVr2hXG/jys.owu2YJfUM/6CdzEgEcyOopFS9zqCRrO', 'tenant6@example.com'),
(13, 'tenant7', '$2y$10$bZfVkRNmfNXVr2hXG/jys.owu2YJfUM/6CdzEgEcyOopFS9zqCRrO', 'tenant7@example.com'),
(14, 'tenant8', '$2y$10$bZfVkRNmfNXVr2hXG/jys.owu2YJfUM/6CdzEgEcyOopFS9zqCRrO', 'tenant8@example.com'),
(15, 'tenant9', '$2y$10$bZfVkRNmfNXVr2hXG/jys.owu2YJfUM/6CdzEgEcyOopFS9zqCRrO', 'tenant9@example.com'),
(16, 'tenant10', '$2y$10$bZfVkRNmfNXVr2hXG/jys.owu2YJfUM/6CdzEgEcyOopFS9zqCRrO', 'tenant10@example.com'),
(17, 'owner4', '$2y$10$bZfVkRNmfNXVr2hXG/jys.owu2YJfUM/6CdzEgEcyOopFS9zqCRrO', 'owner4@example.com'),
(18, 'owner5', '$2y$10$bZfVkRNmfNXVr2hXG/jys.owu2YJfUM/6CdzEgEcyOopFS9zqCRrO', 'owner5@example.com');

-- UserDetails
INSERT INTO UserDetails (detail_id, user_id, first_name, last_name, phone_number, address) VALUES 
(1, 1, 'Admin', 'User', '555-0001', '123 Main St, Anytown, Anystate, 12345'),
(2, 2, 'Owner', 'One', '555-0002', '456 Oak St, Othertown, Otherstate, 67890'),
(3, 3, 'Tenant', 'One', '555-0003', '789 Pine St, Anothertown, Anotherstate, 54321'),
(4, 4, 'Admin', 'Two', '555-0004', '101 Maple St, Sometown, Somestate, 67812'),
(5, 5, 'Owner', 'Two', '555-0005', '202 Birch St, Thistown, Thisstate, 12389'),
(6, 6, 'Tenant', 'Two', '555-0006', '303 Walnut St, That town, Thatstate, 34567'),
(7, 7, 'Tenant', 'Three', '555-0007', '404 Cherry St, Townsville, Stateland, 56789'),
(8, 8, 'Tenant', 'Four', '555-0008', '505 Elm St, Villagetown, Regionstate, 12389'),
(9, 9, 'Admin', 'Three', '555-0009', '606 Fir St, Districtburg, Zoneplace, 67812'),
(10, 10, 'Owner', 'Three', '555-0010', '707 Aspen St, Sectorcity, Countyarea, 11245'),
(11, 11, 'Tenant', 'Five', '555-0011', '808 Hickory St, Areaurb, Borough, 44567'),
(12, 12, 'Tenant', 'Six', '555-0012', '909 Cedar St, Parishville, Municipality, 22345'),
(13, 13, 'Tenant', 'Five', '555-0013', '123 Oak St, Anytown, Anystate, 12345'),
(14, 14, 'Tenant', 'Six', '555-0014', '456 Pine St, Othertown, Otherstate, 67890'),
(15, 15, 'Owner', 'Four', '555-0015', '789 Maple St, Sometown, Somestate, 11223'),
(16, 16, 'Admin', 'Three', '555-0016', '101 Birch St, Thistown, Thisstate, 44556'),
(17, 17, 'Tenant', 'Seven', '555-0017', '202 Walnut St, That town, Thatstate, 77889'),
(18, 18, 'Owner', 'Five', '555-0018', '303 Cedar St, Townsville, Stateland, 99001');

-- PasswordResets
INSERT INTO PasswordResets (reset_id, user_id, reset_token, created_at) VALUES 
(1, 1, 'resetToken1', NOW()),
(2, 2, 'resetToken2', NOW()),
(3, 3, 'resetToken3', NOW()),
(4, 4, 'resetToken4', NOW()),
(5, 5, 'resetToken5', NOW()),
(6, 6, 'resetToken6', NOW()),
(7, 7, 'resetToken7', NOW()),
(8, 8, 'resetToken8', NOW()),
(9, 9, 'resetToken9', NOW()),
(10, 10, 'resetToken10', NOW());

-- Administrators
INSERT INTO Administrators (administrator_id, user_id, additional_admin_info) VALUES 
(1, 1, 'Primary Administrator'),
(2, 4, 'Secondary Administrator'),
(3, 9, 'Tertiary Administrator');

-- Owners
INSERT INTO Owners (owner_id, user_id, additional_owner_info) VALUES 
(1, 2, 'Owner with multiple properties'),
(2, 5, 'Owner with single property'),
(3, 10, 'Owner with multiple apartments'),
(4, 17, 'Owner with diverse portfolio'),
(5, 18, 'Owner with international properties');

-- Tenants
INSERT INTO Tenants (tenant_id, user_id, contact_details) VALUES 
(1, 3, 'tenant contact details'),
(2, 6, 'tenant2 contact details'),
(3, 7, 'tenant3 contact details'),
(4, 8, 'tenant4 contact details'),
(5, 11, 'tenant5 contact details'),
(6, 12, 'tenant6 contact details'),
(7, 13, 'tenant7 contact details'),
(8, 14, 'tenant8 contact details'),
(9, 15, 'tenant9 contact details'),
(10, 16, 'tenant10 contact details');

-- Addresses
INSERT INTO Addresses (address_id, street, city, state, postal_code, country) VALUES 
(1, '123 Main St', 'Anytown', 'Anystate', '12345', 'USA'),
(2, '456 Oak St', 'Othertown', 'Otherstate', '67890', 'USA'),
(3, '789 Pine St', 'Anothertown', 'Anotherstate', '54321', 'USA'),
(4, '101 Maple St', 'Sometown', 'Somestate', '67812', 'USA'),
(5, '202 Birch St', 'Thistown', 'Thisstate', '12389', 'USA'),
(6, '303 Walnut St', 'That town', 'Thatstate', '34567', 'USA'),
(7, '404 Cherry St', 'Townsville', 'Stateland', '56789', 'USA'),
(8, '505 Elm St', 'Villagetown', 'Regionstate', '12389', 'USA'),
(9, '606 Fir St', 'Districtburg', 'Zoneplace', '67812', 'USA'),
(10, '707 Aspen St', 'Sectorcity', 'Countyarea', '11245', 'USA'),
(11, '808 Hickory St', 'Areaurb', 'Borough', '44567', 'USA'),
(12, '909 Cedar St', 'Parishville', 'Municipality', '22345', 'USA');

-- PropertyTypes
INSERT INTO PropertyTypes (type_id, type_name) VALUES 
(1, 'Apartment'),
(2, 'Townhouse'),
(3, 'Condo'),
(4, 'Loft'),
(5, 'Villa');

-- PaymentTypes
INSERT INTO PaymentTypes (payment_type_id, payment_type_name) VALUES 
(1, 'Credit Card'),
(2, 'Bank Transfer'),
(3, 'Cash');

-- MaintenanceStatuses
INSERT INTO MaintenanceStatuses (status_id, status_name) VALUES 
(1, 'Pending'),
(2, 'In Progress'),
(3, 'Completed');

-- DocumentTypes
INSERT INTO DocumentTypes (document_type_id, document_type_name) VALUES 
(1, 'Lease Agreement'),
(2, 'Rental Receipt'),
(3, 'Maintenance Report');

-- Notifications
INSERT INTO Notifications (notification_id, user_id, message, created_at, read_at) VALUES 
(1, 1, 'Your password was reset', NOW(), NULL),
(2, 2, 'New maintenance request', NOW(), NULL),
(3, 3, 'Payment received', NOW(), NULL),
(4, 4, 'Lease agreement updated', NOW(), NULL),
(5, 5, 'New document available', NOW(), NULL),
(6, 6, 'Maintenance task completed', NOW(), NULL),
(7, 7, 'Rental agreement renewed', NOW(), NULL),
(8, 8, 'Your payment is overdue', NOW(), NULL),
(9, 9, 'Property inspection scheduled', NOW(), NULL),
(10, 10, 'New notification received', NOW(), NULL);

-- Properties
INSERT INTO Properties (property_id, owner_id, address_id, type_id, number_of_rooms, size, rental_price, description) VALUES 
(1, 2, 1, 1, 3, 150.0, 2000.00, 'Three-bedroom apartment'),
(2, 2, 2, 2, 2, 100.0, 1500.00, 'Two-bedroom apartment'),
(3, 5, 3, 1, 4, 200.0, 2500.00, 'Four-bedroom house'),
(4, 5, 4, 2, 3, 120.0, 1800.00, 'Three-bedroom townhouse'),
(5, 10, 5, 3, 2, 90.0, 1300.00, 'Two-bedroom condo'),
(6, 10, 6, 4, 1, 70.0, 900.00, 'One-bedroom loft'),
(7, 17, 7, 3, 2, 110.0, 1400.00, 'Two-bedroom duplex'),
(8, 17, 8, 5, 3, 130.0, 1700.00, 'Three-bedroom villa'),
(9, 17, 9, 2, 4, 180.0, 2100.00, 'Four-bedroom bungalow'),
(10, 18, 10, 1, 2, 85.0, 1200.00, 'Two-bedroom flat'),
(11, 18, 11, 2, 3, 125.0, 1600.00, 'Three-bedroom apartment'),
(12, 18, 12, 4, 1, 60.0, 800.00, 'One-bedroom studio');

-- RentalAgreements
INSERT INTO RentalAgreements (agreement_id, property_id, tenant_id, start_date, end_date, rent_amount, security_deposit) VALUES 
(1, 1, 1, '2023-01-01', '2023-12-31', 2000.00, 2000.00),
(2, 2, 2, '2023-02-01', '2024-01-31', 1500.00, 1500.00),
(3, 3, 3, '2023-03-01', '2024-02-29', 2500.00, 2500.00),
(4, 4, 4, '2023-04-01', '2024-03-31', 1800.00, 1800.00),
(5, 5, 5, '2023-05-01', '2024-04-30', 1300.00, 1300.00),
(6, 6, 6, '2023-06-01', '2024-05-31', 900.00, 900.00),
(7, 7, 7, '2023-07-01', '2024-06-30', 1400.00, 1400.00),
(8, 8, 8, '2023-08-01', '2024-07-31', 1700.00, 1700.00),
(9, 9, 9, '2023-09-01', '2024-08-31', 2100.00, 2100.00),
(10, 10, 10, '2023-10-01', '2024-09-30', 1200.00, 1200.00);

-- Payments
INSERT INTO Payments (payment_id, agreement_id, payment_date, amount, payment_type_id) VALUES 
(1, 1, '2023-01-01', 2000.00, 1),
(2, 1, '2023-02-01', 2000.00, 1),
(3, 1, '2023-03-01', 2000.00, 1),
(4, 2, '2023-02-01', 1500.00, 2),
(5, 2, '2023-03-01', 1500.00, 2),
(6, 2, '2023-04-01', 1500.00, 2),
(7, 3, '2023-03-01', 2500.00, 1),
(8, 3, '2023-04-01', 2500.00, 1),
(9, 3, '2023-05-01', 2500.00, 1),
(10, 4, '2023-04-01', 1800.00, 3),
(11, 4, '2023-05-01', 1800.00, 3),
(12, 4, '2023-06-01', 1800.00, 3),
(13, 5, '2023-05-01', 1300.00, 1),
(14, 5, '2023-06-01', 1300.00, 1),
(15, 5, '2023-07-01', 1300.00, 1),
(16, 6, '2023-06-01', 900.00, 2),
(17, 6, '2023-07-01', 900.00, 2),
(18, 6, '2023-08-01', 900.00, 2),
(19, 1, '2024-06-30', 1234.00, 3);

-- MaintenanceTasks
INSERT INTO MaintenanceTasks (task_id, property_id, description, cost, status_id, reported_by, created_at, resolved_at) VALUES 
(1, 1, 'Fix leaking sink', 150.00, 1, 3, NOW(), NULL),
(2, 2, 'Repair broken window', 200.00, 2, 6, NOW(), NOW()),
(3, 3, 'Paint walls', 300.00, 3, 7, NOW(), NOW()),
(4, 4, 'Replace carpet', 250.00, 1, 8, NOW(), NULL),
(5, 5, 'Fix heating system', 500.00, 2, 11, NOW(), NOW()),
(6, 6, 'Repair roof', 1000.00, 3, 12, NOW(), NOW()),
(7, 7, 'Clean gutters', 75.00, 1, 13, NOW(), NULL),
(8, 8, 'Replace light fixtures', 150.00, 2, 14, NOW(), NOW()),
(9, 9, 'Repair door lock', 50.00, 3, 15, NOW(), NOW()),
(10, 10, 'Fix air conditioning', 300.00, 1, 16, NOW(), NULL);

-- Documents
INSERT INTO Documents (document_id, property_id, agreement_id, document_type_id, file_path, uploaded_at) VALUES 
(1, 1, 1, 1, 'lease1.pdf', NOW()),
(2, 2, 2, 2, 'lease2.pdf', NOW()),
(3, 3, 3, 3, 'lease3.pdf', NOW()),
(4, 4, 4, 1, 'lease4.pdf', NOW()),
(5, 5, 5, 2, 'lease5.pdf', NOW()),
(6, 6, 6, 3, 'lease6.pdf', NOW()),
(7, 7, 7, 1, 'lease7.pdf', NOW()),
(8, 8, 8, 2, 'lease8.pdf', NOW()),
(9, 9, 9, 3, 'lease9.pdf', NOW()),
(10, 10, 10, 1, 'lease10.pdf', NOW());