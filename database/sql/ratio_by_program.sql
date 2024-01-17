SELECT program, COUNT(DISTINCT people.id) AS total_students, COUNT(internships.id) AS total_internships, ROUND((COUNT(internships.id) / COUNT(DISTINCT people.id)) * 100, 2) AS percentage
FROM people
LEFT JOIN internships ON people.id = internships.student_id
where people.year_id = 7
GROUP BY program;