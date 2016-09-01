#Lets drop and then create table favourites

DROP TABLE IF EXISTS favourites;

CREATE TABLE IF NOT EXISTS favourites
(favourite_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
title VARCHAR(100) NOT NULL,
videoid VARCHAR(20) NOT NULL
);


