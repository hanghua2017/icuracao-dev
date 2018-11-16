 <?php
$servername = "icuracao.cvgs2dfs92lp.us-west-2.rds.amazonaws.com";
$username = "curacao_master";
$password = "F1S?HR0-bw)3TX{B)m9o}C0mO9q7N&";
$dbname = "curacao_staging";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";


$sql = "SELECT * FROM `url_rewrite` WHERE request_path LIKE '%.'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
    	echo "<pre>";
        echo "id: " . $row['request_path'];
        echo "</pre>";
        $sql1 = "UPDATE url_rewrite SET request_path = REPLACE(request_path,'.','') WHERE `url_rewrite_id` =" .$row['url_rewrite_id']."";
        $result1 = $conn->query($sql1);
        if($result1){
        	echo "success";
        } else {
        	$url_key = str_replace(".","",$row['request_path']);
        	echo $url_key;
        	$sql2 = "DELETE FROM url_rewrite WHERE `request_path` LIKE '%".$url_key."'";
            $result2 = $conn->query($sql2);
        	echo 'updated'; 
        }

    }
} else {
    echo "0 results";
}
$conn->close();
?> 

