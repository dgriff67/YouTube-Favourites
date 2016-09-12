#Lets drop and then create table favourites

DROP TABLE IF EXISTS favourites;

CREATE TABLE IF NOT EXISTS favourites
(favourite_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
title VARCHAR(100) NOT NULL,
videoid VARCHAR(20) NOT NULL
);

#Now lets create some favourites

INSERT INTO davidmarkgriffiths_co_uk.favourites VALUES
(NULL, "Philip Glass - Act I Prelude (Akhnaten)", "6Ql8TidvZto"),
(NULL, "J.S. Bach Cello Suites No.1-6 BWV 1007-1012", "REu2BcnlD34");


#Lets drop and then create table tags

DROP TABLE IF EXISTS tags;

CREATE TABLE IF NOT EXISTS tags
(tagid INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
tag VARCHAR(100) NOT NULL
);

#Now lets create some tags

INSERT INTO davidmarkgriffiths_co_uk.tags VALUES
(NULL, "glass"),
(NULL, "beethoven"),
(NULL, "bach");

#Lets drop and then create table favourite_tags

DROP TABLE IF EXISTS favourite_tags;

CREATE TABLE favourite_tags
(favourite_tagid INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
tagid_FK INT NOT NULL,
favouriteid_FK INT NOT NULL
);

ALTER TABLE favourite_tags
ADD FOREIGN KEY (tagid_FK)
REFERENCES tags(tagid);

ALTER TABLE favourite_tags
ADD FOREIGN KEY (favouriteid_FK)
REFERENCES favourites(favourite_id);

ALTER TABLE favourite_tags ADD CONSTRAINT favourite_tag UNIQUE(tagid_FK,favouriteid_FK);

#Now lets create some favourite tag pairs

INSERT INTO davidmarkgriffiths_co_uk.favourite_tags VALUES
(NULL, "1", "1");


