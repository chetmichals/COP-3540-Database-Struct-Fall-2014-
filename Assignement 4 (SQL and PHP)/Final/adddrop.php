<html>
<head>
<title>Add/Drop</title>
</head>
<body>
<?php
	
	$user = 'cmichals';
	$pass = 'SQLPassword';
	$server = 'dboracle.eng.fau.edu/r11g';
	
	$sqlServer = oci_connect($user, $pass, $server) or die('Could not connect to SQL server');
	
	
	//If no ID number is set, page has been hit first time, have user input student ID and select semester 
	if (isset($_GET['IDNum']) == false)
	{	
		print '<form method="GET" action="'.$_SERVER["PHP_SELF"].'">';
		print '<center><h1>Please Input Student ID to do ADD/DROP on</h1><br>';
		print '<input type="text" class="inputtable" placeholder="Input Student ID" name="IDNum"><br><br>';
		$qury = 'SELECT DISTINCT SEMESTER FROM SCHEDULE';
		$parse = oci_parse($sqlServer, $qury);
		oci_execute($parse);
		$data =  oci_fetch_array($parse, OCI_BOTH);
		
		print '<select name="Semester">';
		print "<option value=\"".$data['SEMESTER']."\">".$data['SEMESTER']."</option>";
		while (($data =  oci_fetch_array($parse, OCI_BOTH)) != false)
		{
			print "<option value=\"".$data['SEMESTER']."\">".$data['SEMESTER']."</option>";
		}
		
		print '</select>';
		print '<br><br><input type = "submit" name = "send" class ="send"></body></html>';
		print '</form>';
		print '<br><br><a href="index.html">Home Page</a>';
		exit;
	}
	else
	{
		$StudentID = strtoupper ($_GET['IDNum']);//strtoupper will make it so lower case input is turned into uppercase. 
		$Semester = $_GET['Semester'];
	}
	
	//If ether Dropcourse or Addcourse or set, drop or add the class. Makes it a bit snappy to add and remove classes
	if (isset($_GET['Dropcourse']))
	{
		$CourseID = $_GET['Dropcourse'];
		$qury = "DELETE FROM TRANSCRIPT WHERE STUDENT_ID = '".$StudentID."' AND  SCHEDULE_ID = '".$CourseID."'";
		//echo $qury;
		$stid = oci_parse($sqlServer, $qury);
		
		//Some error handling to help debug
		$r = oci_execute($stid);
		if (!$r) {
			$e = oci_error($stid);  // For oci_execute errors pass the statement handle
			print htmlentities($e['message']);
			print "\n<pre>\n";
			print htmlentities($e['sqltext']);
			printf("\n%".($e['offset']+1)."s", "^");
			print  "\n</pre>\n";
		}
	}
	else if (isset($_GET['Addcourse']))
	{
		$CourseID = $_GET['Addcourse'];
		$qury = "INSERT INTO TRANSCRIPT (STUDENT_ID, SCHEDULE_ID) VALUES ('".$StudentID."', '".$CourseID."')";
		//echo $qury;
		$stid = oci_parse($sqlServer, $qury);
		
		//Some error handling to help debug
		$r = oci_execute($stid);
		if (!$r) {
			$e = oci_error($stid);  // For oci_execute errors pass the statement handle
			print htmlentities($e['message']);
			print "\n<pre>\n";
			print htmlentities($e['sqltext']);
			printf("\n%".($e['offset']+1)."s", "^");
			print  "\n</pre>\n";
		}
	}
	
	
	//Get the classes student is signed up for
	$qury = 'SELECT COURSE_NAME, SCHEDULE.SCHEDULE_ID FROM COURSE inner join SCHEDULE on SCHEDULE.COURSE_ID = COURSE.COURSE_ID 
	inner join TRANSCRIPT on SCHEDULE.SCHEDULE_ID = TRANSCRIPT.SCHEDULE_ID 
	WHERE TRANSCRIPT.STUDENT_ID = \''.$StudentID .'\'and SCHEDULE.SEMESTER = \''.$Semester.'\'';
	
	//Get classes student isn't signed up for
	$qury2 = 'SELECT COURSE_NAME, SCHEDULE.SCHEDULE_ID FROM COURSE inner join SCHEDULE on SCHEDULE.COURSE_ID = COURSE.COURSE_ID
	WHERE SCHEDULE.SEMESTER = \''.$Semester.'\' minus
	SELECT COURSE_NAME, SCHEDULE.SCHEDULE_ID FROM COURSE inner join SCHEDULE on SCHEDULE.COURSE_ID = COURSE.COURSE_ID 
	inner join TRANSCRIPT on SCHEDULE.SCHEDULE_ID = TRANSCRIPT.SCHEDULE_ID 
	WHERE TRANSCRIPT.STUDENT_ID = \''.$StudentID .'\'and SCHEDULE.SEMESTER = \''.$Semester.'\'';
	
	//Get name
	$qury3 = 'SELECT FIRST_NAME||\' \'||LAST_NAME as NAME from STUDENT where STUDENT_ID = \''.$StudentID .'\'';
	$parse = oci_parse($sqlServer, $qury3);
	oci_execute($parse);
	$data =  oci_fetch_array($parse, OCI_BOTH);
	print "<center>";
	if ($data == false)
	{
		print "<h2>Student ID ".$StudentID." is not in system<h2>";
		print '<br><a href="'.$_SERVER["PHP_SELF"].'"><button>Back</button></a></body></html>';
		exit;
	}
	print "<H1>".$data['NAME']."</H1><H2>".$Semester."</h2><br><h2>ADD</h2>";
	
	
	//ADD
	$parse = oci_parse($sqlServer, $qury2);
	oci_execute($parse);
	$data =  oci_fetch_array($parse, OCI_BOTH);
	$submitAdd = 0;
	
	//Print the classes the student can sign up for
	if ($data == false)
	{
		print "No classes to sign up for";
	}
	else
	{
		print '<form method="GET" action="'.$_SERVER["PHP_SELF"].'">';
		print '<input type="radio" name="Addcourse" value="'.$data['SCHEDULE_ID'].'">'.$data['COURSE_NAME'].'<br>';
		$submitAdd = 1;
	}
	while (($data =  oci_fetch_array($parse, OCI_BOTH)) != false)
	{
		print '<input type="radio" name="Addcourse" value="'.$data['SCHEDULE_ID'].'">'.$data['COURSE_NAME'].'<br>';
	}
	if ($submitAdd == 1)
	{
		print '<input type="hidden" name="IDNum" size="10" value="'.$StudentID.'" />';
		print '<input type="hidden" name="Semester" size="10" value="'.$Semester.'" />';
		print '<br><input type = "submit" name = "send" class ="send"></form>';
	}
	
	
	// DROP
	print "<hr>";
	print "<h2>Drop</h2>";
	
	$parse = oci_parse($sqlServer, $qury);
	oci_execute($parse);
	$data =  oci_fetch_array($parse, OCI_BOTH);
	$submitDrop = 0;
	
	//Print the classes the student is signed up for
	if ($data == false)
	{
		print "No classes to drop";
	}
	else
	{
		print '<form method="GET" action="'.$_SERVER["PHP_SELF"].'">';
		print '<input type="radio" name="Dropcourse" value="'.$data['SCHEDULE_ID'].'">'.$data['COURSE_NAME'].'<br>';
		$submitDrop = 1;
	}
	while (($data =  oci_fetch_array($parse, OCI_BOTH)) != false)
	{
		print '<input type="radio" name="Dropcourse" value="'.$data['SCHEDULE_ID'].'">'.$data['COURSE_NAME'].'<br>';
	}
	if ($submitDrop == 1)
	{
		print '<input type="hidden" name="IDNum" size="10" value="'.$StudentID.'" />';
		print '<input type="hidden" name="Semester" size="10" value="'.$Semester.'" />';
		print '<br><input type = "submit" name = "send" class ="send"></form>';
	}
	
	print '<br><a href="'.$_SERVER["PHP_SELF"].'"><button>Back</button></a></body></html>';
?>
</body>
</html>