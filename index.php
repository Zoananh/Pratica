<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Результат загрузки файла</title>
</head>
<body>
<p><b>Обновление прайс-листа</b></p>
<form action="" method="post" enctype="multipart/form-data">
    <b>Выберите файл Excel</b><br />
    <input type="file" name="filename" size="15" /><br /><br />
    <input type="hidden" name="update" value="OK" />
    <input type="submit" value="Загрузить" /><br />
</form>
</body>
</html>
<?php
//header ("Content-Type: text/html; charset=utf-8");

define (DB_DRIVER, "mysql");
define (DB_CHARSET, "UTF-8");
define (DB_HOST, "127.0.0.1");
define (DB_USER, "mysql");
define (DB_PASS, "mysql");
define (DB_NAME, "MyProject");

try {

$dsn = new PDO(DB_DRIVER.":host=".DB_HOST.";dbname=".DB_NAME,DB_USER, DB_PASS);
$dsn->exec("SET CHARACTER SET utf8");
echo 'Соединение с базой установлено<br/>';
}
catch (PDOException $e)
{
echo 'Подключение не удалось: ' . $e->getMessage();
}





require_once 'Excel/reader.php';
$data = new Spreadsheet_Excel_Reader();
$data->read($path.$_FILES["filename"]["name"]);

if(copy($_FILES["filename"]["tmp_name"],$path.$_FILES["filename"]["name"])){
    
echo("Файл "."<b>".$_FILES["filename"]["name"]."</b>"." успешно загружен!<br>");
$dsn->query("DROP TABLE IF EXISTS markID");
$dsn->query("DROP TABLE IF EXISTS partID");
$dsn->query("DROP TABLE IF EXISTS price_list");
/*$stmt=$dsn->prepare('DROP TABLE IF EXISTS :tablename');
$stmt->bindParam(':tablename', $tablename);
$tablename ='markID';
$tablename ='partD';
$tablename ='cost';
$stmt->execute();
*/
$dsn->query("CREATE TABLE `markID`(

`id_mark` INT(4) NOT NULL AUTO_INCREMENT,
`name` TEXT(125) NOT NULL,
PRIMARY KEY(`id_mark`)
)");

$dsn->query("CREATE TABLE `partID`(

`id_part` INT(4) NOT NULL AUTO_INCREMENT,
`name` TEXT(125) NOT NULL,
PRIMARY KEY(`id_part`)
)" );

$dsn->query("CREATE TABLE `price_list`(
`id_mark` INT(4) NOT NULL,
`model` VARCHAR(125) NOT NULL,
`year` VARCHAR(125) NOT NULL,
`id_part` INT(4) NOT NULL,
`price` DOUBLE
)");

$id=1;
for ($x = 2; $x <= count($data->sheets[0]["cells"]); $x++) {
    $name = $data->sheets[0]["cells"][$x][1];
    $model = $data->sheets[0]["cells"][$x][2];
    $year = $data->sheets[0]["cells"][$x][3];
    $fname = $data->sheets[0]["cells"][$x+1][1];
    if($fname!=$name){
    $fname = $name;
    $dsn->query("INSERT INTO markID (name) 
        VALUES ('$name')" );

    }
    for ($i = 1; $i <= 6; $i++) {
        $price = $data->sheets[0]["cells"][$x][$i+3];
    $dsn->query("INSERT INTO price_list (id_mark,model,year,id_part,price) 
        VALUES ('$id','$model','$year','$i','$price')" );
        }
    $id++;
}

for ($y = 4; $y <=9; $y++) {
    $partname = $data->sheets[0]["cells"][1][$y];
    $qslqerry=("INSERT INTO partID (name) 
        VALUES ('$partname')");
    $dsn->query($qslqerry);
    }
    }
    else{
        echo 'Ошибка загрузки файла<br>';
        exit;
    }




?>
