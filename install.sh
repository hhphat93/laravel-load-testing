#!/bin/bash

docker-compose down -v

# Remove old data volume
sudo rm -rf ./db_master/data/*
sudo rm -rf ./db_slave/data/*
sudo rm -rf ./db_slave_2/data/*

sudo rm -rf ./db_master/log/*
sudo rm -rf ./db_slave/log/*
sudo rm -rf ./db_slave_2/log/*

docker-compose up -d

# Set permission my.cnf for init mysql
sudo chmod 644 ./db_master/conf/mysql_master.cnf 
sudo chmod 644 ./db_slave/conf/mysql_slave.cnf 
sudo chmod 644 ./db_slave_2/conf/mysql_slave.cnf 

until docker exec mysql_master sh -c 'export MYSQL_PWD=111; mysql -u root -e ";"'
do
    echo "Waiting for mysql_master database connection..."
    sleep 4
done


priv_stmt='CREATE USER "mydb_slave_user"@"%" IDENTIFIED BY "mydb_slave_pwd"; GRANT REPLICATION SLAVE ON *.* TO "mydb_slave_user"@"%"; FLUSH PRIVILEGES;'
docker exec mysql_master sh -c "export MYSQL_PWD=111; mysql -u root -e '$priv_stmt'"


until docker-compose exec mysql_slave sh -c 'export MYSQL_PWD=111; mysql -u root -e ";"'
do
    echo "Waiting for mysql_slave database connection..."
    sleep 4
done

until docker-compose exec mysql_slave_2 sh -c 'export MYSQL_PWD=111; mysql -u root -e ";"'
do
    echo "Waiting for mysql_slave_2 database connection..."
    sleep 4
done

MS_STATUS=`docker exec mysql_master sh -c 'export MYSQL_PWD=111; mysql -u root -e "SHOW MASTER STATUS"'`
CURRENT_LOG=`echo $MS_STATUS | awk '{print $6}'`
CURRENT_POS=`echo $MS_STATUS | awk '{print $7}'`

# slave
start_slave_stmt="CHANGE MASTER TO MASTER_HOST='mysql_master',MASTER_USER='mydb_slave_user',MASTER_PASSWORD='mydb_slave_pwd',MASTER_LOG_FILE='$CURRENT_LOG',MASTER_LOG_POS=$CURRENT_POS; START SLAVE;"
start_slave_cmd='export MYSQL_PWD=111; mysql -u root -e "'
start_slave_cmd+="$start_slave_stmt"
start_slave_cmd+='"'

# mysql slave 1
docker exec mysql_slave sh -c "$start_slave_cmd"
docker exec mysql_slave sh -c "export MYSQL_PWD=111; mysql -u root -e 'SHOW SLAVE STATUS \G'"

# mysql slave 2
docker exec mysql_slave_2 sh -c "$start_slave_cmd"
docker exec mysql_slave_2 sh -c "export MYSQL_PWD=111; mysql -u root -e 'SHOW SLAVE STATUS \G'"

echo "Set permission folder log"
sudo chmod -R 777 ./db_master/log/mysql.log
sudo chmod -R 777 ./db_master/log/error.log
sudo chmod -R 777 ./db_master/log/slow.log

sudo chmod -R 777 ./db_slave/log/mysql.log
sudo chmod -R 777 ./db_slave/log/error.log
sudo chmod -R 777 ./db_slave/log/slow.log

sudo chmod -R 777 ./db_slave_2/log/mysql.log
sudo chmod -R 777 ./db_slave_2/log/error.log
sudo chmod -R 777 ./db_slave_2/log/slow.log

sudo chmod -R 777 ./server_loadbalance/log/
sudo chmod -R 777 ./server_ubuntu1/log/
sudo chmod -R 777 ./server_ubuntu2/log/

