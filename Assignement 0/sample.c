#define _CRT_SECURE_NO_WARNINGS

/*
** Program Name: standard.c
**
** Purpose: Create an emp file and then read an employee record from 
** standard input, writing that record into the emp file.  Finally, 
** read the employee record from the emp file and print it on standard
** output.
**
**
*/
 
#include <stdio.h>
#include <stdlib.h>
main()
{
  /* fp is an stdio file pointer that will be used to access the open 
  emp file.*/ 

  FILE *fp;
  char ssn[10], name[21];
  float sal;

  /* create new file */
  fp = fopen("emp", "w"); 

  if (fp == 0)
  {
     printf("File can not be created, program terminated.\n");
     scanf("%f", &sal); /* This is to stop the Black Box from  
                        disappearing in Visual Studio 2010 */

     
     exit (1);
  }
  printf("Please enter SSN, Name, and Salary. \n");
	
  /* Read employee record from standard input */
  scanf("%s%s%f", ssn, name, &sal);
  /* Write that record into emp file.  Be sure to separate
     fields with blanks.
  */
  fprintf(fp, "%s %s %f\n", ssn, name, sal);

  fclose(fp);

  /* Now let's print the contents of emp. */
  fp = fopen("emp", "r");
  if(fp==0) { printf("Cannot print file.\n");
              exit(2);
            }
  fscanf(fp, "%s%s%f", ssn, name, &sal);
  printf("SSN= %s NAME= %s SALARY= %10.2f\n", ssn, name, sal);
  fclose(fp);
  scanf("%f", &sal); /* This is to stop the Black Box from  
                        disappearing in Visual Studio 2010 */
}
