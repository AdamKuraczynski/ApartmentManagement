USE apartmentmanagement;

INSERT INTO Users (username, password_hash, email) VALUES
('admin', 'password_hash1', 'admin@example.com'),
('owner1', 'password_hash2', 'owner1@example.com'),
('tenant1', 'password_hash3', 'tenant1@example.com');

INSERT INTO Administrators (user_id, additional_admin_info) VALUES
(1, 'Main admin info');

INSERT INTO Owners (user_id, additional_owner_info) VALUES
(2, 'Owner 1 additional info');

INSERT INTO Tenants (user_id, contact_details) VALUES
(3, 'Tenant 1 contact details');

INSERT INTO Addresses (street, city, state, postal_code, country) VALUES
('123 Main St', 'Anytown', 'Anystate', '12345', 'Country1'),
('456 Elm St', 'Othertown', 'Otherstate', '67890', 'Country2');

INSERT INTO PropertyTypes (type_name) VALUES
('Apartment'),
('House');

INSERT INTO Properties (owner_id, address_id, type_id, number_of_rooms, size, rental_price, description) VALUES
(2, 1, 1, 3, 120.5, 1500.00, 'Spacious apartment in the city center'),
(2, 2, 2, 5, 250.0, 2500.00, 'Large house with garden');

INSERT INTO PaymentTypes (payment_type_name) VALUES
('Credit Card'),
('Bank Transfer');

INSERT INTO MaintenanceStatuses (status_name) VALUES
('Pending'),
('In Progress'),
('Completed');

INSERT INTO MaintenanceTasks (property_id, description, cost, status_id, reported_by, created_at) VALUES
(1, 'Fix the leaky faucet', 100.00, 1, 3, '2023-01-10 10:00:00');

INSERT INTO DocumentTypes (document_type_name) VALUES
('Lease Agreement'),
('Payment Receipt');

INSERT INTO Notifications (user_id, message, created_at) VALUES
(3, 'Your rent payment is due', '2023-01-01 12:00:00');

INSERT INTO RentalAgreements (property_id, tenant_id, start_date, end_date, rent_amount, security_deposit) VALUES
(1, 3, '2023-01-01', '2023-12-31', 1500.00, 1500.00);

INSERT INTO Payments (agreement_id, payment_date, amount, payment_type_id) VALUES
(1, '2023-01-01', 1500.00, 1);

INSERT INTO Documents (property_id, agreement_id, document_type_id, file_path, uploaded_at) VALUES
(1, 1, 1, '/documents/lease_agreement_1.pdf', '2023-01-01 12:00:00');