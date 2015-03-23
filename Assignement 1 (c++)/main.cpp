//Writen by Chet Michals, September 8th 2014
#include <iostream>
#include <fstream>
#include <string>
using namespace std;

#define ENTRYLENGTH 54

//Function Prototypes
void Print(fstream &dataFile, int nextEntry);
void Retrieve(fstream &dataFile, int nextEntry);
void Insert(fstream &dataFile, int nextEntry);
void Update(fstream &dataFile, int nextEntry);
void NotYetImplemented();


int main () {
	//Declare Varables
	fstream dataFile ("StudentGPA.dat");
	string tempLine,outputChunk,nextLinkChunk,switchChar;
	int topEntry = 0;

	//Get the line number of the header, located in the first line of the database file
	if (dataFile.is_open())
	{
		getline (dataFile,tempLine);
		nextLinkChunk = tempLine.substr(47,5);
		topEntry = stoi(nextLinkChunk,nullptr,10);
	}
	else //Some Error control in the event the file couldn't be opened 
	{
		cout << "Unable to open file";
		system("pause");
		return -1;
	}
	do //Runs the main menu
	{
		cout << "Enter P to Print, R to Retrieve, I to Insert \nU to Update or X to Exit the program. \nWhat do you want to do?" << endl;

		//Gets the user inputs, trucates it to only the first character
		cin >> switchChar;
		switchChar = switchChar.substr(0,1);

		//Should likely be cleaner as a switch, I'm not changing it now. 
		if (switchChar == "P" || switchChar == "p") //Print
		{
			Print(dataFile,topEntry);
		}
		else if (switchChar == "X" || switchChar == "x") //Exit
		{
			cout << "Now Exiting." << endl;
			break; //Gets out of while loop
		}
		else if (switchChar == "R" || switchChar == "r") //Retrieve
		{
			Retrieve(dataFile,topEntry);
		}
		else if (switchChar == "I" || switchChar == "i") //Insert
		{
			Insert(dataFile,topEntry);

			//Resets the top link after an insert
			dataFile.seekg(0); //Set the possition in the file
			getline (dataFile,tempLine); // Get the next line
			nextLinkChunk = tempLine.substr(47,5);
			topEntry = stoi(nextLinkChunk,nullptr,10);
		}
		else if (switchChar == "U" || switchChar == "u") //Update
		{
			Update(dataFile,topEntry);
		}
		else //Invalid input
		{
			cout << "Unknown Function \"" << switchChar << "\" Used, please use a valid function" << endl;
		}
	}
	while (switchChar!= "x" || switchChar != "X");
	dataFile.close();
	system("pause");
	return 0;
}

//Used to print the contents of the database file
void Print(fstream &dataFile, int nextEntry)
{
	string tempLine,outputChunk,nextLinkChunk;
	while (nextEntry > 0) //When NextEntry is negitive, the link list has reached the final element
	{
		dataFile.seekg(ENTRYLENGTH*nextEntry); //Set the possition in the file
		getline (dataFile,tempLine);
		nextLinkChunk = tempLine.substr(47,5); //Gets the ID of the next entry
		nextEntry = stoi(nextLinkChunk, nullptr, 10);
		outputChunk = tempLine.substr(0, 47); //Removes the ID part from the data output
		cout << outputChunk << endl;
	}
}

void NotYetImplemented()
{
	cout << "Not Yet Implemented" << endl;
} 

