This blackjack program was written by Janna Thomas for the PHP
moderately complex assignment.

This folder should be placed in any folder on the apache
server.  Access to the program can be obtained at any browser
by entering the server address in the URL entry.
For example, if the folder is placed in the root apache server
directory, the program can be accessed with the address:
        localhost/JT_Project/index.php

Care should be taken with program inputs as the database inputs are not fully validated.  Further improvements should be 
made to both bind the inputs and also to use a function like
"htmlspecialcharacters" to filter the input from potentially malicious 
attacks.  

Once a player is created they must be selected.

Running the program should create a new database and table as long as the 
login data is correct.  Even if a database connection cannot be established,
the program should still perform the basic game functions.

The user interface was tested in Chrome and Opera on a Linux system.

Login information for the database can be change by editing the login
file in the project root directory.
