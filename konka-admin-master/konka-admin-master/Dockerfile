FROM keymetrics/pm2:8-stretch

MAINTAINER ilovintit <ilovintit@gmail.com>

#部署代码
RUN npm install -g npm
RUN mkdir -p /app
WORKDIR /app
COPY ./package.json /app/
COPY ./package-lock.json /app/
RUN npm install
COPY . /app
ENV NODE_ENV=production
ENV HOST=0.0.0.0
RUN npm run build

EXPOSE 3000

CMD [ "pm2-runtime", "start", "/app/ecosystem.config.js" ]
#pm2-runtime start /app/ecosystem.config.js
