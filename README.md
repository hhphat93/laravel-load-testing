# Fresh install
bash install.sh
bash import_db_employees.sh

# Access container
docker exec -it lb bash
docker exec -it u1 bash
docker exec -it u2 bash

docker exec -it mysql_master bash
docker exec -it mysql_slave bash
docker exec -it mysql_slave_2 bash

docker exec -it server_redis bash