# MyFirstBlogOC -- How to Install

# STEP 1
Clone or Download project

# STEP 2

 - Move this in your www folder;
 -  Rename ( if you want ) the folder with your own app title.

# STEP 3
	
 - Open the "MyFirstBlogOC.sql" and copy the content.
 - Open phpMyAdmin or another sql software
 - Create a new Database
 - Paste the content in request tab.
 - The database is now install !

# STEP 4

 - Go to the Root folder of the project. 	
 - Rename ".env.exemple" in ".env". 	
 - Open ".env" file and insert your own configuration parameters.

# STEP 5

 - Open Config/app.yml 
 - Replace "prefix=/MyFirstBlogOC" by your own title app ( the name of the folder )
## ex:

 - if the app is at the root of your server: "prefix=" let empty;
 - if the app is in another folder: "prefix=/my/folder/titleApp"

# Step 6

 - Open your command shell prompt in the root folder path;
 - Type "composer install", **and now the app works !**