void Retrieve(fstream &dataFile, int nextEntry)
{
	int searchID = 0;
	int nextID;
	string tempLine,idChunk,nextLinkChunk;
	string userInput;

	//Get user input and format it
	cout << "Please input ID to be looked up: ";
	cin >> userInput;
	userInput = userInput.substr(0, 3); //Truncate the input.

	//Basic Error control in the even the input is not a number (or int to be techincal)
	try
	{
		searchID = stoi(userInput);
	}
	catch(...)
	{
		cout << "Invalid input";
		return;
	}


	while (nextEntry > 0) //When NextEntry is negitive, the link list has reached the final element
	{
		dataFile.seekg(ENTRYLENGTH*nextEntry); //Set the possition in the file
		getline (dataFile,tempLine); // Get the next line
		nextLinkChunk = tempLine.substr(47,5); //Gets the ID of the next entry
		nextEntry = stoi(nextLinkChunk, nullptr, 10); //Convert string to int
		idChunk = tempLine.substr(0, 3); //Get the ID
		nextID = stoi(idChunk, nullptr, 10); //Convert ID string to int
		
		if (searchID == nextID)
		{
			string outputChunk = tempLine.substr(0, 47); //Removes the ID part from the data output
			cout <<"Record Found" << endl;
			cout << outputChunk << endl;
			return;
		}
		else if (nextID > searchID) //Since its a linked list, if the ID is higher then the one being checked, the ID isn't in the record
		{
			cout <<"Record not found" << endl;
			return;
		}
	}
	cout <<"Record not found" << endl; //If it gets here, the record wasn't found
}

void Insert(fstream &dataFile, int nextEntry)
{
	int nextID;
	int currentLink = 0;
	int newID;
	string tempLine,idChunk,nextLinkChunk;
	string userInput;
	string newLine;
	string updateLine;
	string currentLine;
	string nextLine;

	/////////// get user input
	cout << "Please input the ID: ";
	cin >> userInput;

	//Error Handling to ensure inputed text is a number 
	try
	{
		userInput = userInput.substr(0, 3);

		//Use to pad an input smaller then 3 characters
		for (int i = userInput.length(); i <3; i++)
		{
			userInput = '0' + userInput;
		}
		newID = stoi(userInput);
		if (newID < 1) //Negitive numbers actually work to be honest, I just don't want them.
		{
			cout << "ID Must be a postive number between 1 and 999" << endl;
			return;
		}
		newLine += userInput;
	}
	catch(...)
	{
		cout << "Invalid input for User ID";
		return;
	}

	cout << "Please input the Last Name: ";
	cin >> userInput;
	userInput.resize(20,' '); 
	newLine += userInput;


	cout << "Please input the First Name: ";
	cin >> userInput;
	userInput.resize(20,' ');
	newLine += userInput;

	cout << "Please input the GPA: ";
	cin >> userInput;
	userInput.resize(4,' ');
	newLine += userInput;

	////////// End Getting user input

	//Grabs the top line to initlize the insertion search process
	dataFile.seekg(0); //Set the possition in the file to the top
	getline (dataFile,currentLine);
	nextLinkChunk = currentLine.substr(47,5); //Gets the ID of the next entry
	nextEntry = stoi(nextLinkChunk, nullptr, 10); // Converts ID to an int

	while (nextEntry > 0) //When NextEntry is negitive, the link list has reached the final element
	{
		dataFile.seekg(ENTRYLENGTH*nextEntry); //Set the possition in the file to the next entry
		getline (dataFile,nextLine); // Get the next line
		idChunk = nextLine.substr(0, 3); //Get the ID
		nextID = stoi(idChunk, nullptr, 10); //Convert ID string to int
		
		//Check to see if the next ID is greater then the current one
		if (newID == nextID)
		{
			cout <<"Record ID Overlap. Please make use of a diffrent ID or make use of Update" << endl;
			return;
		}
		else if (nextID > newID) //Since its a linked list, if the ID is higher then the one being checked for insert, it means we have found the propper possition to insert. 
		{
			break;
		}

		//Since the while loop didn't break, the linked list isn't in the right possition. Shifts the linked list to the next element
		currentLine = nextLine;
		currentLink = nextEntry; // Saves the location of the currently read link
		nextLinkChunk = nextLine.substr(47,5); //Gets the ID of the next entry
		nextEntry = stoi(nextLinkChunk, nullptr, 10); //Gets the location of the next link
		
	}

	//Shifts the location of the "next" element from the current line to the new line.
	updateLine =  currentLine.substr(0, 47); 
	string IDString = currentLine.substr(47, 5);
	newLine += IDString; 

	//Write new line
	dataFile.seekp( 0, ios_base::end ); //Sets the output possition to the end of the file
	dataFile << newLine << endl;

	//Sets the current line to point to the newly added line
	int currentLocation = dataFile.tellp();
	currentLocation = ((currentLocation - 1 )/ ENTRYLENGTH) ;
	IDString = to_string(currentLocation);

	//Use to pad the link location with spaces
	for (int i = IDString.length(); i <5; i++)
	{
		IDString = ' ' + IDString;
	}

	updateLine += IDString;
	dataFile.seekg(ENTRYLENGTH*currentLink); //Set the possition in the file
	dataFile << updateLine << endl;
}

