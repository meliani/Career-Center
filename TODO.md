# Database / Models
- [ ] add columns to projects table / model
    - [ ] Connection email sent
    - [ ] Connection email sent at
    - [ ] Connection email viewed at
- [ ] Add columns to project professor pivot
    - [ ] Overview email sent at
    - [ ] Overview email viewed
# Internship agreement resource
- [ ] Add bulk assign to department and bulk signing.

## Students features
- [x] Announce internship
- [x] View announced internship
- [x] Wait for confirmation
- [x] Generate agreement
- [x] Add teammate -> notify
- [x] Accept teammate -> notify
- [ ] Exchange with supervisors
- [ ] Add changeStatus observer to internship model to send notifications on all changes.

## department assignments
- database notification for new 
-   [x] Add status update on announced, signed, received, validated
-   [x] Disable action on non null
-   [x] Add badge for status
-   [ ] Add badge to signed at, validated at, received at, signed at and status
-   [x] Use enums instead of strings for status,
-   [x] Use enums assigned_department, current_year, department, program, role
-   [x] Add grouped action buttons : Internship, Manage, Defense

## Calendar

-   [ ] Add bulk action to assign a day for defense, add hour, add jury member
-   [x] Add jury member is a form with Member role and name
-   [ ] Add same bulks to individual actions
-   [x] Group actions by type

## Emails

-   [x] Add bulk emails to users, internships
-   [ ] Add send email to external supervisors
-   [x] Add send email to Internal supervisors
-   [ ] Add send email to jury
-   [ ] Add send defense email : to students and jury and referents
-   [ ] Add email notifications for internship validation, signature and announcement.

## Forms

-   [ ] Add defense authorization form
-   [ ] Generate defense document
-   [ ] Add action to send defense document to referent

## Internship management

-   [ ] Add internship started badge if started at is superior to today
-   [ ] Add notes field to form and table
-   [x] Unify internship resource

## policies for Internship model:

-   [x] Only department coordinator role can view records and use review function
-   [x] Only admins and coordinators can edit and delete records
-   [x] Professors can view assigned records and proprieties from internship model
-   [x] head of department can see related records from its assigned internships
-   [x] policies must check if a giver user can update Internship with a certain given program from students table.
-   [x] Consider binomes in assignment to projects
-   [ ] Add a tick box to enable overriding

## logs

Finish pxlrbt/filament-activity-log configuration

## enums

-   [x] Translate enums
-   [ ] List status of all status badges in the same column: Example of signature is null we gonna see (Announced, Validated, Pending Signature)
-   [x] Ignore is valid for internships and count on Announced status.

## frontend

-   [ ] Update QR code to an easy readable one
-   [ ] Add status on QR verification page

## Data

-   [ ] Parse keywords and put them into tags.

## project
-   [x] Adjust date format exports
-   [ ] Add internship process clear checklist
-   [x] Add internship agreements tab
- Add group action with different email templates :
    - Supervisors email
    - Students email
    - external supervisors email
- Add database notification to professors when assigned to a project with observer.
## Charts

-   [ ] Add supervisors count vs projects vs departments to dashboard
-   [x] Projects participation by department
-   [x] Filter graphs by only signed projects

## UX

-   [ ] Add tick box to assign interns to projects
-   [x] Add export projects
-   [x] Internship agreements page
    -   [x] Add name search
    -   [x] Add department and supervisors
    -   [x] Add sorting everywhere
- [ ] Add tick box to assign interns to projects
- [x] Add export projects
- [x] Interns agreements table
    - [x] Add name search
    - [x] Add department and supervisors

- [ ] Possibility for student to choose professor, up to 10 teachers per student
- [ ] Student choose supervisor
- [ ] Supervisor receive email for approval
- [ ] Supervisor can approve by email or by clicking on magic link and reply
- [ ] Supervisor receive in the email the magic link to access personal space
