# notacms
##A very easy CMS for most small websites and business types.

##Install
To install simply copy folder to web root or subdirectory.
In your favourite web browser go to:

{server_name}/edit

username: admin
password: password

Be sure to change your login credentials soon after install.

##Troubleshooting
Ensure that the edit folder's permissions are set at 755. 
If 755 is not sufficient set to 777. 
chmod 755 edit/ -R


You might have to also run:
chown www-data:www-data edit/ -R

Ensure you have php-sqlite3 installed and restart your webserver.
Be sure to select the appropriate php-sqlite3 version for your distro.
For instance if using PHP5.6 you must install php5.6-sqlite3.

Good Luck!!

We will be adding a PayPal donate button soon as I like pots of tea.
