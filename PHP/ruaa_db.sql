CREATE DATABASE ruaa_db;
USE ruaa_db;

-- User Table
CREATE TABLE User (
    Email VARCHAR(255) PRIMARY KEY,
    Name VARCHAR(100) NOT NULL,
    Password VARCHAR(100) NOT NULL,
    Role VARCHAR(50) NOT NULL
);

-- Event Table
CREATE TABLE Event (
    Title VARCHAR(255) PRIMARY KEY,
    Description TEXT,
    Type VARCHAR(100) NOT NULL,
    Date DATE NOT NULL,
    Location VARCHAR(255) NOT NULL,
    Max_Participants INT CHECK(Max_Participants BETWEEN 1 AND 500),
    Registration_Deadline DATE NOT NULL,
    Banner_Image VARCHAR(255)
);

-- Participant Table
CREATE TABLE ShapeParticipant (
    Email VARCHAR(255),
    Title VARCHAR(255),
    Status VARCHAR(50) NOT NULL,
    PRIMARY KEY (Email, Title),
    FOREIGN KEY (Email) REFERENCES User(Email) ON DELETE CASCADE,
    FOREIGN KEY (Title) REFERENCES Event(Title) ON DELETE CASCADE
);

-- Organizer Table
CREATE TABLE Organizer (
    Email VARCHAR(255),
    Created_Event VARCHAR(255),
    PRIMARY KEY (Email, Created_Event),
    FOREIGN KEY (Email) REFERENCES User(Email) ON DELETE CASCADE,
    FOREIGN KEY (Created_Event) REFERENCES Event(Title) ON DELETE CASCADE
);

-- Team Table
CREATE TABLE Team (
    Team_Name VARCHAR(255) PRIMARY KEY,
    Team_Members INT CHECK(Team_Members BETWEEN 1 AND 10),
    Team_Idea TEXT,
    Max_Members INT CHECK(Max_Members BETWEEN 1 AND 10),
    Status VARCHAR(50) NOT NULL,
    Title VARCHAR(255),
    FOREIGN KEY (Title) REFERENCES Event(Title) ON DELETE CASCADE
);

-- Team Member Table
CREATE TABLE Team_Member (
    LeaderName VARCHAR(100) PRIMARY KEY,
    Team_Name VARCHAR(255),
    FOREIGN KEY (Team_Name) REFERENCES Team(Team_Name) ON DELETE CASCADE
);

-- Registration Table
CREATE TABLE Registration (
    Team_Name VARCHAR(255),
    Idea TEXT,
    Name VARCHAR(100) NOT NULL,
    Email VARCHAR(255),
    PRIMARY KEY (Team_Name, Email),
    FOREIGN KEY (Team_Name) REFERENCES Team(Team_Name) ON DELETE CASCADE,
    FOREIGN KEY (Email) REFERENCES User(Email) ON DELETE CASCADE
);

-- Join Request Table
CREATE TABLE JoinRequest (
    Email VARCHAR(255),
    Team_Name VARCHAR(255),
    PRIMARY KEY (Email, Team_Name),
    FOREIGN KEY (Email) REFERENCES User(Email) ON DELETE CASCADE,
    FOREIGN KEY (Team_Name) REFERENCES Team(Team_Name) ON DELETE CASCADE
);

-- Notification Table
CREATE TABLE Notification (
    Email VARCHAR(255) PRIMARY KEY,
    FOREIGN KEY (Email) REFERENCES User(Email) ON DELETE CASCADE
);

-- Team Invitation Table
CREATE TABLE TeamInvitation (
    Email VARCHAR(255),
    Team_Name VARCHAR(255),
    PRIMARY KEY (Email, Team_Name),
    FOREIGN KEY (Email) REFERENCES User(Email) ON DELETE CASCADE,
    FOREIGN KEY (Team_Name) REFERENCES Team(Team_Name) ON DELETE CASCADE
);
