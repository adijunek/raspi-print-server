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

