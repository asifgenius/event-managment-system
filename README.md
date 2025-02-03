## Features
- User Registration & Login 
- Secure authentication with password hashing
- Admin  can Login
- Admin can Create, update, and delete events   
- User roles and permissions
- User can register for events
- Admin can download a CSV file of registered participants
- Capacity management
- Event details include title, description, location, duration, date, and maximum capacity
- Event listing with filters date, title
- View event details with updates 

## Technology
- PHP
- Bootstrap
- HTML
- CSS
- JavaScript
- AJAX
- Mysql

## Demo
![event_management_system](/public/docs/assets/event_management.gif)

## Installation
Run Apache web server

create a folder under xampp htdocs
```sh
mkdir event_managment;
```
clone the repo there
Run the SQL command to create table first: 
```sh
CREATE DATABASE event_management_system;
```

Run initial database script, go to terminal
```sh
php init.php
```
Verify the deployment to you server address in your browser
```sh
 http://localhost/event_managment/
```

## Usage
- Admin Login 
email → admin@gmail.com 
password→  1234

- User Login 
email → user@gmail.com 
password→  1234


## Status Code
| Status Code                   | Description                        
|-------------------------------|--------------------------
| 200                           | OK    
| 201                           | CREATE 
| 400                           | BAD REQUEST
| 404                           | NOT FOUND  
| 500                           | INTERNAL SERVER ERROR
