<?php
if(isset($_POST["submit"])){
    $target_dir = "../img/gallery/";
    $target_file = $target_dir . basename($_FILES["image_upload"]["name"]);
    $uploadOk = 1;
    $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
    // Check if image file is a actual image or fake image
    if(isset($_POST["image_upload"])) {
        $check = getimagesize($_FILES["image_upload"]["tmp_name"]);
        if($check !== false) {
            echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
    }
    // Check if file already exists
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }
    // Check file size
    if ($_FILES["fileToUpload"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["image_upload"]["tmp_name"], $target_file)) {
            echo "The file ". $target_file. " has been uploaded.";
            $dbh = new sqlite3('../../main.db');
            $prepare = $dbh->prepare("INSERT INTO gallery(username, image) VALUES(:username, :image)");
            $prepare->bindParam(':username', $_POST["username"]);
            $image_path = $SERVER['DOCUMENT_ROOT'].'/img/gallery/' . basename( $_FILES["image_upload"]["name"]);
            $prepare->bindParam(':image', $image_path);
            $result = $prepare->execute();
            if(!$result){
                echo $dbh->lastErrorMsg();
            }
            $dbh->close();
            header("location: ../gallery.php");
            exit();
            break;
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>
