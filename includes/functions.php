<?php


//validate and reform input data
function testInput($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
/*return a post data from database as array*/
function postData($id)
{
    global $connection;
    $post = array();
    $postQuery = "SELECT * FROM posts WHERE id = $id ";
    $postData = mysqli_query($connection, $postQuery);
    testQuery($postData);
    if (mysqli_num_rows($postData) > 0) {
        while ($row = mysqli_fetch_assoc($postData)) {
            $post = $row;
        }
    }
    return $post;

}

function testQuery($result)
{
    global $errors;
    global $connection;
    if (!$result) {
        array_push($errors, mysqli_error($connection, 'Query error'));
    }
}


//create comment on special post form
//front side
function createComment()
{
    global $connection;
    global $errors;

    $postId = mysqli_escape_string($connection, $_GET['id']);
    $auhtor = mysqli_escape_string($connection, $_POST['author']);
    $email = mysqli_escape_string($connection, $_POST['email']);
    $content = mysqli_escape_string($connection, $_POST['content']);

    if (empty($email)) {
        array_push($errors, "email is empty");
    }
    if (empty($content)) {
        array_push($errors, "content is empty");
    }
    if (empty($auhtor)) {
        array_push($errors, "author is empty");
    }
    if (empty($errors)) {
        session_start();
        $_SESSION['comment'] = "yes";
        $sql = "INSERT INTO comments(post_id,author,content,date,status,email)  ";
        $sql .= "VALUES($postId, '$auhtor','$content',now(),'unapproved','$email')";


        $createComment = mysqli_query($connection, $sql);

        //selecting post from DB to change comment count

        header('Location: ' . $_SERVER['HTTP_REFERER']);

    }
    return $errors;


}

//show post pages comments
function showComments($id)
{
    global $connection;
    $comments = array();
    $sql = "SELECT * FROM comments WHERE post_id = $id AND status = 'approved' ";
    $postQuery = mysqli_query($connection, $sql);
    if (mysqli_num_rows($postQuery) > 0) {
        while ($row = mysqli_fetch_assoc($postQuery)) {
            $comments[] = $row;
        }
    }
    if (!empty($comments)) {
        return $comments;
    }
}

function userDataByUsername($username)
{
    global $connection;
    $user = array();
    $sql = "SELECT * FROM users WHERE username ='$username'";
    $userQuery = mysqli_query($connection, $sql);
    if (mysqli_num_rows($userQuery)) {
        while ($row = mysqli_fetch_assoc($userQuery)) {
            $user = $row;
        }
    }
    return $user;

}

function registerUser()
{
    global $connection;
    $username = $_POST['username'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'],PASSWORD_DEFAULT);
    $sql = "INSERT INTO users(username,firstname,lastname,email,password,role,image_path) ";
    $sql .= "VALUES('$username','$firstname','$lastname','$email','$password','user','')";

    mysqli_query($connection,$sql);
    echo "user registered successfully!";
}