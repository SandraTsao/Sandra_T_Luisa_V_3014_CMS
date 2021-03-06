<?php

function addProduct($p_name, $p_img, $p_brand, $p_price, $p_review, $p_des, $p_cat) {

    try {
        $pdo = Database::getInstance()->getConnection();

        $cover          = $p_img;
        $upload_file    = pathinfo($cover['name']);
        $accepted_types = array('gif', 'jpg', 'jpe', 'png', 'jpeg', 'webp');
        if (!in_array($upload_file['extension'], $accepted_types)) {
            throw new Exception('Wrong file type!');
        }
    
        $image_path = '../images/';
    
        $generated_name     = md5($upload_file['filename'] . time());
        $generated_filename = $generated_name . '.' . $upload_file['extension'];
        $targetpath         = $image_path . $generated_filename;
    
        if (!move_uploaded_file($cover['tmp_name'], $targetpath)) {
            throw new Exception('Failed to move uploaded file, check permission!');
        }

        $insert_p_query = 'INSERT INTO tbl_products(p_img,p_name,p_brand,p_price,p_review,p_des)';
        $insert_p_query .= ' VALUES(:img,:name,:brand,:price,:review,:des)';

        $insert_p       = $pdo->prepare($insert_p_query);
        $insert_p_result = $insert_p->execute(
            array(
                ':name'=>$p_name,
                ':img'=>$generated_filename,
                ':brand'=>$p_brand,
                ':price'=>$p_price,
                ':review'=>$p_review,
                ':des'=>$p_des
            )
        );

        $last_uploaded_id = $pdo->lastInsertId();
        if ($insert_p_result && !empty($last_uploaded_id)) {
            $update_cat_query = 'INSERT INTO tbl_p_category(p_id, cat_id) VALUES(:p_id, :cat_id)';
            $update_cat      = $pdo->prepare($update_cat_query);

            $update_cat_result = $update_cat->execute(
                array(
                    ':p_id' => $last_uploaded_id,
                    ':cat_id'  => $p_cat
                )
            );
        }
        redirect_to('index.php?createP=You have created the product sucessfully!');
    } catch (Exception $e) {
        $error = $e->getMessage();
        return $error;
    }
}

function getOneProduct($p_id){
    $pdo = Database::getInstance()->getConnection();

    $get_single_query = 'SELECT * FROM `tbl_products` WHERE p_id =:id';
    $p_get = $pdo->prepare($get_single_query);
    $got_single = $p_get->execute(
        array(
            ':id'=>$p_id
        )
    );

    // echo $p_get->debugDumpParams();
    // exit;

    if($got_single && $p_get->rowCount()){
        return $p_get;
    }else{
        return false;
    }
}

function getCurrentCat($p_id) {
    $pdo = Database::getInstance()->getConnection();
    $get_cat_query = 'SELECT * FROM `tbl_p_category` WHERE p_id =:id';
    $cat_get = $pdo->prepare($get_cat_query);
    $got_single = $cat_get->execute(
        array(
            ':id'=>$p_id
        )
    );

    // echo $p_get->debugDumpParams();
    // exit;

    if($got_single && $cat_get->rowCount()){
        return $cat_get;
    }else{
        return false;
    }
}

function editProduct($p_id, $p_name, $p_img, $p_brand, $p_price, $p_review, $p_des, $p_cat) {
    try {
        $pdo = Database::getInstance()->getConnection();

        $cover          = $p_img;
        $upload_file    = pathinfo($cover['name']);
        $accepted_types = array('gif', 'jpg', 'jpe', 'png', 'jpeg', 'webp');
        if (!in_array($upload_file['extension'], $accepted_types)) {
            throw new Exception('Wrong file type!');
        }
    
        $image_path = '../images/';
    
        $generated_name     = md5($upload_file['filename'] . time());
        $generated_filename = $generated_name . '.' . $upload_file['extension'];
        $targetpath         = $image_path . $generated_filename;
    
        if (!move_uploaded_file($cover['tmp_name'], $targetpath)) {
            throw new Exception('Failed to move uploaded file, check permission!');
        }
    
        $update_p_query = 'UPDATE `tbl_products` SET p_name =:name, p_img =:img, p_brand =:brand, p_price =:price, p_review =:review, p_des =:des WHERE p_id =:id';
        $single_update = $pdo->prepare($update_p_query);
        $updated_single = $single_update->execute(
            array(
                ':name'=>$p_name,
                ':img'=>$generated_filename,
                ':brand'=>$p_brand,
                ':price'=>$p_price,
                ':review'=>$p_review,
                ':des'=>$p_des,
                ':id'=>$p_id
            )
        );
    
        if($updated_single){
            $update_cat_query = 'UPDATE `tbl_p_category` SET cat_id =:cat_id WHERE p_id =:p_id';
            $update_cat       = $pdo->prepare($update_cat_query);
            $update_cat_result = $update_cat->execute(
                array(
                    ':p_id'=>$p_id,
                    ':cat_id'=>$p_cat
                )
            );
            
            redirect_to('index.php?editP=You have edited the product sucessfully!');
            // return '<p>You have updated sucessfully!</p>';
        }else{
            return 'Something went wrong with the update.';
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
        return $error;
    }
}

function editProductNoImg($p_id, $p_name, $p_brand, $p_price, $p_review, $p_des, $p_cat) {
    $pdo = Database::getInstance()->getConnection();

    $update_p_query = 'UPDATE `tbl_products` SET p_name =:name, p_brand =:brand, p_price =:price, p_review =:review, p_des =:des WHERE p_id =:id';
    $single_update = $pdo->prepare($update_p_query);
    $updated_single = $single_update->execute(
        array(
            ':name'=>$p_name,
            ':brand'=>$p_brand,
            ':price'=>$p_price,
            ':review'=>$p_review,
            ':des'=>$p_des,
            ':id'=>$p_id
        )
    );

        // echo $single_update->debugDumpParams();
        // exit;

    if($updated_single){
        $update_cat_query = 'UPDATE `tbl_p_category` SET cat_id =:c_id WHERE p_id =:p_id';
        $update_cat       = $pdo->prepare($update_cat_query);
        $update_cat_result = $update_cat->execute(
            array(
                ':c_id'=>$p_cat,
                ':p_id'=>$p_id
            )
        );
        // echo $update_cat->debugDumpParams();
        // exit;
        redirect_to('index.php?editP=You have edited the product sucessfully!');
        // return '<p>You have updated sucessfully!</p>';
    }else{
        return 'Something went wrong with the update.';
    }
}

function deleteProduct($p_id) {
    $pdo = Database::getInstance()->getConnection();

    //2. Run the proper SQL query that remove the user with user_id = $id
    $delete_p_query = 'DELETE FROM tbl_products WHERE p_id=:id';
    $delete_p_set = $pdo->prepare($delete_p_query);
    $delete_p_result = $delete_p_set->execute(array(
        ':id'=>$p_id
    ));

    //3. If it goes through, redirect to admin_deletep.php
    // otherwise, return false
    if($delete_p_result && $delete_p_set->rowCount() > 0){
        $delete_cat_query = 'DELETE FROM tbl_p_category WHERE p_id=:id';
        $delete_cat       = $pdo->prepare($delete_cat_query);
        $delete_cat_result = $delete_cat->execute(
            array(
                ':p_id' => $p_id
            )
        );
        redirect_to('index.php?deleteP=You have deleted the product sucessfully.');
    }else{
        return false;
    }
}