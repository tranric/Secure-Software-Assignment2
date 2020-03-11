CREATE TYPE author_role AS ENUM ('admin', 'user');

CREATE TABLE authors (
	id SERIAL,
	created_on TIMESTAMP DEFAULT NOW(),
	username TEXT NOT NULL UNIQUE,
	password TEXT NOT NULL,
	role author_role NOT NULL DEFAULT 'user',
	PRIMARY KEY (id) 
);

CREATE TABLE articles (
	id SERIAL,
	created_on TIMESTAMP DEFAULT NOW(),
	aid TEXT NOT NULL UNIQUE,
	title TEXT NOT NULL UNIQUE,
	author TEXT NOT NULL,
	stub TEXT,
	content TEXT,
	PRIMARY KEY (aid),
	FOREIGN KEY (id) REFERENCES authors (id) 
);

CREATE TABLE action_log (
	username TEXT NOT NULL,
	action TEXT NOT NULL,
	timestamp TIMESTAMP DEFAULT NOW()
);

CREATE EXTENSION pgcrypto;

