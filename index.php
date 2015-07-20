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



require_once 'PHPExcel.php'; //подключаем наш фреймворк
$records=array(); // инициализируем массив
$mark=array();
$uniqmark=array();
$inputFileType = PHPExcel_IOFactory::identify($_FILES["filename"]["tmp_name"]);  // узнаем тип файла, excel может хранить файлы в разных форматах, xls, xlsx и другие
$objReader = PHPExcel_IOFactory::createReader($inputFileType); // создаем объект для чтения файла
$objPHPExcel = $objReader->load($_FILES["filename"]["tmp_name"]); // загружаем данные файла в объект
$records = $objPHPExcel->getActiveSheet()->toArray(); // выгружаем данные из объекта в массив

$dsn->query("DROP TABLE IF EXISTS markID");
$dsn->query("DROP TABLE IF EXISTS partID");
$dsn->query("DROP TABLE IF EXISTS price_list");

$dsn->query("CREATE TABLE `markID`(

`id` INT(4) NOT NULL AUTO_INCREMENT,
`name` TEXT(125) NOT NULL,
PRIMARY KEY(`id`)
)");

$dsn->query("CREATE TABLE `partID`(

`id` INT(4) NOT NULL AUTO_INCREMENT,
`name` TEXT(125) NOT NULL,
PRIMARY KEY(`id`)
)" );

$dsn->query("CREATE TABLE `price_list`(
`id_mark` INT(4) NOT NULL INDEX,
`model` VARCHAR(125) NOT NULL,
`year` VARCHAR(125) NOT NULL,
`id_part` INT(4) NOT NULL INDEX,
`price` DOUBLE
)");

for ($k=3;$k<9;$k++){
    $dsn->query("INSERT INTO partID (name) 
        VALUES ('".$records[0][$k]."')");  
}
unset($records[0]);
$mark=array_column($records,0);

$uniqmark=array_unique($mark);
foreach ($uniqmark as $name){
    $dsn->query("INSERT INTO markID (name) 
        VALUES ('".$name."')" );
}






/*foreach($records as $ar_colls){
$fio = $ar_colls[3];
$city = $ar_colls[4];
$year = $ar_colls[5];
$year1 = $ar_colls[6];
$year2 = $ar_colls[7];
$year3 = $ar_colls[8];

echo $fio , $city , $year,$year1,$year2,$year3.'<br />';
}*/
























/*
require_once 'Excel/reader.php';
$data = new Spreadsheet_Excel_Reader();
$data->read($_FILES["filename"]["name"]);

if(copy($_FILES["filename"]["tmp_name"],$_FILES["filename"]["name"])){
    
echo("Файл "."<b>".$_FILES["filename"]["name"]."</b>"." успешно загружен!<br>");



$getdata = $data->sheets[0];

$id=1;
for ($x = 2; $x <= count($data->sheets[0]["cells"]); $x++) {
    $name = $getdata["cells"][$x][1];
    $model = $getdata["cells"][$x][2];
    $year = $getdata["cells"][$x][3];
    $fname = $getdata["cells"][$x+1][1];
    if($fname!=$name){
    $fname = $name;
    $dsn->query("INSERT INTO markID (name) 
        VALUES ('".$name."')" );

    }
    for ($i = 1; $i <= 6; $i++) {
        $price = $getdata["cells"][$x][$i+3];
    $dsn->query("INSERT INTO price_list (id_mark,model,year,id_part,price) 
        VALUES (' ". $id ." ',' ". $model ." ',' ". $year . "','" . $i ." ','" . $price . "')" );
        }
    $id++;
}

for ($y = 4; $y <=9; $y++) {
    $partname = $getdata["cells"][1][$y];
    $qslqerry=("INSERT INTO partID (name) 
        VALUES ('".$partname."')");
    $dsn->query($qslqerry);
    }
    }
    else{
        echo 'Ошибка загрузки файла<br>';
        exit;
    }

*/


?>
