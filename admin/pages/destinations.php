<?php
include '../config/db.php';

$result = mysqli_query($conn, "SELECT * FROM destinations");

while($row = mysqli_fetch_assoc($result)){
?>
<tr>
    <td><?php echo $row['destination_name']; ?></td>
    <td>
        <a href="index.php?page=destinations&edit=<?php echo $row['id']; ?>">
            Edit
        </a>

        <a href="actions/delete_destination.php?id=<?php echo $row['id']; ?>">
            Delete
        </a>
    </td>
</tr>
<?php
}
?>
