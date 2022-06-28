# testSymfony

Test using Symfony console to build a simple console application for Catch code test

Author: Jingkai Zhang
Contact: zglker@gmail.com

# Config stmp email server

Please change the const values in src/Services/SendEmailService.php

# How to run

1. "composer install" 
2. "composer dump -o"
3. "php ./console.php test <email> <fileType>" (fileType only accept csv or jsonl)

# Assumption
The file is correct.
And the input to execute the code is correct.

You will see output with regarding file type generate in your local	
