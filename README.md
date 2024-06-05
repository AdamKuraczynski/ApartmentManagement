# ApartmentManagement

#### Mail testing end setup

- To test emails go to directory of includes/MailHog and run .exe
- MailHog will start and listen on port 1025 for SMTP connections and on port 8025 for the web interface.
- To see emails go to http://localhost:8025
- Edit line 19 in mail.php with temporary email for testing If communication does not work


#### To do:

### Other tasks
- [x] Restrict access for specific roles to each file

### Role Management
- [x] **Administrators**: Manage properties, tenants, and overall system.
- [x] **Owners**: Manage their properties and tenant interactions.
- [x] **Tenants**: Manage rental agreements and contact details.

### Property Management
- [x] **Addresses**: Store property address details.
- [x] **Properties**: Track property information, including owner, type, and rental details.
- [x] **PropertyTypes**: Define types of properties.

### Rental Management
- [x] **RentalAgreements**: Manage rental agreements, including start/end dates and payment terms.
- [x] **Payments**: Record payments related to rental agreements.
- [x] **PaymentTypes**: Define types of payments.

### Maintenance Management
- [x] **MaintenanceTasks**: Track maintenance tasks, costs, and statuses.
- [x] **MaintenanceStatuses**: Define statuses for maintenance tasks.

### Document Management
- [x] **Documents**: Store documents related to properties and rental agreements.
- [x] **DocumentTypes**: Define types of documents.

### Notifications
- [x] **Notifications**: Send notifications to users regarding various events.