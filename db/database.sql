CREATE DATABASE medtime CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE medtime;

CREATE TABLE State(
	name varchar(250) NOT NULL,
	PRIMARY KEY (name)
) ENGINE=INNODB;

CREATE TABLE City(
	id int NOT NULL AUTO_INCREMENT,
	state_name varchar(250) NOT NULL,
	PRIMARY KEY (id),
	FOREIGN KEY (state_name)
		REFERENCES State(name)
		ON DELETE CASCADE
) ENGINE=INNODB;

CREATE TABLE User(
	id int NOT NULL AUTO_INCREMENT,
	email varchar(255) NOT NULL,
	role varchar(255) NOT NULL,
	password varchar(255) NOT NULL,
	PRIMARY KEY (id)
) ENGINE=INNODB;

CREATE TABLE Admin(
	id int NOT NULL,
	user_id int NOT NULL,
	updated_at timestamp,
	PRIMARY KEY (id),
	FOREIGN KEY (user_id)
		REFERENCES User(id)
		ON DELETE CASCADE
) ENGINE=INNODB;

CREATE TABLE Doctor(
	ci varchar(12) NOT NULL,
	user_id int NOT NULL,
	firstname varchar(50) NOT NULL,
	lastname varchar(50) NOT NULL,
	starts_at time NOT NULL,
	ends_at time NOT NULL,
	cost float(4,2) NOT NULL,
	PRIMARY KEY (ci),
	FOREIGN KEY (user_id)
		REFERENCES User(id)
		ON DELETE CASCADE
) ENGINE=INNODB;

CREATE TABLE Patient(
	ci varchar(12) NOT NULL,
	user_id int NOT NULL,
	city_id int NOT NULL,
	firstname varchar(50) NOT NULL,
	lastname varchar(50) NOT NULL,
	PRIMARY KEY (ci),
	FOREIGN KEY (user_id)
		REFERENCES User(id)
		ON DELETE CASCADE,
	FOREIGN KEY (city_id)
		REFERENCES City(id)
) ENGINE=INNODB;

CREATE TABLE Phone(
	number varchar(15) NOT NULL,
	user_id int NOT NULL,
	PRIMARY KEY (number),
	FOREIGN KEY (user_id)
		REFERENCES User(id)
		ON DELETE CASCADE
) ENGINE=INNODB;

CREATE TABLE Speciality(
	name varchar(250) NOT NULL,
	PRIMARY KEY (name)
) ENGINE=INNODB;

CREATE TABLE Specialization(
	id int NOT NULL,
	doctor_ci varchar(12) NOT NULL,
	speciality_name varchar(250) NOT NULL,
	PRIMARY KEY (id),
	FOREIGN KEY (doctor_ci)
		REFERENCES Doctor(ci)
		ON DELETE CASCADE,
	FOREIGN KEY (speciality_name)
		REFERENCES Speciality(name)
		ON DELETE CASCADE
) ENGINE=INNODB;

CREATE TABLE Appointment(
	id int NOT NULL AUTO_INCREMENT,
	doctor_ci varchar(12) NOT NULL,
	patient_ci varchar(12) NOT NULL,
	day date NOT NULL,
	hour time NOT NULL,
	PRIMARY KEY (id),
	FOREIGN KEY (doctor_ci)
		REFERENCES Doctor(ci)
		ON DELETE CASCADE,
	FOREIGN KEY (patient_ci)
		REFERENCES Patient(ci)
		ON DELETE CASCADE
) ENGINE=INNODB;
