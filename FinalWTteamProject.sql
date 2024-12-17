DROP DATABASE IF EXISTS cc2026;

CREATE DATABASE CC2026;

USE CC2026;

-- Table for Majors
CREATE TABLE Major (
    majorId INT AUTO_INCREMENT PRIMARY KEY,
    majorName VARCHAR(255) NOT NULL,
    description TEXT
);

-- Table for Users
CREATE TABLE users (
    userID INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    gender ENUM('male', 'female'),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    role ENUM('Student', 'Admin'),
    majorId INT,
    profile_image VARCHAR(255) DEFAULT 'default.png',
    interests TEXT,
    skills TEXT,
    FOREIGN KEY(majorId) REFERENCES Major(majorId) ON DELETE SET NULL
);

-- Table for Group Information
CREATE TABLE groupInfo (
    groupID INT AUTO_INCREMENT PRIMARY KEY,
    groupName VARCHAR(50),
    numLimit INT,
    meetingTimes VARCHAR(50),
    groupDescription TEXT,
    dateCreated DATETIME
);

-- Table for Tutor Profiles
CREATE TABLE tutorProfile (
    tutorID INT PRIMARY KEY,
    skills VARCHAR(250),
    FOREIGN KEY(tutorID) REFERENCES users(userID) ON DELETE CASCADE
);

-- Table for Courses
CREATE TABLE Courses (
    courseId INT AUTO_INCREMENT PRIMARY KEY,
    courseCode VARCHAR(50) NOT NULL,
    courseName VARCHAR(255) NOT NULL,
    credits INT,
    majorId INT,
    FOREIGN KEY(majorId) REFERENCES Major(majorId) ON DELETE CASCADE
);

-- Table for Events
CREATE TABLE createEvent (
    eventID INT AUTO_INCREMENT PRIMARY KEY,
    hostedBy INT,
    eventName VARCHAR(50),
    duration VARCHAR(50),
    purpose VARCHAR(255),
    location VARCHAR(255),
    dateScheduled DATETIME,
    membertype ENUM('student', 'tutor'),
    FOREIGN KEY(hostedBy) REFERENCES users(userID) ON DELETE CASCADE
);

-- Table for Group Memberships
CREATE TABLE isAmember (
    Gno INT AUTO_INCREMENT PRIMARY KEY,
    groupId INT,
    memberId INT,
    groupRole VARCHAR(20),
    membertype ENUM('student', 'tutor'),
    FOREIGN KEY(groupId) REFERENCES groupInfo(groupID) ON DELETE CASCADE,
    FOREIGN KEY(memberId) REFERENCES users(userID) ON DELETE CASCADE
);

-- Table for Tutors Teaching Courses
CREATE TABLE tutorTeaches (
    Cno INT AUTO_INCREMENT PRIMARY KEY,
    tutorId INT,
    courseId INT,
    FOREIGN KEY(tutorId) REFERENCES tutorProfile(tutorID) ON DELETE CASCADE,
    FOREIGN KEY(courseId) REFERENCES Courses(courseId) ON DELETE CASCADE
);

-- Example Query to Verify Table
SELECT * FROM users;
