-- SQL Used for Mini-system 1, to show a transcript


-- Make Transcript for the student (
select COURSE.COURSE_ID, COURSE.COURSE_NAME, transcript.GRADE, COURSE.CREDIT_HOURS, GPAPointsCalc(transcript.GRADE) * COURSE.CREDIT_HOURS as "Quality Points"  
from transcript
inner join SCHEDULE
on SCHEDULE.SCHEDULE_ID = transcript.SCHEDULE_ID
inner join COURSE
on SCHEDULE.COURSE_ID = COURSE.COURSE_ID
where transcript.STUDENT_ID = 'Z123'
order by transcript.SCHEDULE_ID;

-- Calculate total GPA and display credits earned
select sum(GPAPointsCalc(transcript.GRADE) * COURSE.CREDIT_HOURS) / sum(COURSE.CREDIT_HOURS) as "GPA", sum(COURSE.CREDIT_HOURS)
from transcript
inner join SCHEDULE
on SCHEDULE.SCHEDULE_ID = transcript.SCHEDULE_ID
inner join COURSE
on SCHEDULE.COURSE_ID = COURSE.COURSE_ID
where transcript.STUDENT_ID = 'Z123'
and transcript.GRADE is not NULL
order by transcript.SCHEDULE_ID;

-- GPA and Credits for a single semester
select sum(GPAPointsCalc(transcript.GRADE) * COURSE.CREDIT_HOURS) / sum(COURSE.CREDIT_HOURS) as "GPA", sum(COURSE.CREDIT_HOURS)
from transcript
inner join SCHEDULE
on SCHEDULE.SCHEDULE_ID = transcript.SCHEDULE_ID
inner join COURSE
on SCHEDULE.COURSE_ID = COURSE.COURSE_ID
where transcript.STUDENT_ID = 'Z123'
and transcript.GRADE is not NULL
and SCHEDULE.SEMESTER = 'SPRING2014'