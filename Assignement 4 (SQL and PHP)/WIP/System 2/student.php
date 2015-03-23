<?php
	$student = $_GET['student_id'];
	$query = "SELECT  LAST_NAME, FIRST_NAME, GENDER, CITY, TO_CHAR(BIRTH_DATE,'YYYY-MM-DD') as BIRTHDAY from STUDENT WHERE student_ID = '$student'";
	//echo $query;
	
	// Create connection to Oracle
	$user = 'cmichals';
	$pass = 'SQLPassword';
	$server = 'dboracle.eng.fau.edu/r11g';
	$conn = oci_connect($user, $pass, $server) or die('Could not connect to SQL server');
	
	$stid = oci_parse($conn, $query);
	oci_execute($stid);
	//Basic Error handling
	if (!$conn)
	{
	   $m = oci_error();
	   echo $m['message'], "\n";
	   exit;
	}

?>
<HTML>
<BODY>
<P><H1>UPDATE STUDENT INFORMATION FOR STUDENT ID: <? echo $student ?> </H1></P>
<FORM ACTION = "http://lamp.cse.fau.edu/~cmichals/COP3540/studentUpdate.php" method="get">

<? $rs = oci_fetch_array($stid, OCI_ASSOC); 
if (!$rs) 
{echo "<P><H1>student " . $student . " Not Found</H1></P>";
 echo '<P><A href="http://lamp.cse.fau.edu/~cmichals/COP3540/student.html"> Press Here to Start Another Update </A></p>';
 exit;
}
?>

	<table><tr>
		<td>Last Name: </td><td><input type="text" name="last" size="30" value="<? echo $rs["LAST_NAME"];?>" /></td>
	</tr>
		<tr>
		<td>First Name: </td><td><input type="text" name="first" size="30" value="<? echo $rs["FIRST_NAME"];?>" /></td>
	</tr>
	<tr>
		<td>City: </td><td><input type="text" name="city" size="20" value="<? echo $rs["CITY"];?>" /></td>
	</tr>
	<tr>
		<td>Birth Date: </td><td><input type="date" name="date" value="<? echo $rs["BIRTHDAY"];?>" /></td>
	</tr>
	<tr>
		<td>Gender: </td><td>
		<?php
		if ($rs["GENDER"] == "M")
		{
			print '<input type="radio" name="gender" value="M" checked > Male<br><input type="radio" name="gender" value="F"> Female';
		}
		else
		{
			print '<input type="radio" name="gender" value="M"> Male<br><input type="radio" name="gender" value="F" checked > Female';
		}
		?>
		</td>
	</tr>
	</table>
		<input type="hidden" name="student_id" size="10" value="<? echo $student;?>" />

	

		<input type="submit" value="Push To Update" />

	
 

<P>
  <A href="http://lamp.cse.fau.edu/~cmichals/COP3540/student.html"> Press Here to Start Another Update 
  </A>
  </p>
  </FORM>
</BODY>
</HTML>
<?oci_close($conn);?>