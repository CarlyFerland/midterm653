CREATE DATABASE IF NOT EXISTS quotesdb;

USE quotesdb;

CREATE TABLE IF NOT EXISTS authors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    author VARCHAR(255) NOT NULL
);


CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS quotes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quote TEXT NOT NULL,
    author_id INT,
    category_id INT,
    FOREIGN KEY (author_id) REFERENCES authors(id),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

INSERT INTO authors (author) VALUES
('Mark Twain'),
('Oscar Wilde'),
('Maya Angelou'),
('J.K. Rowling'),
('William Shakespeare'),
('Stephen Hawking'),
('Carl Sagan'),
('Louis Pasteur'),
('Neil deGrasse Tyson'),
('Isaac Asimov'),    
('Aristotle'); 


INSERT INTO categories (category) VALUES
('Inspirational'),
('Humor'),
('Science'),
('Literature'),
('Motivational');


INSERT INTO quotes (quote, author_id, category_id) VALUES
('The secret of getting ahead is getting started.', 1, 5),
('The only thing necessary for the triumph of evil is for good men to do nothing.', 1, 1),
('If you tell the truth, you don''t have to remember anything.', 1, 1),
('Some cause happiness wherever they go; others whenever they go.', 2, 2),
('We are all in the gutter, but some of us are looking at the stars.', 2, 1),
('Logic will get you from A to Z; imagination will get you everywhere.', 2, 1),
('Success is not final, failure is not fatal: It is the courage to continue that counts.', 3, 5),
('If you want to know what a man''s like, take a good look at how he treats his inferiors, not his equals.', 3, 1),
('Darkness cannot drive out darkness: only light can do that. Hate cannot drive out hate: only love can do that.', 3, 1),
('There is no greater agony than bearing an untold story inside you.', 3, 4),
('To be yourself in a world that is constantly trying to make you something else is the greatest accomplishment.', 3, 5),
('The only way to do great work is to love what you do.', 4, 5),
('It is our choices, Harry, that show what we truly are, far more than our abilities.', 4, 4),
('To live is the rarest thing in the world. Most people exist, that is all.', 5, 5),
('The greatest glory in living lies not in never falling, but in rising every time we fall.', 5, 5),
('We are what we repeatedly do. Excellence, then, is not an act, but a habit.', 5, 5),
('Love all, trust a few, do wrong to none.', 5, 4),
('All the world''s a stage, and all the men and women merely players.', 5, 4),
('Intelligence is the ability to adapt to change.', 6, 3),
('The greatest enemy of knowledge is not ignorance, it is the illusion of knowledge.', 6, 3), 
('Somewhere, something incredible is waiting to be known.', 7, 3), 
('In science, there are no shortcuts to truth.', 7, 3), 
('Science knows no country, because knowledge belongs to humanity, and is the torch which illuminates the world.', 8, 3), 
('The good thing about science is that it''s true whether or not you believe in it.', 9, 3), 
('The saddest aspect of life right now is that science gathers knowledge faster than society gathers wisdom.', 10, 3), 
('The more we know, the more we realize how much we don''t know.', 11, 3); 
