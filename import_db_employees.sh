#!/bin/bash

docker exec mysql_master sh -c "cd /opt/test_db-master && mysql -uroot -p111 < employees.sql"
