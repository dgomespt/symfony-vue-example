# Use an official Node.js LTS (Long Term Support) image as the base image
FROM node:lts

# Set the working directory in the container
WORKDIR /usr/src/app

# Install Vue CLI globally
RUN npm install -g @vue/cli

# Expose port for development server
EXPOSE 8080
EXPOSE 5173

# Command to run your container
CMD ["vue", "ui", "--headless", "--port", "8080"]
