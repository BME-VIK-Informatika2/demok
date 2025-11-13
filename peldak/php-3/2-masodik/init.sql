USE info2;

CREATE TABLE IF NOT EXISTS books
(
    id     INT AUTO_INCREMENT PRIMARY KEY,
    author VARCHAR(45)  NOT NULL,
    title  VARCHAR(255) NOT NULL,
    year   INT          NOT NULL,
    genre  VARCHAR(45)  NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

INSERT INTO books (author, title, year, genre)
VALUES ('J.K. Rowling', 'Harry Potter and the Philosopher\'s Stone', 1997, 'Fantasy'),
       ('J.K. Rowling', 'Harry Potter and the Chamber of Secrets', 1998, 'Fantasy'),
       ('J.K. Rowling', 'Harry Potter and the Prisoner of Azkaban', 1999, 'Fantasy'),
       ('J.R.R. Tolkien', 'The Hobbit', 1937, 'Fantasy'),
       ('J.R.R. Tolkien', 'The Lord of the Rings: The Fellowship of the Ring', 1954, 'Fantasy'),
       ('J.R.R. Tolkien', 'The Lord of the Rings: The Two Towers', 1954, 'Fantasy'),
       ('J.R.R. Tolkien', 'The Lord of the Rings: The Return of the King', 1955, 'Fantasy'),
       ('George Orwell', '1984', 1949, 'Dystopian'),
       ('George Orwell', 'Animal Farm', 1945, 'Political Satire'),
       ('Harper Lee', 'To Kill a Mockingbird', 1960, 'Fiction'),
       ('F. Scott Fitzgerald', 'The Great Gatsby', 1925, 'Fiction'),
       ('Aldous Huxley', 'Brave New World', 1932, 'Dystopian'),
       ('Ray Bradbury', 'Fahrenheit 451', 1953, 'Dystopian'),
       ('Isaac Asimov', 'Foundation', 1951, 'Science Fiction'),
       ('Isaac Asimov', 'I, Robot', 1950, 'Science Fiction'),
       ('Frank Herbert', 'Dune', 1965, 'Science Fiction'),
       ('Suzanne Collins', 'The Hunger Games', 2008, 'Dystopian'),
       ('Veronica Roth', 'Divergent', 2011, 'Dystopian'),
       ('George R.R. Martin', 'A Game of Thrones', 1996, 'Fantasy'),
       ('C.S. Lewis', 'The Lion, the Witch and the Wardrobe', 1950, 'Fantasy');
