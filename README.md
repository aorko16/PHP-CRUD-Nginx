
PHP CRUD app (single file) ---Project

php-crud-docker/
├── html/
│   └── index.php          # PHP CRUD app (single file)
├── Dockerfile
├── default.conf
└── docker-compose.yml
-------------------------------------------------------------------------------------------------------
Step -1.  Dockerfile

FROM php:8.2-fpm

# Install mysqli
RUN docker-php-ext-install mysqli

# Install nginx
RUN apt-get update && apt-get install -y nginx

# Copy app
COPY ./html /var/www/html

# Copy nginx config
COPY ./default.conf /etc/nginx/sites-available/default

EXPOSE 80

CMD service nginx start && php-fpm

-------------------------------------------------------------------------------------------------------------
Step -2.  default.conf

server {
    listen 80;
    root /var/www/html;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}

-------------------------------------------------------------------------------------------------------------
Step -3.  docker-compose.yml
version: '3.9'

services:
  web:
    build: .
    container_name: php-web
    ports:
      - "8080:80"               # Host 8080 -> Container 80
    volumes:
      - ./html:/usr/share/nginx/html:rw
    depends_on:
      - db
    networks:
      - app-net
    restart: unless-stopped

  db:
    image: mysql:8.0
    container_name: mysql-db
    restart: unless-stopped
    environment:
      MYSQL_ROOT_PASSWORD: root123
      MYSQL_DATABASE: mydb
      MYSQL_USER: user1
      MYSQL_PASSWORD: pass123
    volumes:
      - db-data:/var/lib/mysql
    networks:
      - app-net

networks:
  app-net:

volumes:
  db-data:

----------------------------------------------------------------------------------------------------------------
Step -4.  html/index.php (Single-file CRUD app)
<?php
$host = "db";
$user = "user1";
$pass = "pass123";
$db   = "mydb";

$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_error) die("Connection failed: " . $mysqli->connect_error);

// Create
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mysqli->query("INSERT INTO users (name, email) VALUES ('$name', '$email')");
}

// Update
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mysqli->query("UPDATE users SET name='$name', email='$email' WHERE id=$id");
}

// Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $mysqli->query("DELETE FROM users WHERE id=$id");
}

// Fetch all users
$result = $mysqli->query("SELECT * FROM users");
?>

<h2>PHP CRUD Application</h2>

<?php if(isset($_GET['edit'])):
$id = $_GET['edit'];
$row = $mysqli->query("SELECT * FROM users WHERE id=$id")->fetch_assoc();
?>
<form method="post">
    <input type="hidden" name="id" value="<?= $row['id'] ?>">
    Name: <input type="text" name="name" value="<?= $row['name'] ?>" required>
    Email: <input type="email" name="email" value="<?= $row['email'] ?>" required>
    <input type="submit" name="update" value="Update User">
</form>
<a href="index.php">Back</a>
<?php else: ?>
<form method="post">
    Name: <input type="text" name="name" required>
    Email: <input type="email" name="email" required>
    <input type="submit" name="add" value="Add User">
</form>
<?php endif; ?>

<table border="1" cellpadding="5" cellspacing="0">
<tr><th>ID</th><th>Name</th><th>Email</th><th>Actions</th></tr>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= $row['name'] ?></td>
    <td><?= $row['email'] ?></td>
    <td>
        <a href="?edit=<?= $row['id'] ?>">Edit</a>
        <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this user?')">Delete</a>
    </td>
</tr>
<?php endwhile; ?>
</table>

---------------------------------------------------------------------------------------------------------------
# Run Docker Image 
docker ps
docker-compose logs


Step -5.  MySQL Table login --- password: pass123
docker exec -it mysql-db mysql -u user1 -p

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL
);

Step -6. AWS Security Group (MOST IMPORTANT)
# Server firewall check (Ubuntu) 
sudo ufw status 
docker exec -it php-web sh
ps aux | grep nginx
curl http://localhost:8080# PHP-CRUD-Nginx
