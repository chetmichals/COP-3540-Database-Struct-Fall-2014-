<?php
$student_id=$_GET['student_id'];
$last = $_GET['last'];
$first = $_GET['first'];
$city = $_GET['city'];
$date = $_GET['date'];
$gender = $_GET['gender'];

$query="UPDATE STUDENT SET LAST_NAME='$last', FIRST_NAME='$first', CITY='$city', GENDER='$gender', BIRTH_DATE=TO_DATE('$date', 'YYYY-MM-DD') WHERE STUDENT_ID='$student_id'";
// Create connection to Oracle

	$user = 'cmichals';
	$pass = 'SQLPassword';
	$server = 'dboracle.eng.fau.edu/r11g';
	$conn = oci_connect($user, $pass, $server) or die('Could not connect to SQL server');

if (!$conn) {
   $m = oci_error();
   echo $m['message'], "\n";
   exit;
}
$stid = oci_parse($conn, $query);
$count = oci_execute($stid);
?>
<HTML>
<BODY>
<?
if ($count==0)
{
	echo "<p><h1>Update for student NOT SUCCESSFUL</h1></p>";
}
else
{
	echo "<p><h1>Update for student SUCCESSFUL</h1></p>";
}
?>

<P>
  <A href="http://lamp.cse.fau.edu/~cmichals/COP3540/student.html"> Press Here to Start Another Update 
  </A>
  </p>
  </BODY>
  </HTML>
  