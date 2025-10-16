FROM node:22-bookworm

WORKDIR /usr/src/app

# Copy only package files first
COPY package*.json ./

# Always include devDependencies so vite is available
RUN npm install --include=dev

# Copy rest of the source
COPY . .

EXPOSE 5173

# Run dev server directly
CMD ["npm", "run", "dev", "--", "--host"]
