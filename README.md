#HOW TO INSTALL THE HIAWATHA MONITOR
###UNDERSTAND HOW THE HIAWATHA MONITOR WORKS

Read the information at [The Hiawatha Monitor HowTo](https://www.hiawatha-webserver.org/howto/monitor)
to understand what the Hiawatha Monitor is and how it works.

###CONFIGURE YOUR WEBSERVER

Use the following Hiawatha configuration for the Hiawatha Monitor website.

On modern Linux distributions change `/var/www/...` to `/srv/http/...`.

```conf
UrlToolkit {
  ToolkitID = monitor
  RequestURI isfile Return
  Match ^/(css|images|js)/ Return
  Match ^/(favicon.ico|robots.txt)$ Return
  Match [^?]*(\?.*)? Rewrite /index.php$1
}

VirtualHost {
  Hostname = monitor.domain.com
  WebsiteRoot = /var/www/monitor/public
  StartFile = index.php
  AccessLogfile = /var/www/monitor/logfiles/access.log
  ErrorLogfile = /var/www/monitor/logfiles/error.log
  ExecuteCGI = yes
  # UseFastCGI = PHP5 #Uncomment if you use PHP as a FastCGI daemon (chage to PHP7 if needed)
  TimeForCGI = 15
  UseToolkit = monitor
}

```

###CONFIGURE PHP

The Hiawatha Monitor requires the following PHP modules:

*   **PHP7:**
    *   `php-pgsql`
    *   `php-xsl`


*   **PHP5:**
    *   `php-mysql`
    *   `php-xsl`

#####Use the following PHP settings:

*   `allow_url_include = Off`
*   `cgi.fix_pathinfo = 0 ("0" when using FastCGI PHP), ("1" otherwise)`
*   `date.timezone = <your timezone>` [List of Supported Timezones](https://secure.php.net/manual/en/timezones.php)
*   `magic_quotes_gpc = Off`
*   `register_globals = Off`

###CONFIGURE YOUR DATABASE

Open the website in your browser and follow the instructions on your screen.
In case of an error, add `/setup` to the URL.

###CONFIGURE CRON DAEMON
Use the following crontab settings to fetch the information from the
webservers and to send the daily reports:

```conf
  */5 * * * * /path/to/monitor/website/database/fetch_webserver_logs
  0   0 * * * /path/to/monitor/website/database/delete_old_logs
  59 23 * * * /path/to/monitor/website/database/send_reports
```

###CONFIGURE systemd Timers
Copy the files from the systemd_timers folder to `/etc/systemd/system/`.
Afterwards remove the systemd_timers folder.

Then enable the Timers with:

```sh
sudo systemctl enable hiawatha-monitor_delete_old_logs.timer
hiawatha-monitor_fetch_webserver_logs.timer hiawatha-monitor_send_reports.timer
```

###USING THE HIAWATHA MONITOR
Login with username **admin** and password **monitor** and start adding
webservers in the Webserver Administration page.
Add `MonitorServer = <IP of monitor server>` to the configuration file of your
Hiawatha webservers.

Don't forget to change the **admin** password and to check out the settings in
the Settings administration page. When you're done testing,
set `DEBUG_MODE = no` in `/.../settings/website.conf` and remove the setup
module from `/.../settings/public_pages.conf`.
