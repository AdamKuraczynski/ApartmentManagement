# ApartmentManagement
 
## Overview
ApartmentManagement is a web-based application designed to simplify the management of rental properties. The system supports administrators, property owners, and tenants with various features such as property listings, rental agreements, maintenance requests, payment management, and document storage.

## Features
- **Property Management**: Manage property details, addresses, and types.
- **Rental Agreements**: Track and manage rental agreements with tenants.
- **Maintenance Requests**: Submit and manage maintenance tasks.
- **Payment Tracking**: Record and track rental payments.
- **Document Management**: Store and access important documents related to properties and rentals.
- **User Panels**: Different dashboards for administrators, owners, and tenants.

## Mail testing end setup

- To test emails go to directory of includes/MailHog and run .exe
- MailHog will start and listen on port 1025 for SMTP connections and on port 8025 for the web interface.
- To see emails go to http://localhost:8025
- Edit line 19 in mail.php with temporary email for testing If communication does not work
