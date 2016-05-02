CREATE DATABASE NewHope;

USE NewHope;

CREATE TABLE users (
	fName VARCHAR(30) NOT NULL,
    lName VARCHAR(30) NOT NULL,
    username VARCHAR(50) NOT NULL PRIMARY KEY,
    email VARCHAR(50) NOT NULL,
    passwrd VARCHAR(50) NOT NULL,
    conditionM VARCHAR(50) NOT NULL
);

CREATE TABLE meetings (
	name VARCHAR(50) PRIMARY KEY,
	fecha VARCHAR(50) NOT NULL,
	place VARCHAR(50) NOT NULL,
	conditionM CHAR(1) NOT NULL,
	extrainfo VARCHAR(255)
);

CREATE TABLE comments(
	username VARCHAR(50)NOT NULL,
	commentP VARCHAR(250) NOT NULL PRIMARY KEY
);

CREATE TABLE mymeetings(
	orderId INTEGER PRIMARY KEY,
	username VARCHAR(50),
	mname VARCHAR(50),
	fecha VARCHAR(50),
	place VARCHAR(50),
	extrainfo VARCHAR(50)
);

INSERT INTO items
VALUES
("Mathematica", 300, 10, 
	"Mathematica is a symbolic mathematical computation program, sometimes called a computer algebra program, used in many scientific, engineering, mathematical, and computing fields.",
	"Wolfram Research", "mathematica.png"),
("Scilab",		250, 9, 
	"Scilab is an open source, cross-platform numerical computational package and a high-level, numerically oriented programming language.",
	"Scilab Enterprises", "scilab.png"),
("Matlab",		200, 5,
	"MATLAB (matrix laboratory) is a multi-paradigm numerical computing environment and fourth-generation programming language.",
	"MathWorks", "matlab.png"),
("Sage",		0,	 8,
	"SageMath is a free open-source mathematics software system licensed under the GPL.",
	"The Sage Development Team", "sagemath.png"),
("Multisim",	320, 10, 
	"NI Multisim (formerly MultiSIM) is an electronic schematic capture and simulation program. It includes microcontroller simulation, as well as integrated import and export features to Printed Circuit Board layout software.",
	"National Instruments", "multisim.png"),
("Maple",		150, 0,
	"Maple is a commercial computer algebra system developed and sold commercially by Maplesoft, a software company based in Waterloo, Ontario, Canada.",
	"Waterloo Maple", "maple.png"),
("Labview",		350, 4,
	"LabVIEW is a system-design platform and development environment for a visual programming language from National Instruments.",
	"National Instruments", "labview.png");
