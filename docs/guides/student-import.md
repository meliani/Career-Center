# Student Import Functionality

This document describes how to use the CSV import feature for students in the Career Center application.

## Import Process

The system allows administrators to import student data from CSV files. This can be used to update existing student records or create new ones.

### CSV Format Requirements

The CSV file should have headers that match the following fields:

- `matricule`: Student ID number
- `civilite`: Title (M. or Mme.)
- `nom`: Last name
- `prenom`: First name
- `الاسم_الشخصي`: First name in Arabic
- `الاسم_العائلي`: Last name in Arabic
- `annee_inscriptionreiscription_2024_2025`: Current enrollment status (e.g., première année, deuxième année)
- `code_filiere`: Program code
- `filiere`: Program name
- `cineacarte_sejour`: National ID card number
- `n_de_passeport`: Passport number
- `code_massar`: Massar code
- `date_de_naissance`: Birth date
- `lieu_de_naissance`: Birth place
- `مكان_الازدياد`: Birth place in Arabic
- `nationalite`: Nationality
- `adresse_de_correspondance`: Address
- `ville_de_residence`: City
- `annee_du_baccalaureat`: Baccalaureate year
- `serie_du_baccalaureat`: Baccalaureate type
- `mention_du_baccalaureat`: Baccalaureate mention
- `lieu_dobtention_du_bac`: Baccalaureate place
- `cnc`: CNC ID
- `filiere_cnc`: CNC program
- `classement_cnc`: CNC rank
- `telephone_mobile`: Student phone number
- `telephone_du_pere`: Father's phone number
- `telephone_de_la_mere`: Mother's phone number
- `email`: Personal email
- `mail_inpt`: Institutional email
- `annee_dentree`: Enrollment year
- `voie_dacces`: Access path
- `observations`: Notes

### Import Options

When importing students, you have the following options:

1. **Merge Mode**:
   - **Update existing students**: Update fields for existing students found in the database (by email or matricule)
   - **Skip existing students**: Only import new students, skip those already in the database
   - **Replace existing students**: Replace all fields for existing students with the data from the import file

2. **Academic Year**:
   - Select which academic year to associate with the imported students

### Process Steps

1. Navigate to the Students listing in the Administration panel
2. Click the "Import Students" button in the top-right corner
3. Upload your CSV file
4. Choose your merge mode and academic year
5. Click "Import"
6. The system will process the file and display a summary of the results

### Notes

- The import process is designed to handle errors gracefully. If certain rows cannot be imported, the process will continue and log the errors.
- A summary notification will show how many records were created, updated, skipped, or failed.
- For detailed error information, check the application logs.
- Students are matched by institutional email (mail_inpt) or matricule.
- Empty cells in the CSV will not overwrite existing data when using the 'update' mode.

## Troubleshooting

If you encounter issues during import:

1. Ensure your CSV file has the correct column headers
2. Check that the data formats match the expected formats (especially dates)
3. Verify that your CSV file is properly encoded (UTF-8 is recommended)
4. For large files, the import may take some time to process
