<?php
class PVController
{
    public static function insertPv($user_id)
    {
        // Extract image and intervention_id from the request
        $intervention_id = $_POST['intervention_id'] ?? '';
        $image = $_FILES['image'] ?? null;

        if (empty($intervention_id) || $image === null) {
            http_response_code(400);
            echo json_encode(["message" => "intervention_id and image are required."]);
            return;
        }

        // Check if user_id is who has the intervention
        $intervention = Intervention::get($intervention_id);
        if ($intervention['technicien_id'] != $user_id) {
            http_response_code(403);
            echo json_encode(["message" => "You are not allowed to add a PV to this intervention."]);
            return;
        }

        // Handle file upload
        $imageTmpPath = $image['tmp_name'];
        $imageName = $image['name'];
        $imageSize = $image['size'];
        $imageType = $image['type'];

        // Check image size
        if ($imageSize > 5000000) { // 5MB
            http_response_code(400);
            echo json_encode(["message" => "Image size is too large. Max size is 5MB."]);
            return;
        }

        // Check image type
        $allowedImageTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($imageType, $allowedImageTypes)) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid image type. Only JPEG, JPG, and PNG images are allowed."]);
            return;
        }

        // Generate a unique name for the image
        $imageExtension = pathinfo($imageName, PATHINFO_EXTENSION);
        $uniqueImageName = uniqid('pv_', true) . '.' . $imageExtension;

        // Define the upload directory and destination path
        $uploadFileDir = './pvs/';
        $destPath = $uploadFileDir . $uniqueImageName;

        // Create the upload directory if it does not exist
        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0755, true);
        }

        // Move the file to the desired location
        if (move_uploaded_file($imageTmpPath, $destPath)) {
            // Call the insertPV method to handle the rest of the insertion logic
            $response = PV::insertPV($intervention_id, $uniqueImageName);

            http_response_code(200);
            echo json_encode($response);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Error moving the uploaded file."]);
        }
    }
    public static function getPV($user_id, $role = 'technicien')
    {
        $IDPv = $_GET['IDPv'] ?? '';
        if (empty($IDPv)) {
            http_response_code(400);
            echo json_encode(["message" => "IDPv is required."]);
            return;
        }
        $pv = PV::getPV($IDPv);
        if ($pv && $role == 'technicien') {
            $intervention = Intervention::get($pv['intervention_id']);
            if ($intervention['technicien_id'] != $user_id) {
                http_response_code(403);
                echo json_encode(["message" => "You are not allowed to view this PV."]);
                return;
            }
        }
        return $pv;
    }
    public static function getPVsTec($user_id)
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $fromDate = $data['fromDate'] ?? '';
        $toDate = $data['toDate'] ?? '';
        if (empty($fromDate) || empty($toDate)) {
            http_response_code(400);
            echo json_encode(["message" => "fromDate and toDate are required."]);
            return;
        }
        $pvs = PV::getPVsTec($fromDate, $toDate, $user_id);
        return $pvs;
    }
    public static function getPVs()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $fromDate = $data['fromDate'] ?? '';
        $toDate = $data['toDate'] ?? '';
        if (empty($fromDate) || empty($toDate)) {
            http_response_code(400);
            echo json_encode(["message" => "fromDate and toDate are required."]);
            return;
        }
        $pvs = PV::getPVs($fromDate, $toDate);
        return $pvs;
    }
}
