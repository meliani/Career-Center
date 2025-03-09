[![Deploy on Push](https://github.com/meliani/Career-Center/actions/workflows/deploy.yml/badge.svg?branch=main)](https://github.com/meliani/Career-Center/actions/workflows/deploy.yml)
# Careers Management System for Schools

    ⚠️ 19 September 2024: We're back for some heavy work ths year ⚠️

    ⚠️ Warning ⚠️

    This repository contains the source code of carrieres.inpt.ac.ma, backed by proprietary code. 
    The license does not permit hosting it yourself.
    The code is open for the sake of transparency and allowing contributions.

  ✅ **If you have contributed to this repository, you can use the platfrom freely with no restrictions.** ✅
  *These are our policies today and may change in the future.*
  *If you are interested in using this platform for your organization, please contact the author at <m@meliani.xyz>.*
  
    ⚠️ Annother Warning: This Repository is Under Active Development ⚠️

    Please note that this repository is currently in development. 
    You may encounter bugs, incomplete features, or other issues.
    Be prepared for frequent updates and changes.
    Contributions, feedback, and bug reports are welcome. Thank you for your understanding!

## What is that?

A platform that assists students in finding internships and jobs, while also helping professors manage their students and internships.

## Where are we going?

We aim to expand our platform to offer more comprehensive career guidance and resources, fostering stronger connections between students, educators, and employers. Our goal is to become the leading solution for career management in educational institutions, providing seamless support from education to employment.

## What's Next?

We are integrating machine learning and smart algorithms to enhance our platform. These advancements will offer personalized internship and job recommendations, predictive career path analysis, and data-driven insights for professors to better support their students' career development.

## How to install

### 1. Clone the repository

```bash
git clone --recursive git@github.com:meliani/Career-Center.git
```

`--recursive` because I'm using a customized version of [Countries](https://github.com/parfaitementweb/filament-country-field) package to accurately describe our country. It's hosted as submodule in the `packages` folder.
It's not necessary to use `--recursive` if you don't want to use the customized version of the package. You can just remove the submodule and install the package from the official repository.
You can also remove call to the local repository in the `composer.json` file.

### 2. Install the dependencies

```bash
cd Career-Center && composer install && npm install
```

### 3. Create a database and update the .env file

```bash
cp .env.dev .env
```

### 4. Generate the application key and run the migrations

```bash
php artisan key:generate
```

```bash
php artisan migrate --seed
```

### 5. Link the storage folder and run the server

```bash
php artisan storage:link
```

```bash
php artisan serve
```

### 6. Addons

#### install fasttext data models

Go somewhere in your server and run comands below
Copy the binary and model paths to the `.env` file
    
```
git clone https://github.com/facebookresearch/fastText.git
cd fastText
make
```

```
wget https://dl.fbaipublicfiles.com/fasttext/supervised-models/lid.176.bin
```

If you have trouble with the installation, please send an email to <m@meliani.xyz> or open an issue.

## Features

### Quick overview

- Announce internships
- Generate internship agreements
- Generate internship projects
- Assign supervisors to projects
- Assign jury to projects
- Generate jury reports
- Generate calendars for students, supervisors, examiners, and jury
- Internship agreement management
- Send predefined emails to students, supervisors, examiners, and jury
- Send generic emails to students, supervisors, examiners, and jury
- Generate statistics
- Generate charts

For the comlete list look at [English docs](DOCS-EN.md) or [French docs](DOCS-FR.md)

## Department Prediction Feature

This feature uses FastText to automatically predict and assign departments to internship agreements based on their titles and descriptions.

### Prerequisites

1. Install FastText:
```
git clone https://github.com/facebookresearch/fastText.git
cd fastText
make
```

2. Download the language identification model:
```
wget https://dl.fbaipublicfiles.com/fasttext/supervised-models/lid.176.bin
```

3. Update the `.env` file with the paths to the FastText binary and model.

### Model Training and Prediction

You can train your own department prediction model and optionally run predictions using these commands:

```
# Generate training file
php artisan train:generate-department-file departments.train

# Train quantized model
php artisan train:department-model storage/app/training_data/training/departments.train --model=quantized

# Run prediction
php artisan predict:final-year-department
```

## Supported browsers, databases, and PHP versions

Check [Support.md](SUPPORT.md)

## contributors

- github.com/meliani
- github.com/copilot

## How to contribute

`Fork to your account -> Clone locally -> Create Feature Branche -> Make your changes -> push to your github -> Open pull request on main repo`

OR

`Open an Issue here -> explain your idea or join a gist for example.`

OR

`Start a topic in discussion panel.`

## Code quality

will be displayed soon

## testing

will be displayed soon

## License

Source code in this repository is covered by a proprietary license. The license does not permit hosting it yourself. The code is open for the sake of transparency and allowing contributions.

## Troubleshooting

### Permission denied (publickey) error

If you encounter the "Permission denied (publickey)" error when trying to clone the repository, you can resolve it by running the following command:

```bash
git config --global url."https://github.com/".insteadOf git@github.com:
```
