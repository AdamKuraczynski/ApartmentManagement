CREATE DATABASE IF NOT EXISTS apartmentmanagement;

USE apartmentmanagement;

CREATE TABLE `Users` (
  `user_id` int PRIMARY KEY AUTO_INCREMENT,
  `username` varchar(255) UNIQUE NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(255) UNIQUE NOT NULL
);

CREATE TABLE `PasswordResets` (
  `reset_id` int PRIMARY KEY AUTO_INCREMENT,
  `user_id` int,
  `reset_token` varchar(255) UNIQUE NOT NULL,
  `created_at` datetime NOT NULL
);

CREATE TABLE `Administrators` (
  `administrator_id` int PRIMARY KEY AUTO_INCREMENT,
  `user_id` int UNIQUE,
  `additional_admin_info` text
);

CREATE TABLE `Owners` (
  `owner_id` int PRIMARY KEY AUTO_INCREMENT,
  `user_id` int UNIQUE,
  `additional_owner_info` text
);

CREATE TABLE `Tenants` (
  `tenant_id` int PRIMARY KEY AUTO_INCREMENT,
  `user_id` int UNIQUE,
  `contact_details` text
);

CREATE TABLE `Addresses` (
  `address_id` int PRIMARY KEY AUTO_INCREMENT,
  `street` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `postal_code` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL
);

CREATE TABLE `Properties` (
  `property_id` int PRIMARY KEY AUTO_INCREMENT,
  `owner_id` int,
  `address_id` int,
  `type_id` int,
  `number_of_rooms` int NOT NULL,
  `size` float NOT NULL,
  `rental_price` decimal(10,2) NOT NULL,
  `description` text
);

CREATE TABLE `PropertyTypes` (
  `type_id` int PRIMARY KEY AUTO_INCREMENT,
  `type_name` varchar(50) NOT NULL
);

CREATE TABLE `RentalAgreements` (
  `agreement_id` int PRIMARY KEY AUTO_INCREMENT,
  `property_id` int,
  `tenant_id` int,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `rent_amount` decimal(10,2) NOT NULL,
  `security_deposit` decimal(10,2) NOT NULL
);

CREATE TABLE `Payments` (
  `payment_id` int PRIMARY KEY AUTO_INCREMENT,
  `agreement_id` int,
  `payment_date` date NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_type_id` int
);

CREATE TABLE `PaymentTypes` (
  `payment_type_id` int PRIMARY KEY AUTO_INCREMENT,
  `payment_type_name` varchar(50) NOT NULL
);

CREATE TABLE `MaintenanceTasks` (
  `task_id` int PRIMARY KEY AUTO_INCREMENT,
  `property_id` int,
  `description` text NOT NULL,
  `cost` decimal(10,2),
  `status_id` int,
  `reported_by` int,
  `created_at` datetime NOT NULL,
  `resolved_at` datetime
);

CREATE TABLE `MaintenanceStatuses` (
  `status_id` int PRIMARY KEY AUTO_INCREMENT,
  `status_name` varchar(50) NOT NULL
);

CREATE TABLE `Documents` (
  `document_id` int PRIMARY KEY AUTO_INCREMENT,
  `property_id` int,
  `agreement_id` int,
  `document_type_id` int,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` datetime NOT NULL
);

CREATE TABLE `DocumentTypes` (
  `document_type_id` int PRIMARY KEY AUTO_INCREMENT,
  `document_type_name` varchar(50) NOT NULL
);

CREATE TABLE `Notifications` (
  `notification_id` int PRIMARY KEY AUTO_INCREMENT,
  `user_id` int,
  `message` text NOT NULL,
  `created_at` datetime NOT NULL,
  `read_at` datetime
);

CREATE TABLE `UserDetails` (
  `detail_id` int PRIMARY KEY AUTO_INCREMENT,
  `user_id` int,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone_number` varchar(12),
  `address` text
);

ALTER TABLE `PasswordResets` ADD FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`);

ALTER TABLE `Administrators` ADD FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`);

ALTER TABLE `Owners` ADD FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`);

ALTER TABLE `Tenants` ADD FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`);

ALTER TABLE `Properties` ADD FOREIGN KEY (`owner_id`) REFERENCES `Users` (`user_id`);

ALTER TABLE `Properties` ADD FOREIGN KEY (`address_id`) REFERENCES `Addresses` (`address_id`);

ALTER TABLE `Properties` ADD FOREIGN KEY (`type_id`) REFERENCES `PropertyTypes` (`type_id`);

ALTER TABLE `RentalAgreements` ADD FOREIGN KEY (`property_id`) REFERENCES `Properties` (`property_id`);

ALTER TABLE `RentalAgreements` ADD FOREIGN KEY (`tenant_id`) REFERENCES `Tenants` (`tenant_id`);

ALTER TABLE `Payments` ADD FOREIGN KEY (`agreement_id`) REFERENCES `RentalAgreements` (`agreement_id`);

ALTER TABLE `Payments` ADD FOREIGN KEY (`payment_type_id`) REFERENCES `PaymentTypes` (`payment_type_id`);

ALTER TABLE `MaintenanceTasks` ADD FOREIGN KEY (`property_id`) REFERENCES `Properties` (`property_id`);

ALTER TABLE `MaintenanceTasks` ADD FOREIGN KEY (`status_id`) REFERENCES `MaintenanceStatuses` (`status_id`);

ALTER TABLE `MaintenanceTasks` ADD FOREIGN KEY (`reported_by`) REFERENCES `Users` (`user_id`);

ALTER TABLE `Documents` ADD FOREIGN KEY (`property_id`) REFERENCES `Properties` (`property_id`);

ALTER TABLE `Documents` ADD FOREIGN KEY (`agreement_id`) REFERENCES `RentalAgreements` (`agreement_id`);

ALTER TABLE `Documents` ADD FOREIGN KEY (`document_type_id`) REFERENCES `DocumentTypes` (`document_type_id`);

ALTER TABLE `Notifications` ADD FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`);

ALTER TABLE 'UserDetails' ADD FOREIGN KEY ('user_id') REFERENCES 'Users' ('user_id');