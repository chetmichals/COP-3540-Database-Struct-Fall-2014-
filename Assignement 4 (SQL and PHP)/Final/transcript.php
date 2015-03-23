<html>
<head>
<title>Transcript</title>
</head>
<body>
<?php
	
	$user = 'cmichals';
	$pass = 'SQLPassword';
	$server = 'dboracle.eng.fau.edu/r11g';
	
	$sqlServer = oci_connect($user, $pass, $server) or die('Could not connect to SQL server');
	
	if (isset($_GET['IDNum']) == false)
	{	
		print '<form method="GET" action="'.$_SERVER["PHP_SELF"].'">';
		print '<center><h1>Please Input Student ID to look up</h1><br>';
		print '<input type="text" class="inputtable" placeholder="Input Student ID" name="IDNum"><br><br>';
		print '<input type = "submit" name = "send" class ="send" value = "Submit"></body></html>';
		print '</form>';
		print '<br><br><a href="index.html">Home Page</a>';
		exit;
	}
	else
	{
		$StudentID = strtoupper ($_GET['IDNum']);//strtoupper will make it so lower case input is turned into uppercase. 
	}
	
	
	$qury = 'select COURSE.COURSE_ID, COURSE.COURSE_NAME, transcript.GRADE, COURSE.CREDIT_HOURS, GPAPOINTSCALC(TRANSCRIPT.GRADE) * COURSE.CREDIT_HOURS AS "QUALITY_POINTS", SCHEDULE.SEMESTER
	from transcript 
	inner join SCHEDULE	on SCHEDULE.SCHEDULE_ID = transcript.SCHEDULE_ID 
	inner join COURSE on SCHEDULE.COURSE_ID = COURSE.COURSE_ID 
	where transcript.STUDENT_ID = \''.$StudentID.'\' order by transcript.SCHEDULE_ID';
				
	//$qury = 'select * from COURSE inner join SCHEDULE on SCHEDULE.COURSE_ID = COURSE.COURSE_ID';
	
	//$qury = 'SELECT INSTRUCTOR_ID, FIRST_NAME, LAST_NAME, CITY, OFFICE FROM INSTRUCTOR';
	//echo $qury;
	
	$parse = oci_parse($sqlServer, $qury);
	oci_execute($parse);
	$data =  oci_fetch_array($parse, OCI_BOTH);
	print "<center>";
	
	//Display if there is no record found
	if ($data == false)
	{
		print "<h2>No record found for student ID ".$StudentID."<h2>";
		print '<br><a href="'.$_SERVER["PHP_SELF"].'"><button>Back</button></a></body></html>';
		exit;
	}
	//Keep track of current semester, used to break up tables
	$currentSemester = $data['SEMESTER'];
	
	
	print "<h1>$StudentID</h1><br>";
	print '<h2>'.$currentSemester.'</h2><table border="1"><tr><th>COURSE ID</th><th>COURSE NAME</th><th>GRADE</th><th>CREDIT HOURS</th><th>QUALITY POINTS</th></tr>';
	
	//Output the data to tables
	do
	{
		//If the Semester is done, close out the table, print the GPA, and start a new table
		if ($data['SEMESTER'] != $currentSemester)
		{
			$qury = "select sum(GPAPointsCalc(transcript.GRADE) * COURSE.CREDIT_HOURS) / sum(COURSE.CREDIT_HOURS) as \"GPA\", sum(COURSE.CREDIT_HOURS)
			from transcript
			inner join SCHEDULE
			on SCHEDULE.SCHEDULE_ID = transcript.SCHEDULE_ID
			inner join COURSE
			on SCHEDULE.COURSE_ID = COURSE.COURSE_ID
			where transcript.STUDENT_ID = '".$StudentID."'
			and transcript.GRADE is not NULL
			and SCHEDULE.SEMESTER = '".$currentSemester."'";
			$semesterQurry =  oci_parse($sqlServer, $qury);
			oci_execute($semesterQurry);
			
			$semesterQurryResults =  oci_fetch_array($semesterQurry, OCI_BOTH);
			if ($semesterQurryResults != false)
			{
				print "<tr></tr>";
				print "<tr><th></th><th></th><th></th><th>Total Credits</th><th>GPA</th></tr>";
				print "<tr><td></td><td></td><td></td><td>".$semesterQurryResults[1]."</td><td>".number_format((float)$semesterQurryResults[0], 2, '.', '')."</td></tr>";
			}
			print "</table>";
			
			
			$currentSemester = $data['SEMESTER'];
			
			print '<h2>'.$currentSemester.'</h2><table border="1"><tr><th>COURSE ID</th><th>COURSE NAME</th><th>GRADE</th><th>CREDIT HOURS</th><th>QUALITY POINTS</th></tr>';
		}
		
		//Print the individual record's data
		print "<tr><td>";
		echo $data['COURSE_ID'];
		print "</td><td>";
		echo $data['COURSE_NAME'];
		print "</td><td>";
		echo $data['GRADE'];
		print "</td><td>";
		echo $data['CREDIT_HOURS'];
		print "</td><td>";
		echo $data['QUALITY_POINTS'];
		print "</td></tr>";
		
	}
	while (($data =  oci_fetch_array($parse, OCI_BOTH)) != false) ;
	
	//Print the GPA of the final semester
	$qury = "select sum(GPAPointsCalc(transcript.GRADE) * COURSE.CREDIT_HOURS) / sum(COURSE.CREDIT_HOURS) as \"GPA\", sum(COURSE.CREDIT_HOURS)
			from transcript
			inner join SCHEDULE
			on SCHEDULE.SCHEDULE_ID = transcript.SCHEDULE_ID
			inner join COURSE
			on SCHEDULE.COURSE_ID = COURSE.COURSE_ID
			where transcript.STUDENT_ID = '".$StudentID."'
			and transcript.GRADE is not NULL
			and SCHEDULE.SEMESTER = '".$currentSemester."'";
	$semesterQurry =  oci_parse($sqlServer, $qury);
	oci_execute($semesterQurry);
	
	//Close table with a final summery of credits earned. 
	$semesterQurryResults =  oci_fetch_array($semesterQurry, OCI_BOTH);
	if ($semesterQurryResults != false)
	{
		print "<tr></tr>";
		print "<tr><th></th><th></th><th></th><th>Total Credits</th><th>GPA</th></tr>";
		print "<tr><td></td><td></td><td></td><td>".$semesterQurryResults[1]."</td><td>".number_format((float)$semesterQurryResults[0], 2, '.', '')."</td></tr>";
	}
	print "</table>";
	
	//Now print the totals for the student
	$qury = 'select sum(GPAPointsCalc(transcript.GRADE) * COURSE.CREDIT_HOURS) / sum(COURSE.CREDIT_HOURS) as "GPA", sum(COURSE.CREDIT_HOURS)
			from transcript
			inner join SCHEDULE
			on SCHEDULE.SCHEDULE_ID = transcript.SCHEDULE_ID
			inner join COURSE
			on SCHEDULE.COURSE_ID = COURSE.COURSE_ID
			where transcript.STUDENT_ID = \''.$StudentID.'\'
			and transcript.GRADE is not NULL
			order by transcript.SCHEDULE_ID';
	//echo $qury;
	$parse = oci_parse($sqlServer, $qury);
	oci_execute($parse);
	$data =  oci_fetch_array($parse, OCI_BOTH);
	if ($data != false)
	{ 
		print '<h2>Totals</h2><table border="1"><tr><th>Credit Hours</th><th>GPA</th></tr>';
		print "<tr><td>".$data[1]."</td><td>".$data[0]."</td></tr>";
		print "</table>";
	}
	print '<br><a href="'.$_SERVER["PHP_SELF"].'"><button>Back</button></a>';
	print "</center>";
	
?>
</body>
</html>