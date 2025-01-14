CREATE DATABASE IF NOT EXISTS saw;

USE saw;

CREATE TABLE IF NOT EXISTS User (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(255) NOT NULL,
    lastname VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS Article (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS UserFavorite (
    user_id INT,
    article_id INT,
    PRIMARY KEY (user_id, article_id),
    FOREIGN KEY (user_id) REFERENCES User(id),
    FOREIGN KEY (article_id) REFERENCES Article(id)
);

CREATE TABLE IF NOT EXISTS Comment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    article_id INT,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES User(id),
    FOREIGN KEY (article_id) REFERENCES Article(id)
);

INSERT INTO Article (title, author, content) VALUES
('The Future of AI', 'John Doe', 'Artificial Intelligence is transforming the world.'),
('Climate Change and Its Impact', 'Jane Smith', 'Climate change is one of the biggest challenges of our time.'),
('The Rise of Quantum Computing', 'Alice Johnson', 'Quantum computing is set to revolutionize technology.'),
('Exploring the Universe', 'Bob Brown', 'Space exploration is expanding our understanding of the universe.'),
('Advancements in Renewable Energy', 'Carol White', 'Renewable energy sources are becoming more efficient.'),
('The Evolution of the Internet', 'David Green', 'The internet has drastically changed over the past few decades.');