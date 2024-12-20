DROP DATABASE IF exists `Blogpress`;
CREATE DATABASE `Blogpress`;
USE `Blogpress`;
CREATE TABLE authors(
`authorID` int(10) not null auto_increment,
`author_name` varchar(15) not null ,
`email` varchar(15) not null,
`password` varchar(15) not null,
 PRIMARY KEY(`authorID`) 
);
CREATE TABLE articles(
`articleID` int(10) not null auto_increment,
`article_title` varchar(15) not null ,
`article_content` text not null ,
`authorID` int(10) not null,
PRIMARY KEY(`articleID`),
FOREIGN KEY (`authorID`) references authors(authorID)
ON DELETE CASCADE
);
CREATE TABLE comments(
`commentID` int(10) not null auto_increment,
`visitor_name` varchar(15) not null,
`comment_content` varchar(200) not null,
`articleID` int(10) not null,
PRIMARY KEY (`commentID`),
FOREIGN KEY (`articleID`) REFERENCES articles(articleID)
ON DELETE CASCADE
);
CREATE TABLE statistiques(
`statisticID` int(10) not null auto_increment,
`articleID` int(10) not null,
`comment_count` int(8) ,
`views` int(8) DEFAULT 0,
`likes` int(8) default 0,
PRIMARY KEY (`statisticID`),
FOREIGN KEY (`articleID`) REFERENCES articles(articleID)
ON DELETE CASCADE
);