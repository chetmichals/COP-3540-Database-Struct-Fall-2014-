SET ECHO ON
--Chet Michals
--Z23000334
--User-id: Cmichals
--Password: SQLPassword

--1. List all Course_IDs scheduled for the FALL2014 semester.	
select course.course_id from course 
inner join schedule 
on course.COURSE_ID = schedule.COURSE_ID
where semester = 'FALL2014';

--2. Register yourself for this course (COP3540) for Semester SPRING2014. (Use SQLDeveloper to find the Schedule_ID for the Course.  Insert a Student record with a Student_ID of "Z999"? and your specifics for the rest of the data, and then Insert a Transcript record.  Give yourself an "A"? for the course.  Don't forget to Commit the Inserts.
insert into student 
(student_id, last_name, first_name, gender, birth_date, city)
values ('Z999','Michals','Chet','M','09-MAY-90','Boca Raton');

insert into transcript (STUDENT_ID, SCHEDULE_ID, GRADE) values
('Z999', 20140105, 'A');

commit;

--3. List all Students_IDs that have no Courses scheduled for SPRING2014 using a sub query.
(select distinct student.student_ID from student
where student.student_ID not in
(select transcript.student_ID from SCHEDULE 
inner join transcript
on transcript.SCHEDULE_ID = SCHEDULE.SCHEDULE_ID
where semester = 'SPRING2014'));

--4. List all Students_IDs that have no Courses scheduled for SPRING2014 using a Set type operation.
select student_ID from student 
minus
select student_ID from schedule 
inner join transcript
on transcript.SCHEDULE_ID = schedule.SCHEDULE_ID
where semester = 'SPRING2014';


--5. List all Students' Names and IDs where the student has an "A"? in COP3530 AND has taken or is taking COP3540.

select FIRST_NAME||' '||LAST_NAME as Name, STUDENT_ID from STUDENT
where STUDENT_ID in
(select STUDENT_ID from TRANSCRIPT
INNER JOIN SCHEDULE
on TRANSCRIPT.SCHEDULE_ID = SCHEDULE.SCHEDULE_ID
WHERE COURSE_ID = 'COP3530'
AND GRADE = 'A'
INTERSECT(
select STUDENT_ID from TRANSCRIPT
INNER JOIN SCHEDULE
on TRANSCRIPT.SCHEDULE_ID = SCHEDULE.SCHEDULE_ID
WHERE COURSE_ID = 'COP3540'));

--6. List all Students' Names and IDs where the student has an "A"? in COP3530 OR has taken or is taking COP3540.

select FIRST_NAME||' '||LAST_NAME as Name, STUDENT_ID from STUDENT
where STUDENT_ID in
(select STUDENT_ID from TRANSCRIPT
INNER JOIN SCHEDULE
on TRANSCRIPT.SCHEDULE_ID = SCHEDULE.SCHEDULE_ID
WHERE COURSE_ID = 'COP3530'
AND GRADE = 'A'
UNION(
select STUDENT_ID from TRANSCRIPT
INNER JOIN SCHEDULE
on TRANSCRIPT.SCHEDULE_ID = SCHEDULE.SCHEDULE_ID
WHERE COURSE_ID = 'COP3540'));

/* 7.	Print a Roster for this class for FALL2014.  Include Student ID, Last_Name, 
First_Name, Student_City, Course_Name, Course_ID, Location and Time.  
Location is the concatenation of Schedule City and Schedule Room with a column heading or "Location"?.
*/

select STUDENT.STUDENT_ID, STUDENT.LAST_NAME, STUDENT.FIRST_NAME, 
STUDENT.CITY as "Student City", COURSE.COURSE_NAME,
SCHEDULE.COURSE_ID, SCHEDULE.CITY|| ' '||SCHEDULE.ROOM as Location, SCHEDULE.CLASS_TIME
from SCHEDULE
inner join TRANSCRIPT
on SCHEDULE.SCHEDULE_ID = TRANSCRIPT.SCHEDULE_ID
inner join STUDENT
on STUDENT.STUDENT_ID = TRANSCRIPT.STUDENT_ID
inner join COURSE
on COURSE.COURSE_ID = SCHEDULE.COURSE_ID
where SCHEDULE.COURSE_ID = 'COP3540' and SCHEDULE.SEMESTER = 'FALL2014';

-- 8.	Calculate the number of credit hours taken by each student by Semester showing Student_ID, Semester and the total credit hours.
select STUDENT.STUDENT_ID, SCHEDULE.SEMESTER, SUM(COURSE.CREDIT_HOURS) from TRANSCRIPT
inner join SCHEDULE
on TRANSCRIPT.SCHEDULE_ID = SCHEDULE.SCHEDULE_ID
inner join COURSE
on COURSE.COURSE_ID = SCHEDULE.COURSE_ID
inner join STUDENT
on STUDENT.STUDENT_ID = TRANSCRIPT.STUDENT_ID 
group by STUDENT.STUDENT_ID, SCHEDULE.SEMESTER;

-- 9. Write an Oracle function named "GPAPointsCalc"? that can be used to get "point values"? for a grade to be used for grade point average calculations.  (A=4, B=3, C=2, D=1, F=0)
CREATE OR REPLACE FUNCTION GPAPointsCalc(grade IN VARCHAR2)
   RETURN NUMBER
   IS
   BEGIN
      IF grade = 'A' THEN
        return 4;
      END IF;
      IF grade = 'B' THEN
        return 3;
      END IF;
      IF grade = 'C' THEN
        return 2;
      END IF;
      IF grade = 'D' THEN
        return 1;
      END IF;
      IF grade = 'F' THEN
        return 0;
      END IF;
      return null;
    END;
	/
  
/*10.	Print a Transcript for Edward Nygma that uses the function above to 
calculate point values for a class.  Include Course_ID, Section, Course_Name, 
Instructor Last and First Name, Semester, Grade and the GPA Points returned by 
the function.  (Edward's ID Number is Z145!!)*/
select COURSE.COURSE_ID, SCHEDULE.SECTION, COURSE.COURSE_NAME,
INSTRUCTOR.LAST_NAME|| ', '|| INSTRUCTOR.FIRST_NAME as "Instructor Name",
SCHEDULE.SEMESTER, TRANSCRIPT.GRADE, GPAPointsCalc(TRANSCRIPT.GRADE) as "GPA Points"
from STUDENT
inner join TRANSCRIPT
on TRANSCRIPT.STUDENT_ID = STUDENT.STUDENT_ID
inner join SCHEDULE
on TRANSCRIPT.SCHEDULE_ID = SCHEDULE.SCHEDULE_ID
inner join COURSE
on COURSE.COURSE_ID = SCHEDULE.COURSE_ID
inner join INSTRUCTOR
on INSTRUCTOR.INSTRUCTOR_ID = SCHEDULE.INSTRUCTOR_ID
where STUDENT.STUDENT_ID = 'Z145';


-- 11.	List all students (IDs, Last_Name, First_Name, GPA) with a GPA lower than Edwards's GPA.  (Hmm, how does one calculate a GPA??)
select STUDENT.STUDENT_ID, STUDENT.LAST_NAME, STUDENT.FIRST_NAME, GPA_TABLE."GPA" from
(
	select STUDENT.STUDENT_ID, (SUM((GPAPointsCalc(TRANSCRIPT.GRADE))*(COURSE.CREDIT_HOURS))/SUM(COURSE.CREDIT_HOURS))as "GPA"
	from STUDENT
	inner join TRANSCRIPT
	on TRANSCRIPT.STUDENT_ID = STUDENT.STUDENT_ID
	inner join SCHEDULE
	on TRANSCRIPT.SCHEDULE_ID = SCHEDULE.SCHEDULE_ID
	inner join COURSE
	on COURSE.COURSE_ID = SCHEDULE.COURSE_ID
	inner join INSTRUCTOR
	on INSTRUCTOR.INSTRUCTOR_ID = SCHEDULE.INSTRUCTOR_ID
	where TRANSCRIPT.GRADE is not null
	group by STUDENT.STUDENT_ID
	having (SUM((GPAPointsCalc(TRANSCRIPT.GRADE))*(COURSE.CREDIT_HOURS))/SUM(COURSE.CREDIT_HOURS)) <
	(
		select (SUM((GPAPointsCalc(TRANSCRIPT.GRADE))*(COURSE.CREDIT_HOURS))/SUM(COURSE.CREDIT_HOURS))as "GPA"
		from STUDENT
		inner join TRANSCRIPT
		on TRANSCRIPT.STUDENT_ID = STUDENT.STUDENT_ID
		inner join SCHEDULE
		on TRANSCRIPT.SCHEDULE_ID = SCHEDULE.SCHEDULE_ID
		inner join COURSE
		on COURSE.COURSE_ID = SCHEDULE.COURSE_ID
		inner join INSTRUCTOR
		on INSTRUCTOR.INSTRUCTOR_ID = SCHEDULE.INSTRUCTOR_ID
		where TRANSCRIPT.GRADE is not null
		and STUDENT.STUDENT_ID = 'Z145'
		group by STUDENT.STUDENT_ID
	)
) GPA_TABLE
inner join STUDENT
on STUDENT.STUDENT_ID = GPA_TABLE.STUDENT_ID;

--12.	List the Student IDs of all students that received ONLY ‘B's or "C's.  Don't count courses that do not yet have a grade.
select distinct STUDENT_ID from TRANSCRIPT
where GRADE in ('B','C')
minus 
(
select STUDENT_ID from TRANSCRIPT
where GRADE not in ('B','C')
and GRADE is not null
);

-- 13.	List all Student_Ids, Last_Names and First_Names that took a course in SPRING2014 that they also took in another semester.
select STUDENT.STUDENT_ID,STUDENT.LAST_NAME, STUDENT.FIRST_NAME from
(select STUDENT.STUDENT_ID, SCHEDULE.COURSE_ID from STUDENT
inner join TRANSCRIPT
on STUDENT.STUDENT_ID = TRANSCRIPT.STUDENT_ID
inner join SCHEDULE
on TRANSCRIPT.SCHEDULE_ID = SCHEDULE.SCHEDULE_ID
where SCHEDULE.SEMESTER = 'SPRING2014'
INTERSECT (
select STUDENT.STUDENT_ID, SCHEDULE.COURSE_ID from STUDENT
inner join TRANSCRIPT
on STUDENT.STUDENT_ID = TRANSCRIPT.STUDENT_ID
inner join SCHEDULE
on TRANSCRIPT.SCHEDULE_ID = SCHEDULE.SCHEDULE_ID
where SCHEDULE.SEMESTER != 'SPRING2014'
)
) dupes
inner join STUDENT
on STUDENT.STUDENT_ID = dupes.STUDENT_ID;

commit;