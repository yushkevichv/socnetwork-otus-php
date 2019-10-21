<?php


$mysqli = new mysqli("mysql", "default", "secret", "socnetwork");
if ($mysqli->connect_errno) {
    echo "Не удалось подключиться к MySQL: " . $mysqli->connect_error;
}

if($query = $_GET['q']) {
    $dbQuery = "
            select * from 
            (
                select * from 
                (
                    select id, name, last_name, gender, city, TIMESTAMPDIFF(YEAR, birthday, CURDATE()) as age  from users where name like '$query%' order by id asc limit  0, 50
                ) t1
                union 
                select * from 
                (
                    select id, name, last_name, gender, city, TIMESTAMPDIFF(YEAR, birthday, CURDATE()) as age  from users where last_name like '$query%'  order by id asc limit 0, 50
                ) t2
               
            ) tbl order by id asc limit  0, 50";
}
else {
    $dbQuery = "select id, name, last_name, gender, city, TIMESTAMPDIFF(YEAR, birthday, CURDATE()) as age  from users order by id asc limit  0, 50  ";
}

$res = $mysqli->query($dbQuery);
//var_dump($res->fetch_assoc());
//die();


$res->data_seek(0);
?>
<html>
<body>

<table>
    <tr>
        <th>id</th>
        <th>имя</th>
        <th>фамилия</th>
        <th>пол</th>
        <th>город</th>
        <th>возраст</th>
    </tr>
<?php
while ($row = $res->fetch_assoc()) {
    ?>
    <tr>
        <td><?=$row['id']?></td>
        <td><?=$row['name']?></td>
        <td><?=$row['last_name']?></td>
        <td><?=$row['gender']?></td>
        <td><?=$row['city']?></td>
        <td><?=$row['age']?></td>
    </tr>
    <?php
}

$res->free();

?>
</table>
</body>
</html>
