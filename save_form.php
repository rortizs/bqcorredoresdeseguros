<?php
include_once 'db.php';
if(isset($_POST['submit']))
{
   $name = $_POST['nombre'];
   $lastname = $_POST['apellido'];
   $email = $_POST['correo'];
   $mobile = $_POST['telefono'];
   $comment = $_POST['comentario'];

   $sql = "INSERT INTO clientes (nombre,apellido,email,telefono,comentario)
   VALUES ('$name','$lastname','$email','$mobile', '$comment')";
   if (mysqli_query($conn, $sql)) {
      echo "New record has been added successfully !";
   } else {
      echo "Error: " . $sql . ":-" . mysqli_error($conn);
   }
   mysqli_close($conn);
   header('Location: '.'/?contacto=enviado');
    
}
?>