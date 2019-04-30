ALTER TABLE teachers
ADD CCT_disc_spec_who VARCHAR(127),
ADD PECT_status ENUM('inactive', 'active', 'certified'),
ADD PECT_date DATE,
ADD PECT_disc_spec SMALLINT,
ADD PECT_disc_spec_who VARCHAR(127),
ADD PECT_obser_who VARCHAR(127),
ADD PECT_obser_date DATE,
ADD PECT_reflection_status ENUM('incomplete', 'in review', 'accepted'),
ADD PECT_reflection_date DATE,
ADD PECT_survey_status ENUM('not sent', 'sent', 'completed'),
ADD PECT_survey_date DATE,
ADD PECT_notes TEXT;

ALTER TABLE feedback
ADD ref_TARec BOOLEAN,
ADD ref_ClassAssign BOOLEAN;
