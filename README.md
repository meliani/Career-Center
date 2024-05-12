# Careers management system: Backend

    Warning

    This repository contains the source code of carrieres.inpt.ac.ma, backed by proprietary code. 
    The license does not permit hosting it yourself. 
    The code is open for the sake of transparency and allowing contributions.

## What is that ?

A platform that helps students to find internships and jobs, and helps professors to manage their students and internships.

## How to install

1. Clone the repository

    ```bash
        git clone git@github.com:meliani/Career-Center.git
    ```

2. Install the dependencies

    ```bash
    cd Career-Center && composer install && npm install
    ```

3. Create a database and update the .env file

    ```bash
    cp .env.example .env
    ```

4. Generate the application key and run the migrations

    ```bash
    php artisan key:generate
    ```

    ```bash
    php artisan migrate --seed
    ```

    ```bash
    php artisan serve
    ```

## Code quality

will be displayed soon

## testing

will be displayed soon

## Features

### Charts

- *Anounced Internships per Program*
    A chart that shows the number of internships announced per program
- *Projects participation by department*
    A chart that shows the number of projects per department
- *Internships per department*
    A chart that shows the number of internships per department
- *Internships per program*
    A chart that shows the number of internships per program
- *Intenships distribution by department*
    A chart that shows the number of internships per department
- *Internships distribution by city*
    A chart that shows the number of internships per city
- *Internships distribution by country*
    A chart that shows the number of internships per country
- *Internships distribution by company*
    A chart that shows the number of internships per company
- *Internships by end date*
    A chart that shows the number of internships by end date
- *Assigned supervisors per department*
    A chart that shows the number of assigned supervisors per department

### Emails

Send generic email (one by one or bulk) to students from students page
Send generic email from projects page
Send generic emails from internships page

### Forms

### policies

Strict policies for All model depending on the user role and professor role

### Calendar

### department assignments

### Students features

### Internship agreement management

## contributors

github.com/meliani
github.com/copilot

## How to contribute

Clone -> Create Feature Branche -> Change -> Commit -> Open pull request

Open Issue -> explain your idea or join a gist for example.

## License

Source code in this repository is covered by a proprietary license. The license does not permit hosting it yourself. The code is open for the sake of transparency and allowing contributions.
