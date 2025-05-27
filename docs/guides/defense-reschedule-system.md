# Defense Reschedule Request System

The Defense Reschedule Request System allows students to request changes to their defense schedules and administrators to process these requests.

## Features

### Student Features
- View current defense schedule
- Submit reschedule requests with reason and preferred date/time
- Track status of reschedule requests
- Receive notifications when requests are processed

### Administrator Features
- View and manage all reschedule requests
- Approve or reject requests with notes
- Automatic rescheduling of defenses when requests are approved
- Badge indicator showing pending request count

## Components

1. **Database Models**
   - `RescheduleRequest` model to store request details
   - `RescheduleRequestStatus` enum for tracking request status (Pending, Approved, Rejected)

2. **Student Interface**
   - `StudentDefenseWidget` displays defense details and reschedule options
   - `RequestDefenseReschedule` page for submitting and viewing reschedule requests

3. **Admin Interface**
   - `RescheduleRequestResource` for managing reschedule requests
   - Approve/reject actions with notification system

4. **Notifications**
   - `RescheduleRequestSubmitted` notification for administrators
   - `RescheduleRequestProcessed` notification for students

5. **Services**
   - `DefenseReschedulingService` handles the automatic rescheduling of defenses

## Workflow

1. Student views their defense schedule on the dashboard
2. Student submits a reschedule request with reason and preferred date/time
3. Administrators receive a notification about the new request
4. Administrator reviews and approves or rejects the request
5. Student receives notification about the decision
6. If approved, the defense is automatically rescheduled
7. The rescheduled defense appears on the student's dashboard

## Implementation Details

- Uses Filament for administrative interfaces
- Livewire components for student-facing interfaces
- Database relationships for connecting timetables, students, and requests
- Queue-based notification system
