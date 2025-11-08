<?php
include("dbconfig.php");
if ($conn) {
    echo "✅ Connected successfully to medi2home database!";
} else {
    echo "❌ Connection failed!";
}
?>
