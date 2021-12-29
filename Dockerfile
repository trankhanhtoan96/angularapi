FROM node:16-buster-slim
WORKDIR /app
COPY . .
RUN npm install
RUN npm run build:ssr
EXPOSE 4000
CMD ["node", "/app/dist/angular/server/main.js"]
