# Raspberry Pi Print Server with Web Interface


## 1. Install CUPS

```
sudo apt-get install cups
```

edit CUPS config file

```
sudo nano /etc/cups/cupsd.conf
```

Change/add the following lines to the configuration file:

```
# Only listen for connections from the local machine.

#Listen localhost:631

#CHANGED TO LISTEN TO LOCAL LAN

Port 631

# Restrict access to the server...
<Location />
  Order allow,deny
  Allow @Local
</Location>


# Restrict access to the admin pages...
<Location /admin>
  Order allow,deny
  Allow @Local
</Location>


# Restrict access to configuration files...
<Location /admin/conf>
  AuthType Default
  Require user @SYSTEM
  Order allow,deny
  Allow @Local
</Location>
```
Restart CUPS

```
sudo service cups restart
```

Next we add the Pi user to the Ipadmin group. This gives the Raspberry Pi the ability to perform administrative functions of CUPS without necessarily being a super user.

```
sudo usermod -a -G Ipadmin pi
```

Next we need to ensure that CUPS can be connected to on the home network and its also accessible across the entire network.

To get it to allow all connections on the network, run;

```
sudo cupsctl –remote-any
```
After this we then restart cups to effect changes using;
```
sudo /etc/init.d/cups  restart
```

Connect raspberry pi to an usb printer. Use the lpstat(1) command to see a list of available printers:
```
lpstat -p -d
```

Adding your printer to CUPS can be doen through CUPS administration web page at `http://[your-raspi-ip]:631`

## Install Samba
Samba is an interoperability tool that allows for easy communication between windows and linux or unix programs and it will be used to allow our windows based system to communicate with CUPS running on the Raspberry Pi to print.

While cups is being installed, it installs other dependencies like samba, but just in case it wasn’t installed, you can install it by following the procedure below.
```
sudo apt-get install samba
```
Then edit samba configuration file.
```
sudo nano /etc/samba/samba.conf 
```

n the conf file, scroll to the print section and change the; guest ok = no to guest ok = yes
```
guest ok = yes
```

Also under the printer driver section, change the; read only = yes to read only  = no
```
read only  = no
```
With this all done save the file using ctrl+X followed by y and enter.

After saving the file restart samba to effect the changes using;
```
sudo /etc/init.d/samba restart
```
At this point you should be able to print documents remotely from your PC, by adding pi remote printer to your PC.
For mobile devices to be able to use wireless remote printer, you should install 3rd application like Let's Print Droid.
But if you don't want to install 3rd party app, you may continue with following steps:
 
We're going to setup a web server on the Raspberry Pi where remote users could upload documents (images, pdf, ms office documents) to Raspberry Pi and then print them. Users could use PCs or mobile devices that are connected to the same LAN with the Raspberry Pi.



## Install Nginx
```
sudo apt install nginx
```
and restart the server:
```
sudo /etc/init.d/nginx start
```


## Install PHP
```
sudo apt install php-fpm
```

then enable PHP in Nginx

```
cd /etc/nginx
sudo nano sites-enabled/default
```

find the line
```
index index.html index.htm;
```
roughly around line 25 (Press CTRL + C in nano to see the current line number)

Add `index.php` after `index` to look like this:
```
index index.php index.html index.htm;
```

then edit the following section. It should look like this:
```
       location ~ \.php$ {
                include snippets/fastcgi-php.conf;

        # With php-fpm (or other unix sockets):
        fastcgi_pass unix:/var/run/php/php7.0-fpm.sock;
        # With php-cgi (or other tcp sockets):
    #    fastcgi_pass 127.0.0.1:9000;
        }

```

Reload the configuration file:
```
sudo /etc/init.d/nginx reload
```

## Install ImageMagick

ImageMagick is needed for the Pi to be able create thumbnails of any document you upload through web page.

```
sudo apt-get install imagemagick
```

then, in order for imagemagick to be able to convert pdf document, edit `/etc/ImageMagick-7/policy.xml` file.
Find a row that contains:
```
<policy domain="coder" rights="none" pattern="PDF" />
```
wrap  it between <!-- and --> to comment it.

## Install LibreOffice
CUPS does not natively support printing MS Office documents (Doc, Excel, PowerPoint), in order for our Print Server to be able to print MS Office Documents, we're going to install LibreOffice.

```
sudo apt-get install libreoffice
```
If you're using older raspi (or raspi zero) like me, the installation will complain about can not find java home. This is because java Server VM is only supported on ARMv7+ VFP, which we don't have in our old Raspberry Pi.
But we can ignore that since we will only use LibreOffice headlessly to print MS Office documents.

So, now we have everything set up, all we need is codes for Print Server Web Interface.
Clone this repo into `/var/www/html`. Edit PRINTER constant in `config.php'

Now you can acces our Raspberry Print Server at `http://raspberry-ip`

Good Luck!
