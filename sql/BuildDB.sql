SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
CREATE TABLE IF NOT EXISTS colleges (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    title VARCHAR(127),
    
    PRIMARY KEY (id),
    INDEX (title)
) ENGINE=INNODB;

INSERT INTO colleges
    (title)
VALUES
    ('College of Arts and Sciences'),
    ('Leeds School of Business'),
    ('School of Education'),
    ('College of Engineering and Applied Science'),
    ('Graduate School'),
    ('School of Law'),
    ('College of Media, Communication and Information'),
    ('College of Music'),
    ('Continuing Education and Professional Studies'),
    ('University Libraries'),
    ('Program in Environmental Design');

CREATE TABLE IF NOT EXISTS departments (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    title VARCHAR(72),
    acronym CHAR(4),
    STEM BOOLEAN,
    
    PRIMARY KEY (id),
    INDEX (title)
) ENGINE=INNODB;

INSERT INTO departments
    (title, acronym, STEM)
VALUES
    ('Aerospace Engineering Sciences', 'ASEN', true),
    ('Accounting and Business Law', 'ACCT', false),
    ('Anthropology', 'ANTH', true),
    ('Applied Mathematics', 'APPM', true),
    ('Art and Art History', 'AAAH', false),
    ('Asian Languages and Civilizations', 'EALC', false),
    ('Astrophysical and Planetary Sciences', 'ASTR', true),
    ('Atmospheric and Oceanic Sciences', 'ATOC', true),
    ('Career Services', 'CSVC', false),
    ('Chemical and Biological Engineering', 'CHEN', true),
    ('Chemistry and Biochemistry', 'CHEM', true),
    ('Civil, Environmental, and Architectural Engineering', 'CVEN', true),
    ('Classics', 'CLAS', false),
    ('Communication', 'COMM', false),
    ('Computer Science', 'CSCI', true),
    ('Comparative Literature', 'COML', false),
    ('Ecology and Evolutionary Biology', 'EBIO', true),
    ('Economics', 'ECON', true),
    ('Electrical, Computer, and Energy Engineering', 'ECEE', true),
    ('English', 'ENGL', false),
    ('Environmental Studies', 'ENVS', true),
    ('Ethnic Studies', 'ETHN', false),
    ('Finance', 'FNCE', false),
    ('Film Studies', 'FILM', false),
    ('French and Italian', 'FREN', false),
    ('Geography', 'GEOG', true),
    ('Geological Sciences', 'GEOL', true),
    ('Germanic and Slavic Languages and Literatures', 'GSLL', false),
    ('History', 'HIST', false),
    ('Humanities', 'HUMN', false),
    ('Integrative Physiology', 'IPHY', true),
    ('International Affairs', 'IAFS', false),
    ('Jewish Studies', 'JWST', false),
    ('Linguistics', 'LING', true),
    ('Management and Entrepreneurship', 'MGMT', false),
    ('Marketing', 'MKTG', false),
    ('Mathematics', 'MATH', true),
    ('Mechanical Engineering', 'MCEN', true),
    ('Molecular, Cellular, and Developmental Biology', 'MCDB', true),
    ('Music', 'MUSC', false),
    ('Operations and Information Management', 'OPIM', false),
    ('Philosophy', 'PHIL', false),
    ('Physics', 'PHYS', true),
    ('Political Science', 'PSCI', true),
    ('Psychology and Neuroscience', 'PSYC', true),
    ('Religious Studies', 'RLST', false),
    ('Sociology', 'SOCY', true),
    ('Spanish and Portuguese', 'SPAN', false),
    ('Speech, Language, and Hearing Sciences', 'SLHS', true),
    ('Theatre and Dance', 'THTR', false),
    ('Women\'s Studies', 'WMST', false),
    ('Other', '----', false);



