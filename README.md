# ApartmentManagement

#### Mail testing end setup

- To test emails go to directory of includes/MailHog and run .exe
- MailHog will start and listen on port 1025 for SMTP connections and on port 8025 for the web interface.
- To see emails go to http://localhost:8025
- Edit line 19 in mail.php with temporary email for testing If communication does not work


#### To do:

### Other tasks
- [ ] Restrict access for specific roles to each file

### Role Management
- [ ] **Administrators**: Manage properties, tenants, and overall system.
- [ ] **Owners**: Manage their properties and tenant interactions.
- [ ] **Tenants**: Manage rental agreements and contact details.

### Property Management
- [ ] **Addresses**: Store property address details.
- [ ] **Properties**: Track property information, including owner, type, and rental details.
- [ ] **PropertyTypes**: Define types of properties.

### Rental Management
- [ ] **RentalAgreements**: Manage rental agreements, including start/end dates and payment terms.
- [ ] **Payments**: Record payments related to rental agreements.
- [ ] **PaymentTypes**: Define types of payments.

### Maintenance Management
- [ ] **MaintenanceTasks**: Track maintenance tasks, costs, and statuses.
- [ ] **MaintenanceStatuses**: Define statuses for maintenance tasks.

### Document Management
- [ ] **Documents**: Store documents related to properties and rental agreements.
- [ ] **DocumentTypes**: Define types of documents.

### Notifications
- [ ] **Notifications**: Send notifications to users regarding various events.
