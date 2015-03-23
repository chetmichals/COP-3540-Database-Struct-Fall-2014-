-- Get the classes the student is taking this term

select COURSE_ID from schedule
inner join TRANSCRIPT 
on schedule.SCHEDULE_ID = TRANSCRIPT.SCHEDULE_ID
where SEMESTER = 'SPRING2014'
and STUDENT_ID = 'Z123'

-- Get the available courses the student can take
select COURSE_ID from schedule 
where SEMESTER = 'SPRING2014' 
minus
(select COURSE_ID from schedule
inner join TRANSCRIPT 
on schedule.SCHEDULE_ID = TRANSCRIPT.SCHEDULE_ID
where SEMESTER = 'SPRING2014'
and STUDENT_ID = 'Z123')

