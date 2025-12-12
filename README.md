How to Run the Project

1. Build and start and end containers
 - docker compose up -d
 - docker compose up --build
 - docker compose down -v

2. Install Laravel dependencies
 - docker compose exec app composer install

3. Install Node/Vue dependencies (inside the same container)
 - docker compose exec app npm install

4. Set up environment
 - cp .env.example .env
 - DB_CONNECTION=mysql
    DB_HOST=mysql
    DB_PORT=3306
    DB_DATABASE=laravel
    DB_USERNAME=laravel
    DB_PASSWORD=root

 - Generate Key 
    - docker compose exec app php artisan key:generate

5. Run migrations
 - docker compose exec app php artisan migrate


6. Start Laravel server
 - docker compose exec app php artisan serve

7. Start Vite (Vue dev server)
 - docker compose exec app npm run dev

Access your app
| Service                   | URL                                            |
| ------------------------- | ---------------------------------------------- |
| **Laravel backend**       | [http://localhost:8000](http://localhost:8000) |
| **Vue (Vite) dev server** | [http://localhost:5173](http://localhost:5173) |


-----
Access mysql in docker
1. Check How Your MySQL Container Is Exposed
 - docker ps
 - Look for the MySQL container. Example output might show:
    - 0.0.0.0:3306->3306/tcp
    - This means:
        Host: 127.0.0.1 (or your server’s IP)
        Port: 3306

2. Confirm MySQL Is Listening
 - docker exec -it <container_name> mysql -u root -p