void Update(fstream &dataFile, int nextEntry)
{
	int nextID;
	int newID;
	string idChunk;
	string nextLinkChunk;
	string userInput;
	string updateInfo;
	string currentLine;
	string nextLine;

	//Get the ID to be updated
	cout << "Please input the ID to be updated: ";
	cin >> userInput;

	//Error Handling to ensure inputed text is a number 
	try
	{
		userInput = userInput.substr(0, 3);
		newID = stoi(userInput); // Converts input to an int
		if (newID < 1) //Ensures number is positive, shouldn't really matter to be honest
		{
			cout << "ID Must be a postive number between 1 and 999" << endl;
			return;
		}
	}
	catch(...) //If stoi throws an error, this should get called.
	{
		cout << "Invalid input for User ID";
		return;
	}

	//Grabs the top line to initlize the search process
	dataFile.seekg(0); //Set the possition in the file to the top
	getline (dataFile,currentLine);
	nextLinkChunk = currentLine.substr(47,5); //Gets the ID of the next entry
	nextEntry = stoi(nextLinkChunk, nullptr, 10); // Converts ID to an int

	while (nextEntry > 0) //When NextEntry is negitive, the link list has reached the final element
	{
		dataFile.seekg(ENTRYLENGTH*nextEntry); //Set the possition in the file to the next entry
		getline (dataFile,nextLine); // Get the next line
		idChunk = nextLine.substr(0, 3); //Get the ID
		nextID = stoi(idChunk, nullptr, 10); //Convert ID string to int
		
		//Check to see if the next ID is greater then the current one
		if (newID == nextID)
		{
			cout <<"Record found: "<< endl << nextLine << endl;
			break;
		}
		else if (nextID > newID) //Since its a linked list, if the ID is higher then the one being checked for insert, it means we have found the propper possition to insert. 
		{
			cout <<"Record of ID " << newID << " could not be found to update" << endl;
			return;
		}

		//Since the while loop didn't break, the linked list isn't in the right possition. Shifts the linked list to the next element
		currentLine = nextLine;
		nextLinkChunk = nextLine.substr(47,5); //Gets the ID of the next entry
		nextEntry = stoi(nextLinkChunk, nullptr, 10); //Gets the location of the next link
		
	}

	//Used to break from function if the record is not found but the entire list has been searched
	if (nextEntry < 0) 
	{
		cout <<"Record of ID " << newID << " could not be found to update" << endl;
		return;
	}

	//Get the input for the update info
	cout << "Please input the updated Last Name: ";
	cin >> userInput;
	userInput.resize(20,' ');
	updateInfo += userInput;


	cout << "Please input the updated First Name: ";
	cin >> userInput;
	userInput.resize(20,' ');
	updateInfo += userInput;

	cout << "Please input the updated GPA: ";
	cin >> userInput;
	userInput.resize(4,' ');
	updateInfo += userInput;

	////////// End Getting user input
	dataFile.seekg(ENTRYLENGTH*nextEntry + 3); //Set the possition in the file for the update
	dataFile << updateInfo;
}