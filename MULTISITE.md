# Multisite Setup

This document details all the steps needed to set up a new domain alias on a HarassMap server. A domain alias is a way of allowing third party organizations to run an instance of HarassMap without purchasing server space or installing software. In essence, you’re simply redirecting a domain or subdomain at HarassMap and using the existing platform in real time.

Any domain or subdomain can therefore “alias” (be redirected) to HarassMap, and when HarassMap displays content for that domain, certain settings can be customized just for that domain. For instance, if you were to alias the domain `harassmap-antarctica.org` to HarassMap, HarassMap could be configured to display a different logo for visitors who arrive at HarassMap through that domain.

This guide assumes:

* You already have a running HarassMap server. See the [production setup guide](PRODUCTION.md).
* You have a domain name for the alias (e.g. yoursite2.example.com) pointing to the server's IP address. We recommend [Gandi](https://www.gandi.net) as a domain name registrar.
* You have SSH'ed to the server as the root user or a user with sudo privileges.

### Server Configuration

Add an apache config file for the new server. Follow the instructions below to set up the new host. Replace any instances of **NEW_DOMAIN** below with the new domain name.

    sudo nano /etc/apache2/sites-available/NEW_DOMAIN.conf

Paste the following into the config file:

    <IfModule mod_ssl.c>
    <VirtualHost *:443>
      ServerName NEW_DOMAIN
      DocumentRoot /home/deploy/harassmap
      DirectoryIndex /index.php

      ErrorLog ${APACHE_LOG_DIR}/NEW_DOMAIN-error.log
      CustomLog ${APACHE_LOG_DIR}/NEW_DOMAIN-access.log combined

      <Directory "/home/deploy/harassmap">
        Options Indexes FollowSymLinks
        AllowOverride All
        Allow from All
        Require all granted
      </Directory>

      FallbackResource /index.php
    </VirtualHost>
    </IfModule>

Save the file and exit the editor. Then run:

    sudo a2ensite NEW_DOMAIN
    sudo service apache2 reload

If you visit the website address in your browser, you may see a security warning about a security certificate not existing for the site. You may see a site asking for a username and password. Or you may see a version of the HarassMap site. Any of these options indicates that things are moving in the right direction with setup. Proceed to the next step.

### Obtain an SSL Certificate

We need to generate an SSL certificate for this new site so run the following:

    sudo certbot --apache

This should walk you through generating a certificate for this site. When prompted whether to redirect HTTP traffic, enter '2' to confirm. When the process concludes
should have a valid SSL certificate.

### Add Domain to Google Maps Token

When you set up the main HarassMap site, you would have generated a Google Maps token and may have restricted it to the main domain. If so, you must now
add the new domain as a permitted referrer. This can be achieved via the Google API Console. See Google's documentation for further instructions.

### Password Protect Site

If you'd like to temporarily password protect the new site while it is set up, first create an .htpasswd file:

    sudo su - deploy
    mkdir /home/deploy/private
    htpasswd -c /home/deploy/private/.htpasswd harassmapadmin

Enter and re-enter a password when prompted.

Now edit the config file:

    exit # Return to privileged user
    sudo nano /etc/apache2/sites-available/NEW_DOMAIN.conf

Find the following code inside the configuration file.

Replace the `Directory` block with the following:

      <Directory "/home/deploy/harassmap">
        AuthType Basic
        AuthName "Authentication Required"
        AuthBasicProvider file
        AuthUserFile /home/deploy/private/.htpasswd
        Require user harassmapadmin

        Options Indexes FollowSymLinks
        AllowOverride All
        Allow from All
      </Directory>

Note: `Require all granted` should not appear in the updated file.

Reload the server config:

    sudo systemctl reload apache2

The site should now be password protected. Check the website to make sure.

To remove password protection, change the `Directory` block back to it's previous state (see above).

### CMS Tasks

You must now create the domain in the site admin panel. Log into the admin and click 'Domain' in the top menu.
Click 'Create' and fill in the details for the new domain:

* URL
* Name
* Email (e.g. noreply@NEW_DOMAIN)
* Map coordinates
* Add random logo

Then, go to 'Settings' and 'Administrators'. Click 'Add Administrator' to create a new admin user with Regional Administrator rights and link with the appropriate domain.

Next, go to 'Media' and create a new folder for the new domain and name it the same as the domain URL.

Finally, send credentials, URL login and setup info to site admin (see template).

### Logos

Each domain needs to have 4 logos per language. These are set up by the domain owner.

**Desktop:** This is the main logo

**Mobile:** Shown on mobile screens

**Footer:** In the footer

**Email:** This is used in emails sent to your users

### Enabling Email Sending

Ensure you have configured your email provider to allow emails to be sent from the new site's sending address or domain. How to do this varies depending on the email provider.
