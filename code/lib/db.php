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
function get_article_list($dbconn){
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
		authors ON articles.author=authors.id
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
		password='".$_POST['password']."'
		LIMIT 1";
	return run_query($dbconn, $query);
}

	
?>
