CREATE SCHEMA IF NOT EXISTS blog CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
USE blog;

DROP TABLE IF EXISTS posts;

CREATE TABLE IF NOT EXISTS posts (
    id      INT AUTO_INCREMENT PRIMARY KEY,
    title   VARCHAR(50) NOT NULL,
    content TEXT NOT NULL
);

INSERT INTO posts (title, content)
VALUES ('PHP and Password Security', 'Handling passwords in PHP is a critical aspect of web application development. It’s essential never to store user passwords as plain text, but always in a secure, hashed form. Modern PHP provides built-in methods to safely manage passwords, ensuring that user data remains protected even if the server is compromised. This approach not only enhances security but also builds trust with users, showing that their sensitive information is being handled responsibly.');
INSERT INTO posts (title, content)
VALUES ('PHP and Cookies', 'Using cookies in PHP is a simple and effective way to personalize the user experience. With a cookie, you can remember user preferences, such as a chosen theme or layout, so that the website appears customized on their next visit. This allows for state persistence without the need for a database and makes the website feel more responsive and user-friendly, improving overall usability while keeping implementation straightforward.');