CREATE TABLE IF NOT EXISTS teachers (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    identikey VARCHAR(24) UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    name VARCHAR(127),
    email VARCHAR(127),
    department_id INT UNSIGNED,
    year TINYINT,
    gender ENUM('male', 'female'),
    program ENUM('undergrad', 'masters', 'doctorate', 'postdoc', 'faculty', 'other'),
    affiliation ENUM('RA', 'TA', 'GPTI', 'instructor', 'professor', 'staff', 'other'),
    international ENUM('yes', 'no'),
    firstVTCer INT UNSIGNED,
    firstVTCdate DATE,
    firstVTCnotes VARCHAR(127),
    secondVTCer INT UNSIGNED,
    secondVTCdate DATE,
    secondVTCnotes VARCHAR(127),
    CCT_status ENUM('inactive', 'active', 'certified'),
    CCT_date DATE,
    CCT_disc_spec SMALLINT,
    CCT_obser_who VARCHAR(127),
    CCT_obser_date DATE,
    CCT_depteval_who VARCHAR(127),
    CCT_depteval_date DATE,
    CCT_port_status ENUM('incomplete', 'in review', 'accepted'),
    CCT_port_date DATE,
    CCT_survey_status ENUM('not sent', 'sent', 'completed'),
    CCT_survey_date DATE,
    CCT_kolb_quad ENUM('diverging', 'assimilating', 'converging', 'accommodating'),
    CCT_kolb_date DATE,
    CCT_kolb_who VARCHAR(127),
    CCT_wing_date DATE,
    CCT_wing_who VARCHAR(127),
    CCT_notes TEXT,
    PDC_status ENUM('inactive', 'active', 'certified'),
    PDC_date DATE,
    PDC_CV_status  ENUM('incomplete', 'in review', 'accepted'),
    PDC_CV_date DATE,
    PDC_visit_where varchar(127),
    PDC_visit_date DATE,
    PDC_port_status  ENUM('incomplete', 'in review', 'accepted'),
    PDC_port_date DATE,
    PDC_pres_title VARCHAR(255),
    PDC_pres_date DATE,
    PDC_plan_status  ENUM('incomplete', 'in review', 'accepted'),
    PDC_plan_date DATE,
    PDC_mentor_hrs SMALLINT,
    PDC_mentor_who VARCHAR(127),
    PDC_eval_date DATE,
    PDC_eval_who VARCHAR(127),
    PDC_survey_status ENUM('not sent', 'sent', 'completed'),
    PDC_survey_date DATE,
    PDC_notes TEXT,
    
    
    PRIMARY KEY (id),
    INDEX (identikey),
    INDEX (department_id),
    INDEX (firstVTCer),
    INDEX (secondVTCer),
    
    FOREIGN KEY (department_id)
        REFERENCES departments(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    
    FOREIGN KEY (firstVTCer)
        REFERENCES teachers(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    
    FOREIGN KEY (secondVTCer)
        REFERENCES teachers(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
    
) ENGINE=INNODB;

INSERT INTO teachers
    (id,identikey, name)
VALUES
    (0,'guest', 'Guest');
INSERT INTO teachers
    (identikey, name, email)
VALUES
    ('gtp', 'gtp', 'gtp@colorado.edu');


CREATE TABLE IF NOT EXISTS permissions (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    permission VARCHAR(63),
    
    PRIMARY KEY (id)
) ENGINE=INNODB;

INSERT INTO permissions
    (permission)
VALUES
    ('tinfo'),
    ('attendance'),
    ('wsinfo'),
    ('fbinfo'),
    ('wssignin'),
    ('reports'),
    ('su');



CREATE TABLE IF NOT EXISTS userpermissions (
    teacher_id INT UNSIGNED NOT NULL,
    permission_id INT UNSIGNED NOT NULL,
    
    PRIMARY KEY (teacher_id, permission_id),
    
    FOREIGN KEY (teacher_id)
        REFERENCES teachers(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (permission_id)
        REFERENCES permissions(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=INNODB;

INSERT INTO userpermissions
    (teacher_id, permission_id)
VALUES
    (1, 1),
    (1, 2),
    (1, 3),
    (1, 4),
    (1, 5),
    (1, 6),
    (1, 7);



CREATE TABLE IF NOT EXISTS series (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    title VARCHAR(255),
    
    PRIMARY KEY (id),
    INDEX (title)
) ENGINE=INNODB;

INSERT INTO series
    (title)
VALUES
    ('Monday'),
    ('Technology'),
    ('Friday Forum'),
    ('Intercultural and Diversity'),
    ('Course Design'),
    ('Lead Training'),
    ('TIGER 1'),
    ('TIGER 2'),
    ('TIGER DOC'),
    ('Fall Intensive');



CREATE TABLE IF NOT EXISTS workshops (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    head_count SMALLINT UNSIGNED,
    date DATE,
    time TIME,
    title VARCHAR(255),
    series_id INT UNSIGNED,
    semester ENUM('spring', 'summer', 'fall'),
    credits TINYINT UNSIGNED NOT NULL DEFAULT 1,
    
    PRIMARY KEY (id),
    INDEX (series_id),
    
    FOREIGN KEY (series_id)
        REFERENCES series(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
    
) ENGINE=INNODB;



CREATE TABLE IF NOT EXISTS presenters (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    workshop_id INT UNSIGNED NOT NULL,
    teacher_id INT UNSIGNED,
    name VARCHAR(63),
    
    PRIMARY KEY (id),
    INDEX (workshop_id),
    INDEX (teacher_id),
    
    FOREIGN KEY (workshop_id)
        REFERENCES workshops(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    
    FOREIGN KEY (teacher_id)
        REFERENCES teachers(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
    
) ENGINE=INNODB;



CREATE TABLE IF NOT EXISTS feedback (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    workshop_id INT UNSIGNED,
    workshop_rating TINYINT,
    presenter_rating TINYINT,
    most_helpful TEXT,
    least_helpful TEXT,
    improve TEXT,
    recommend ENUM('yes', 'no'),
    recommend_why TEXT,
    suggestions TEXT,
    ref_GTPWeb BOOLEAN,
    ref_CIRTLWeb BOOLEAN,
    ref_CUCalendar BOOLEAN,
    ref_LeadEmail BOOLEAN,
    ref_DeptEmail BOOLEAN,
    ref_DeptPoster BOOLEAN,
    ref_RSSFeed BOOLEAN,
    ref_Twitter BOOLEAN,
    ref_Facebook BOOLEAN,
    department_id INT UNSIGNED,
    
    PRIMARY KEY (id),
    INDEX (workshop_id),
    INDEX (department_id),
    
    FOREIGN KEY (workshop_id)
        REFERENCES workshops(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    
    FOREIGN KEY (department_id)
        REFERENCES departments(id)
        ON UPDATE CASCADE ON DELETE SET NULL
    
) ENGINE=INNODB;



CREATE TABLE IF NOT EXISTS attendance (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    teacher_id INT UNSIGNED NOT NULL,
    workshop_id INT UNSIGNED NOT NULL,
    credits TINYINT UNSIGNED NOT NULL DEFAULT 1,
	attendee_name VARCHAR(127),
	attendee_email VARCHAR(127),
    department_id INT UNSIGNED,
    gender ENUM('male', 'female'),
    program ENUM('undergrad', 'masters', 'doctorate', 'postdoc', 'faculty', 'other'),
    affiliation ENUM('RA', 'TA', 'GPTI', 'instructor', 'professor', 'staff', 'other'),
    international ENUM('yes', 'no'),
    year TINYINT,
    
    
    PRIMARY KEY (id),
    INDEX (teacher_id),
    INDEX (workshop_id),
    
    FOREIGN KEY (teacher_id)
        REFERENCES teachers(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    
    FOREIGN KEY (workshop_id)
        REFERENCES workshops(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
    
) ENGINE=INNODB;



CREATE TABLE IF NOT EXISTS actions (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    teacher_id INT UNSIGNED NOT NULL,
    ip VARBINARY(16) NOT NULL,
    action ENUM('insert', 'update', 'delete', 'login', 'logout', 'status', 'info') NOT NULL,
    note VARCHAR(255),
    
    PRIMARY KEY (id),
    INDEX (teacher_id),
    
    FOREIGN KEY (teacher_id)
        REFERENCES teachers(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
    
) ENGINE=INNODB;
