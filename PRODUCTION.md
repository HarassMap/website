# Setting Up HarassMap on a Production Server

This guide assumes:

* You have an Ubuntu Linux 18.04 server up and running. We recommend [DigitalOcean](https://www.digitalocean.com) as a virtual server provider.
* You have a domain name (e.g. yoursite.example.com) pointing to the server's IP address. We recommend [Gandi](https://www.gandi.net) as a domain name registrar.
* You have an account with an outgoing email provider and have access to SMTP credentials or an API key. We recommend [Mailgun](https://www.mailgun.com).
* Port 443 (HTTPS) on the server is open to the world, and port 80 is also open if you want to redirect HTTP traffic (recommended).
* You have SSH'ed to the server as the root user or a user with sudo privileges.

### Install Dependencies and Packages

First, update your package list and upgrade any system packages.

    sudo apt update && sudo apt upgrade -y

Choose the default option if any screens pop up prompting you for something.

Then install the required packages for HarassMap:

    sudo apt-get install software-properties-common
    sudo add-apt-repository ppa:ondrej/php
    sudo apt update
    sudo apt install -y git mariadb-server apache2 composer nodejs npm php7.1 php7.1-ctype php7.1-curl php7.1-xml php7.1-fileinfo php7.1-gd php7.1-json php7.1-mbstring php7.1-mysql php7.1-sqlite3 php7.1-zip libapache2-mod-php7.1 nano

### Setup a Database

Enter the MySQL console:

    sudo mysql -u root

Select a password for the `harassmap` database user and save it securely.

At the MySQL prompt, enter:

    CREATE DATABASE harassmap;
    GRANT ALL PRIVILEGES ON *.* TO 'harassmap'@'localhost' IDENTIFIED BY 'PASSWORD';

Replacing `PASSWORD` with the password you just selected. Now enter:

    \q

To exit the MySQL console.

### Create `deploy` User

This will be the (unprivileged) user under which the app runs.

    sudo adduser deploy

Enter a random password (you won't need it) and leave the rest blank.

### Get Code and Install More Dependencies

Change to the non-privileged deploy user:

    sudo su - deploy

Obtain the code:

    git clone https://github.com/nooraflinkman/harassmap.git
    cd harassmap

Install dependencies:

    composer install
    npm install

### Edit App Configuration

Copy the example configuration and fill out the values:

    cp .env.example .env
    nano .env

See the comments in the file for further guidance. For database settings, the defaults should work if
you followed the instructions above. You should only need to enter your password.

By default, the system will use local storage for uploads. If you want to use S3-style object storage
you can enter your settings.

Save the file and exit the text editor.

### Prepare Database and Assets

Migrate and seed the database:

    php artisan october:up
    php artisan harassmap:up

Compile assets:

    node_modules/.bin/gulp build --production

### Edit Apache Configuration

Return to the privileged user:

    exit

Enable the required Apache modules:

    sudo a2enmod rewrite
    sudo a2enmod ssl

Change the run user:

    sudo sed -i 's/www-data/deploy/g' /etc/apache2/envvars

Edit config:

    sudo nano /etc/apache2/sites-available/harassmap.conf

Paste in the following, replacing all occurrences DOMAIN with your domain name.

    <IfModule mod_ssl.c>
    <VirtualHost *:443>
      ServerName DOMAIN
      DocumentRoot /home/deploy/harassmap
      DirectoryIndex /index.php

      ErrorLog ${APACHE_LOG_DIR}/DOMAIN-error.log
      CustomLog ${APACHE_LOG_DIR}/DOMAIN-access.log combined

      <Directory "/home/deploy/harassmap">
        Options Indexes FollowSymLinks
        AllowOverride All
        Allow from All
        Require all granted
      </Directory>

      FallbackResource /index.php
    </VirtualHost>
    </IfModule>

Save and exit. Then activate the configuration:

    sudo a2ensite harassmap
    sudo systemctl restart apache2

### Obtain an SSL Certificate

Install certbot:

    sudo apt-get install -y software-properties-common
    sudo add-apt-repository universe
    sudo add-apt-repository ppa:certbot/certbot
    sudo apt-get update
    sudo apt-get install -y certbot python3-certbot-apache

Press enter when prompted. Run certbot to get a certificate:

    sudo certbot --apache

Your domain name should be automatically picked up from your configuration file.
When prompted whether to redirect HTTP traffic, enter '2' to confirm. When the process concludes
should have a valid SSL certificate.

Test automatic renewal:

    sudo certbot renew --dry-run

You should see "Congratulations, all renewals succeeded".

### Log in to the Admin Panel

Visit https://DOMAIN/admin, replacing DOMAIN with your domain name.

Sign in with username `admin` and password `admin`.

Click 'Admin Person' at top-right, click 'My account'. Change the account password
to something secure and change the email to an address you can access.

### Configure Email

It is important to configure the server so it can send email in case your users need to reset their password,
for example. In the admin panel, click 'Settings' in the top menu bar and then find 'Mail configuration'
in the left navigation menu. Enter your settings. This will depend on which email provider you are using.

Test your email configuration by logging out of the admin panel and using the 'Forgot your password?' link.
After entering your username, you should receive an email at the address you entered above. If not,
check the system event log in under 'Dashboard' in the admin panel.

### Google Analytics

If you'd like to use Google Analytics, you can enter your key in the admin
panel under 'Settings' > 'HarassMap Settings'. Remember to click the 'Save' button after you've
entered the key.

### Mutlisite

HarassMap supports multiple organizations using the same HarassMap server/instance.
See [multisite instructions](MULTISITE.md) for more information.
