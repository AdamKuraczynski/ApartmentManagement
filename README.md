﻿# ApartmentManagement

#### Mail testing end setup

- To test emails go to directory of includes/MailHog and run .exe
- MailHog will start and listen on port 1025 for SMTP connections and on port 8025 for the web interface.
- To see emails go to http://localhost:8025
- Edit line 19 in mail.php with temporary email for testing If communication does not work


## To do:

- notifications
- fix all panels from admin dashboard
  - maintenance
  - rental agreements
  - properties + addresses
- define types of properties, payments, documents, maintenance task statuses
- documents storing and access
- add panel that allows users modify their details
- add panels that allow you to view details of: (links to pages should be in the table in the "view" panels)
  - specific property,
  - user (only for admin or owner),
  - rental agreement,
  - maintienance task
