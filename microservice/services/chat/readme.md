# camapboingua => node
chown -R camapboingua:camapboingua .

docker exec -it chat-mongo bash

mongosh --port 27017 -u root -p root

use chat

db.createUser(
  {
    user: "chat_user",
    pwd:  "123456", 
    roles: [ 
        { role: "readWrite", db: "chat" },
        { role: "read", db: "reporting" } 
    ]
  }
)