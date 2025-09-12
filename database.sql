CREATE DATABASE IF NOT EXISTS school_voting;
USE school_voting;
CREATE TABLE admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);
CREATE TABLE voters (
    voter_id VARCHAR(20) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    has_voted TINYINT(1) DEFAULT 0
);
CREATE TABLE positions (
    position_id INT AUTO_INCREMENT PRIMARY KEY,
    position_name VARCHAR(100) NOT NULL
);
CREATE TABLE candidates (
    candidate_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    position_id INT,
    FOREIGN KEY (position_id) REFERENCES positions(position_id) ON DELETE CASCADE
);
CREATE TABLE votes (
    vote_id INT AUTO_INCREMENT PRIMARY KEY,
    voter_id VARCHAR(20),
    candidate_id INT,
    position_id INT,
    FOREIGN KEY (voter_id) REFERENCES voters(voter_id) ON DELETE CASCADE,
    FOREIGN KEY (candidate_id) REFERENCES candidates(candidate_id) ON DELETE CASCADE,
    FOREIGN KEY (position_id) REFERENCES positions(position_id) ON DELETE CASCADE
);
INSERT INTO admins(username,password) VALUES('admin', PASSWORD('admin123'));
