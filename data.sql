--password is 'password123'
INSERT INTO authors (username, password, role) VALUES ('admin', crypt('password123', gen_salt('bf')), 'admin');
--password is 'password123'
INSERT INTO authors (username, password) VALUES ('student', crypt('password123', gen_salt('bf')));

INSERT INTO articles (aid, title, author, stub, content) 
VALUES ('cross-site-scripting', 'Cross-site Scripting', 'admin', 'Cross-site scripting (XSS) is a type of computer security vulnerability typically found in web applications.', 'Cross-site scripting (XSS) is a type of computer security vulnerability typically found in web applications. XSS enables attackers to inject client-side scripts into web pages viewed by other users. A cross-site scripting vulnerability may be used by attackers to bypass access controls such as the same-origin policy. Cross-site scripting carried out on websites accounted for roughly 84% of all security vulnerabilities documented by Symantec up until 2007. In 2017, XSS was still considered a major threat vector. XSS effects vary in range from petty nuisance to significant security risk, depending on the sensitivity of the data handled by the vulnerable site and the nature of any security mitigation implemented by the sites owner network.');
INSERT INTO articles (aid, title, author, stub, content) 
VALUES ('sql-injection', 'SQL Injection', 'student', 'SQL injection is a code injection technique, used to attack data-driven applications, in which malicious SQL statements are inserted into an entry field for execution (e.g. to dump the database contents to the attacker).', 'SQL injection is a code injection technique, used to attack data-driven applications, in which malicious SQL statements are inserted into an entry field for execution (e.g. to dump the database contents to the attacker). SQL injection must exploit a security vulnerability in an applications software, for example, when user input is either incorrectly filtered for string literal escape characters embedded in SQL statements or user input is not strongly typed and unexpectedly executed. SQL injection is mostly known as an attack vector for websites but can be used to attack any type of SQL database.

	SQL injection attacks allow attackers to spoof identity, tamper with existing data, cause repudiation issues such as voiding transactions or changing balances, allow the complete disclosure of all data on the system, destroy the data or make it otherwise unavailable, and become administrators of the database server.

	In a 2012 study, it was observed that the average web application received 4 attack campaigns per month, and retailers received twice as many attacks as other industries.');

