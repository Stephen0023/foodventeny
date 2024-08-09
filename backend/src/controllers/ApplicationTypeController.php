<?php
require_once 'models/ApplicationType.php';

class ApplicationTypeController
{
    private $applicationTypeModel;

    public function __construct()
    {
        $this->applicationTypeModel = new ApplicationType();
    }

    public function getApplicationTypes()
    {
        $types = $this->applicationTypeModel->getAllApplicationTypes();
        echo json_encode($types);
    }

    public function getApplicationType($id)
    {
        $type = $this->applicationTypeModel->getApplicationTypeById($id);
        echo json_encode($type);
    }

    public function createApplicationType($data)
    {
        // Adjust the target directory to point to the correct location
        $target_dir ='/var/www/html/uploads/';

        // Ensure that the uploads directory exists
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }



        $target_file = $target_dir . basename($_FILES["cover_photo"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        

        // Check if image file is an actual image or fake image
        $check = getimagesize($_FILES["cover_photo"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            echo json_encode(['success' => false, 'message' => 'File is not an image.']);
            $uploadOk = 0;
        }

        

        // Check file size (limit to 5MB)
        if ($_FILES["cover_photo"]["size"] > 5000000) {
            echo json_encode(['success' => false, 'message' => 'Sorry, your file is too large.']);
            $uploadOk = 0;
        }

        

        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            echo json_encode(['success' => false, 'message' => 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.']);
            $uploadOk = 0;
        }


        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo json_encode(['success' => false, 'message' => 'Sorry, your file was not uploaded.']);
        } else {

            if (move_uploaded_file($_FILES["cover_photo"]["tmp_name"], $target_file)) {
                // File successfully uploaded, save the data including the file path to the database
                $data['cover_photo'] = '/uploads/' . basename($_FILES["cover_photo"]["name"]);
                $result = $this->applicationTypeModel->createApplicationType($data);
                echo json_encode(['success' => $result]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Sorry, there was an error uploading your file.']);
            }
        }
    }

    public function updateApplicationType($id, $data)
    {
        $result = $this->applicationTypeModel->updateApplicationType($id, $data);
        echo json_encode(['success' => $result]);
    }

    public function deleteApplicationType($id)
    {
        $result = $this->applicationTypeModel->deleteApplicationType($id);
        echo json_encode(['success' => $result]);
    }
}
