# LED webscraper
LED.go.th Web scraper with MAP view

#Setup

1.import Mysql database to Database Server 
 - database1.sql.zip
 - database2.sql.zip

2.upload folder public_html to webserver

3.config database username and password

4.set cron timer as you need 1-60 mins
 - getFectPageQ.CRON.suspend.php.suspend
 - getLinkContent.CRON.php
 - getLinkList.CRON.php
 - getPagelink.CRON.php
 - updateSold.CRON.php
