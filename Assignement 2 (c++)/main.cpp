//Writen by Chet Michals, September 18th 2014
#include <iostream>
#include <fstream>
#include <string>
#include <iomanip>
using namespace std;

#define BLOCKLENGTH 156
#define BLOCKINFOLENGTH 10
#define RECORDLENGTH 48

// Global Variables
fstream dataFile ("StudentGPAHashBlock.dat");


//Structers
struct studentRecord
{
	string ID;
	string LastName;
	string FirstName;
	string GPA;
	string deleteSwitch;
};

struct blockInfo
{
	int recordsUsed;
	int nextPage;
	int currentPage;
	int recordStates[3];
	int recordID[3];
	bool freeRecords;
	string rawBlock;
};

//Function Prototypes
void Dump();
blockInfo getBlockInfo(string block);
blockInfo getBlockInfo(string block, int blockID);
string getBlock(int blockNumber);
blockInfo nullBlock();

int main()
{
	Dump();
	system("pause");
	return 0;
}

string getBlock(int blockNumber)
{
	string returnString;
	if (blockNumber < 0)
	{
		//This should never happen
		cout << "An error has occured, trying to retreve an invalid block." << endl;
		return 0;
	}
	dataFile.seekg(BLOCKLENGTH*blockNumber); //Set the possition in the file
	getline (dataFile, returnString); // Get the line
	return returnString;
}

blockInfo getBlockInfo(string block)
{
	string tempChunk;
	blockInfo newBlockInfo;
	newBlockInfo.freeRecords = false;

	//Get the number of records
	tempChunk = block.substr(0,5);
	newBlockInfo.recordsUsed = stoi(tempChunk, nullptr, 10); //Convert string to int

	//Get the next page
	tempChunk = block.substr(5,5);
	newBlockInfo.nextPage = stoi(tempChunk, nullptr, 10); //Convert string to int

	//Get the state of each record
	for (int i = 0; i < 3; i++)
	{
		tempChunk = block.substr(BLOCKINFOLENGTH+(RECORDLENGTH * i),1); //Grab the delete switch for the correct record
		newBlockInfo.recordStates[i] = stoi(tempChunk, nullptr, 10); //Convert string to int
		if (newBlockInfo.recordStates[i] != 1)
		{
			newBlockInfo.freeRecords = true;
		}
		tempChunk = block.substr(1+BLOCKINFOLENGTH+(RECORDLENGTH * i),3); //Grab the ID of the record
		newBlockInfo.recordID[i] = stoi(tempChunk, nullptr, 10); //Convert string to int
	}
	newBlockInfo.rawBlock = block;
	return newBlockInfo;
}

blockInfo getBlockInfo(string block, int blockID)
{
	blockInfo localBlock = getBlockInfo(block);
	localBlock.currentPage = blockID;
	return localBlock;
}

void Dump()
{ 
	string sCommand = "type StudentGPAHashBlock.dat";
	system(sCommand.c_str());
	return;
}

void Insert()
{
	int newID;
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
	if (FindID(newID).currentPage == -2)
	{
		cout << "Record of ID "<< newID << " already exisits." << endl;
	}
}

blockInfo FindID(int ID)
{
	int hash = ID % 10; //Get the hash for the initalRecord

	string localBlock = getBlock(hash);
	blockInfo localBlockInfo = getBlockInfo(localBlock,hash);
	

	do
	{
		//Checks the 3 IDs in the block
		for (int i = 0; i < 3; i++)
		{
			if (localBlockInfo.recordStates[i] != 1) continue; //If the record is virgin or deleted, don't check it
			if (localBlockInfo.recordID[i] == ID) return localBlockInfo;//record found, return information
		}
		localBlock = getBlock(localBlockInfo.nextPage);
		localBlockInfo = getBlockInfo(localBlock,localBlockInfo.nextPage);
	}
	while(localBlockInfo.nextPage != -1);

	//If it makes it to here, the record was not found, return a dummy block.
	return nullBlock();
}

blockInfo nullBlock()
{
	blockInfo nullblock;
	nullblock.currentPage = -2;
	return nullblock;
}

int nextFreePage(int startPage)
{

}