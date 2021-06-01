<?php
include '../database/dbconnection.php';
include "../database/UserAuth.php";

if(!isset($_REQUEST['download'])) {
    exit();
}

date_default_timezone_set('Asia/Singapore');

error_reporting(0);
function backDb($conn, $database){
	$tables = array();
	$sql = "SHOW TABLES";
	$query = $conn->query($sql);
	while($row = $query->fetch_row()){
		$tables[] = $row[0];
	}
 
 
$outsql = "-- MySQL dump 10.17  Distrib 10.3.16-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: $database
-- ------------------------------------------------------
-- Server version 10.3.16-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `$database`
--

/*!40000 DROP DATABASE IF EXISTS `$database`*/;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `$database` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `$database`;
";
	
	foreach ($tables as $table) {
 
 
	    $sql = "SHOW CREATE TABLE $table";
	    $query = $conn->query($sql);
	    $row = $query->fetch_row();
 
	    $outsql .= "\n\nDROP TABLE IF EXISTS `$table`;\n" . $row[1] . ";\n\n";
 
	    $sql = "SELECT * FROM $table";
	    $query = $conn->query($sql);
 
	    $columnCount = $query->field_count;
 
 
	    for ($i = 0; $i < $columnCount; $i ++) {
	        while ($row = $query->fetch_row()) {
	            $outsql .= "INSERT INTO $table VALUES(";
	            for ($j = 0; $j < $columnCount; $j ++) {
	                $row[$j] = $row[$j];
 
	                if (isset($row[$j])) {
	                    $outsql .= '"' . $row[$j] . '"';
	                } else {
	                    $outsql .= '""';
	                }
	                if ($j < ($columnCount - 1)) {
	                    $outsql .= ',';
	                }
	            }
	            $outsql .= ");\n";
	        }
	    }
 
	    $outsql .= "\n"; 
	}
 
 
    $backup_file_name = $database . (date("_Y-m-d_H-i-s")) . '.sql';
    $fileHandler = fopen($backup_file_name, 'w+');
    fwrite($fileHandler, $outsql);
    fclose($fileHandler);
 
 
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . basename($backup_file_name));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($backup_file_name));
    ob_clean();
    flush();
    readfile($backup_file_name);
    exec('rm ' . $backup_file_name);
 
 	$file_path = dirname(__FILE__, 1)."\\";
	$fullpath  = $file_path.$backup_file_name;
	$fullpath  = str_replace("\\", "/", $fullpath);
	unlink($fullpath);
}

// Initiating the backup database function
backDb($mysqli, $database);
?>