#Download base image ubuntu 20.04
FROM node:21-alpine3.18
 
RUN mkdir -p /var/www/node_modules && chown -R node:node /var/www

WORKDIR /var/www

COPY package*.json ./

USER node

RUN npm install

COPY --chown=node:node . .

EXPOSE 3000

CMD [ "npm", "start" ]
