FROM node:14
WORKDIR /app
COPY . .
RUN npm cache clean --force
RUN npm install
RUN npm run build:ssr
EXPOSE 4000
CMD ["node", "/app/dist/angular/server/main.js"]
