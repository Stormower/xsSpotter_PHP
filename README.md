xsSpotter is a little tool wrote in PHP-CLI to find XSS flaws.

usage:
php scanner.php

When you run the script, it asks:
Make a choice: 
 1- Scan a website (default) 
 2- Scan multiple websites 
 3- Use Google dork 
 
 You will have to type a number and press ENTER.
 1- is to scan a unique URL. if you make this choice, type URL and press ENTER.
 2- is to scan multiple websites. If you make this choice, type the name of the list file and press ENTER.
 3- This still don't work.

Then, the script will ask you:
Choose accuracy (a number):
You will have to give it a number. Higher this number is, more chance the script have to scan all pages on the website.
25 is a good number but if you want to be sure to scan everything, type 100.

You will see:
INITIALISATION
and then
SCANNING FILES
It means the screen is clicking on every link on the website and saving each URL.

XXX links FOUND is the amount of time the script clicked on a link.
Deleting these that are same
XXX links FOUND !! is the number of unique links found on the website.

You will see:
Now, let's scan for XSS and SQL flaws ! 
If XSS FOUND !! appears, the script found a XSS. The link is in result.txt.
If SQL FOUND !! apears, the script MIGHT HAVE FOUND a SQL. But it CAN BE A FALSE POSITIVE !!

All the links found on the scanned website are in example.com.txt

if you have any question or if you want to contribute to this project, you can find me on this Discord: https://discordapp.com/invite/KBQdtRJ



