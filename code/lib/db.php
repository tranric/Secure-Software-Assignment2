<?php

$dbconn = pg_pconnect("host=$pg_host port=$pg_port dbname=$pg_dbname user=$pg_dbuser password=$pg_dbpassword") or die("Could not connect");
if ($debug) {
	echo "host=$pg_host, port=$pg_port, dbname=$pg_dbname, user=$pg_dbuser, password=$pg_dbpassword<br>";
	$stat = pg_connection_status($dbconn);
	if ($stat === PGSQL_CONNECTION_OK) {
		echo 'Connection status ok';
	} else {
		echo 'Connection status bad';
	}    
}

function run_query($dbconn, $query) {
	if ($debug) {
		echo "$query<br>";
	}
	$result = pg_query($dbconn, $query);
	if ($result == False and $debug) {
		echo "Query failed<br>";
	}
	return $result;
}

//Logging Function
function log_user_action($dbconn, $username, $action){
	$query=
		"INSERT INTO
		action_log
		VALUES
		('".$_POST['username']."', '$action', current_timestamp)";
	return run_query($dbconn, $query);
}

//database functions
#function get_article_list($dbconn){
function get_article_list($dbconn, $username){
	#gets role from username
	$role = pg_prepare($dbconn,"","select role from authors where username =$1");
	$role = pg_execute($dbconn,"",array($username));
	
	
	#if admin use same statement showing all
	if ($role =='admin'){
		#$result = pg_prepare($dbconn,"","SELECT 
		$query = "SELECT 
		articles.created_on AS DATE,
		articles.aid as aid,
		articles.title as title,
		authors.username as author,
		articles.stub as stub
		FROM articles INNER JOIN authors ON
		articles.id = authors.id
		ORDER BY date DESC";
		#$result = pg_execute($dbconn,"",array());
		#return $result;
		return run_query($dbconn, $query);
	}
	#else show only where articles were created by said user.
	else {
		#$result = pg_prepare($dbconn,"","select 
		$query = "select 
		articles.created_on as date,
		articles.aid as aid,
		articles.title as title,
		articles.author as author,
		articles.stub as stub
		FROM 
		articles 
		INNER JOIN 
		authors ON articles.id = authors.id
		
		ORDER BY
		date DESC";
		#where authors.role = $1
		#WHERE author.username = '".$_POST['username']."' FROM authors

		#$result = pg_execute($dbconn,"",array($role));
		#return $result;
		return run_query($dbconn, $query);
	}
	
	/*
		$query= 
		"SELECT 
		articles.created_on as date,
		articles.aid as aid,
		articles.title as title,
		authors.username as author,
		articles.stub as stub
		FROM
		articles
		INNER JOIN
		authors ON articles.author=authors.id
		ORDER BY
		date DESC";
return run_query($dbconn, $query);
*/

}

function get_article_list_index_page($dbconn){
		$query= 
		"SELECT 
		articles.created_on as date,
		articles.aid as aid,
		articles.title as title,
		authors.username as author,
		articles.stub as stub
		FROM
		articles
		INNER JOIN
		authors ON articles.id=authors.id
		ORDER BY
		date DESC";
return run_query($dbconn, $query);
}

function get_article($dbconn, $aid) {
	$query= 
		"SELECT 
		articles.created_on as date,
		articles.aid as aid,
		articles.title as title,
		authors.username as author,
		articles.stub as stub,
		articles.content as content
		FROM 
		articles
		INNER JOIN
		authors ON articles.id=authors.id
		WHERE
		aid='".$aid."'
		LIMIT 1";
return run_query($dbconn, $query);
}

function delete_article($dbconn, $aid) {
	$username = $_SESSION['username'];
	log_user_action($dbconn,$username,"article deleted");
	$result = pg_prepare($dbconn,"","DELETE FROM articles WHERE aid=$1");
	$result = pg_execute($dbconn,"",array(htmlspecialchars($aid)));
	return $result;
}

function add_article($dbconn, $title, $content, $author) {
	$username = $_SESSION['username'];
	log_user_action($dbconn,$username,"article created");
	$stub = substr($content, 0, 30);
	$aid = str_replace(" ", "-", strtolower($title));
	$result = pg_prepare($dbconn,"","insert into articles(aid, title, author, stub, content)
		values($1,$2,$3,$4,$5)");
	$result = pg_execute($dbconn,"",array(htmlspecialchars($aid),htmlspecialchars($title),
		htmlspecialchars($author),htmlspecialchars($stub),htmlspecialchars($content)));
	return $result;
}

function update_article($dbconn, $title, $content, $aid) {
	$username = $_SESSION['username'];
	log_user_action($dbconn,$username,"updated article");
	$query=
		"UPDATE articles
		SET 
		title='$title',
		content='$content'
		WHERE
		aid='$aid'";
	return run_query($dbconn, $query);
}

function authenticate_user($dbconn, $username, $password) {
	$query=
		"SELECT
		authors.id as id,
		authors.username as username,
		authors.password as password,
		authors.role as role
		FROM
		authors
		WHERE
		username='".$_POST['username']."'
		AND
		password=crypt('".$_POST['password']."', password)
		LIMIT 1";
	return run_query($dbconn, $query);
}

#added for creation of new users for #4 
function new_user($dbconn,$username,$password){
	#note add functionality for hashed passwords for #5
	$result = pg_prepare($dbconn,"","insert into authors(username,password) values ($1,crypt($2, gen_salt('bf')))");
	$result = pg_execute($dbconn,"",array($username,$password));
	
	$result  = pg_prepare($dbconn,"","select * from authors where username=$1");
	$result  = pg_execute($dbconn,"",array($username));
	
	return $result;
}
?>
