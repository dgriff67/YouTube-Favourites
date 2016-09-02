#Lets drop and then create table tags

DROP TABLE IF EXISTS tags;

CREATE TABLE IF NOT EXISTS tags
(tagid INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
tag VARCHAR(100) NOT NULL
);

#Now lets create some tags

INSERT INTO davidmarkgriffiths_co_uk.tags VALUES
(NULL, "classical"),
(NULL, "beethoven"),
(NULL, "bach");


