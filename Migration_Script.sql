SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
INSERT INTO teachers
    (id,identikey, name)
VALUES
    (0,'guest', 'Guest');

ALTER TABLE attendance
ADD attendee_name VARCHAR(127);

ALTER TABLE attendance
ADD attendee_email VARCHAR(127);

ALTER TABLE attendance
MODIFY COLUMN 
affiliation ENUM('RA', 'TA', 'GPTI', 'instructor', 'professor', 'other', 'staff');

ALTER TABLE teachers
MODIFY COLUMN 
affiliation ENUM('RA', 'TA', 'GPTI', 'instructor', 'professor', 'other', 'staff');
