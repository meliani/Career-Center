# User Guide for the Career Center Platform

## General Information

The source code of the Career Center platform is made available as open source for any security audit operation.

The platform is under a custom license: usage rights for non-commercial purposes are permitted with the owner's permission. Everything is in the readme file on the GitHub.com/MELIANI/career-center repository.

## Administrator / Teacher User Guide

### Account Activation

Go to the platform's homepage, go to the desired space and follow the forgotten password procedure, the accounts are already created by the administration with the institutional email address while we set up a Single Sign-On (SSO) system at the institute level.

## List of Features

### Dashboard

The dashboard contains graphs to have an overview of the progress of the PFE, technical and worker internships and their related statistics.

### Logic of the internship process on the platform

#### 1 - Launch of a campaign for the search for internships (PFE / Technical / Worker)

The campaign management is handled by system administrators and enterprise relations directors. They launch mailing campaigns for internship searches, defining:
- Campaign duration (start/end dates)
- Target fields of study
- Internship types (PFE, Technical, Worker)
- Geographical scope
- Target companies
- Search criteria

The email database maintenance and campaign execution are managed by authorized administrative staff, ensuring:
- Regular database updates
- Automated bounce processing
- Unsubscribe management
- Engagement tracking

The email database is composed of companies, former supervisors, professors, former students, former supervisors, university partners.

#### 2 - Internship announcements by companies

Companies interested in the campaign send their internship offers on the platform, the offers are validated by the DASER before being published on the platform.

#### 3 - Internship declaration

The student in this step fills out a form to provide information related to the generation of his convention.
Once this is done the student generates his convention, the status of his declaration will go from the status "draft" to the status "declared".

#### 2 - Validation by the department coordinator

Validation is done either by email or on the platform where he has access to students related to his field.

#### 3 - Follow-up by the school

the school ensures the follow-up of the conventions on the platform by providing the current status of the conventions on the platform ("completed", "signed")

#### 4 - Conversion of signed internship agreements into end-of-study projects

The project can contain pairs or trios so several students and several conventions if necessary.

#### 5 - Assignment of supervisors to projects

The assignment can be done directly by the CF and/or CD then validated by the administration for a meticulous control over the supervisions added by the colleagues.
The assignment can also be done in mass by injecting a file from a spreadsheet (excel or libre office calc).

#### 6 - Notification of students

Students are notified by email of the assignment of their supervisors.

#### 7 - Notification of supervisors

Supervisors are notified by email of the assignment of students.

#### 8 - Assignment of examiners

The same procedure as step 5, 6 and 7 is repeated for the assignment of examiners.

#### 9 - Defense schedules

##### Setting of defense intervals

the school defines the intervals when the defenses will take place start date and end of the interval, start time of the day and end time, duration of breaks, duration of defenses.

##### Room settings

Creation of rooms planned for defenses.

##### Generation of slots

With a tailor-made algorithm, the platform generates time slots for defenses according to the defined intervals, excludes holidays and weekends programmatically.

##### Slot assignment

With another algorithm the platform will distribute the projects on the time slots according to the planning constraints while avoiding the overlap of resources (teachers or rooms).

##### Choice of algorithms

The planning algorithms are adaptable according to needs.
    - Favor defenses by end date of internship minimizing waiting time
    - Favor defenses by minimizing teacher travel
    - Favor defenses by minimizing the interval of defenses

#### 10 - Notification of students

Students are notified by email of the assignment of their defense dates.

#### 11 - Notification of supervisors / examiners

Supervisors and examiners are notified by email of the assignment of defense dates.
