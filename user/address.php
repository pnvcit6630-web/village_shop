<?php include("../config/db.php"); ?>
<form method="post">
    <textarea name="address"></textarea>
    <button name="save">บันทึก</button>
</form>

<?php
if(isset($_POST['save'])){
    $conn->query("INSERT INTO addresses(user_id,address)
    VALUES('{$_SESSION['user_id']}','{$_POST['address']}')");
}
?>
