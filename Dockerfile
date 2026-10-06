FROM node:22-alpine AS build
RUN apk add --no-cache build-base gcc autoconf automake zlib-dev libpng-dev vips-dev
WORKDIR /opt/app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
ENV NODE_ENV=production
RUN npm run build && npm prune --omit=dev

FROM node:22-alpine
RUN apk add --no-cache vips-dev
WORKDIR /opt/app
ENV NODE_ENV=production
COPY --from=build /opt/app ./
RUN mkdir -p public/uploads && chown -R node:node /opt/app/public/uploads
EXPOSE 1337
USER node
CMD ["npm", "run", "start"]
