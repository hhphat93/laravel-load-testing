Dockerfile
https://viblo.asia/p/docker-tao-docker-images-tu-dockerfile-3P0lPORvZox

database master slave
https://hackernoon.com/mysql-master-slave-replication-using-docker-3pp3u97

ssl localhost
https://viblo.asia/p/tao-ssl-certificate-authority-cho-https-tren-local-1VgZvpQY5Aw


permission laravel
https://stackoverflow.com/questions/30639174/how-to-set-up-file-permissions-for-laravel
sudo find . -type f -exec chmod 664 {} \;   
sudo find . -type d -exec chmod 755 {} \;
sudo chown -R webuser:webuser .

php
cat /etc/php/8.1/fpm/php.ini

list process
ps aux | grep php

chat realtime: https://viblo.asia/p/viet-ung-dung-chat-realtime-voi-laravel-vuejs-redis-va-socketio-laravel-echo-Qpmle9Q9lrd
    npm install --save laravel-echo
    npm install -g laravel-echo-server

    npx mix watch
    laravel-echo-server start
    php artisan queue:work

#import database employees mysql_master
mysql -uroot -p111 < /opt/test_db-master/employees.sql

docker exec -it mongo bash
mongosh -u root -p example --authenticationDatabase admin

db.test.insertOne(
  {
    title: "The Favourite",
    genres: [ "Drama", "History" ],
    runtime: 121,
    rated: "R",
    year: 2018,
    directors: [ "Yorgos Lanthimos" ],
    cast: [ "Olivia Colman", "Emma Stone", "Rachel Weisz" ],
    type: "movie"
  }
)

http://localhost:8081/
admin:pass