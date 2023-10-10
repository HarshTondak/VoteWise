<?php
require_once("admin/inc/config.php");

// This runs whenever a user logs in...
// This will update the election status automatically from here, so that we don't have to do it manually
$fetchingElections = mysqli_query($db, "SELECT * FROM elections") or die(mysqli_error($db));

while ($data = mysqli_fetch_assoc($fetchingElections)) {
    $starting_date = $data['starting_date'];
    $ending_date = $data['ending_date'];
    $curr_date = date("Y-m-d");
    $election_id = $data['id'];
    $status = $data['status'];

    if ($status == "Active") {
        // Active => Expire after the Ending Date
        $date1 = date_create($curr_date);
        $date2 = date_create($ending_date);

        // date2 - date1
        $diff = date_diff($date1, $date2);

        if ((int) $diff->format("%R%a") < 0) {
            // Update! 
            mysqli_query($db, "UPDATE elections SET status = 'Expired' WHERE id = '" . $election_id . "'") or die(mysqli_error($db));
        }
    } else if ($status == "InActive") {
        // InActive => Active after the Starting Date
        $date1 = date_create($curr_date);
        $date2 = date_create($starting_date);

        // date2 - date1
        $diff = date_diff($date1, $date2);

        if ((int) $diff->format("%R%a") <= 0) {
            // Update! 
            mysqli_query($db, "UPDATE elections SET status = 'Active' WHERE id = '" . $election_id . "'") or die(mysqli_error($db));
        }
    }
}
?>


<!DOCTYPE html>
<html>

<head>
    <title>Login - Online Voting System</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/login.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="container h-100">
        <div class="d-flex justify-content-center h-100">
            <div class="user_card">
                <div class="d-flex justify-content-center">
                    <div class="brand_logo_container">
                        <img src="assets/images/logo.gif" class="brand_logo" alt="Logo">
                    </div>
                </div>

                <?php
                if (isset($_GET['sign-up'])) {
                    ?>
                    <div class="d-flex justify-content-center form_container">
                        <form method="POST">
                            <div class="input-group mb-3">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                </div>
                                <input type="text" name="su_username" class="form-control input_user" placeholder="Username"
                                    required />
                            </div>
                            <div class="input-group mb-2">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                </div>
                                <input type="text" name="su_contact_no" class="form-control input_pass"
                                    placeholder="Contact ID" required />
                            </div>
                            <div class="input-group mb-2">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                </div>
                                <input type="password" name="su_password" class="form-control input_pass"
                                    placeholder="Password" required />
                            </div>
                            <div class="input-group mb-2">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                </div>
                                <input type="password" name="su_retype_password" class="form-control input_pass"
                                    placeholder="Retype Password" required />
                            </div>

                            <div class="d-flex justify-content-center mt-3 login_container">
                                <button type="submit" name="sign_up_btn" class="btn login_btn">Sign Up</button>
                            </div>
                        </form>
                    </div>

                    <div class="mt-4">
                        <div class="d-flex justify-content-center links text-white">
                            Already have an Account? <a href="index.php" class="ml-2 text-white">Sign In</a>
                        </div>
                    </div>
                    <?php
                } else {
                    ?>
                    <div class="d-flex justify-content-center form_container">
                        <form method="POST">
                            <div class="input-group mb-3">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                </div>
                                <input type="text" name="contact_no" class="form-control input_user"
                                    placeholder="Contact ID" required />
                            </div>
                            <div class="input-group mb-2">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                </div>
                                <input type="password" name="password" class="form-control input_pass"
                                    placeholder="Password" required />
                            </div>
                            <div class="d-flex justify-content-center mt-3 login_container">
                                <button type="submit" name="loginBtn" class="btn login_btn">Sign In</button>
                            </div>
                        </form>
                    </div>

                    <div class="mt-4">
                        <div class="d-flex justify-content-center links text-white">
                            Don't have an account? <a href="?sign-up=1" class="ml-2 text-white">Sign Up</a>
                        </div>
                        <div class="d-flex justify-content-center links">
                            <a href="#" class="text-white">Forgot your password?</a>
                        </div>
                    </div>
                    <?php
                }
                ?>

                <!-- Update Messages at login/signup page -->
                <?php
                if (isset($_GET['registered'])) {
                    ?>
                    <span class="bg-white text-success text-center my-3"> Your account has been created successfully!
                    </span>
                    <?php
                } else if (isset($_GET['invalid'])) {
                    ?>
                        <span class="bg-white text-danger text-center my-3"> Passwords mismatched, please try again! </span>
                    <?php
                } else if (isset($_GET['not_registered'])) {
                    ?>
                            <span class="bg-white text-warning text-center my-3"> Sorry, you are not registered! </span>
                    <?php
                } else if (isset($_GET['invalid_access'])) {
                    ?>
                                <span class="bg-white text-danger text-center my-3"> Invalid username or password! </span>
                    <?php
                }
                ?>

            </div>
        </div>
    </div>

    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>

</body>

</html>

<?php
// we are using require_once() instead of include() because include() donot cares if the file
// specified is really included or not, but require returns a fatal error if the file in not inlcuded for any reason.
// require_once() first checks if the file is already included or not
// If included then it won't add it again, while the require() does...
require_once("admin/inc/config.php");

if (isset($_POST['sign_up_btn'])) {

    // mysqli_real_escape_string is used to remove special characters form the query string
    $su_username = mysqli_real_escape_string($db, $_POST['su_username']);
    $su_contact_no = mysqli_real_escape_string($db, $_POST['su_contact_no']);
    // sha1 is used to hash the password
    $su_password = mysqli_real_escape_string($db, sha1($_POST['su_password']));
    $su_retype_password = mysqli_real_escape_string($db, sha1($_POST['su_retype_password']));
    $user_role = "Voter";

    if ($su_password == $su_retype_password) {
        // Insert Query 
        mysqli_query($db, "INSERT INTO users(username, contact_no, password, user_role) VALUES('" . $su_username . "', '" . $su_contact_no . "', '" . $su_password . "', '" . $user_role . "')") or die(mysqli_error($db));
        ?>
        <!-- location.assign is used to update the URL-->
        <script> location.assign("index.php?sign-up=1&registered=1"); </script>
        <?php

    } else {
        ?>
        <!-- For entering different password and retype-password at singup page -->
        <script> location.assign("index.php?sign-up=1&invalid=1"); </script>
        <?php
    }
} else if (isset($_POST['loginBtn'])) {

    // mysqli_real_escape_string is used to remove special characters form the query string
    $contact_no = mysqli_real_escape_string($db, $_POST['contact_no']);
    // sha1 is used to hash the password
    $password = mysqli_real_escape_string($db, sha1($_POST['password']));

    // Query Fetch / SELECT
    $fetchingData = mysqli_query($db, "SELECT * FROM users WHERE contact_no = '" . $contact_no . "'") or die(mysqli_error($db));

    if (mysqli_num_rows($fetchingData) > 0) {

        $data = mysqli_fetch_assoc($fetchingData);

        if ($contact_no == $data['contact_no'] and $password == $data['password']) {

            // CREATED A SESSION
            session_start();
            $_SESSION['user_role'] = $data['user_role'];
            $_SESSION['username'] = $data['username'];
            $_SESSION['user_id'] = $data['id'];

            if ($data['user_role'] == "Admin") {
                $_SESSION['key'] = "AdminKey";
                ?>
                    <!-- location.assign is used to update the URL -->
                    <!-- Move to ADMIN homepage -->
                    <script> location.assign("admin/index.php?homepage=1"); </script>
                <?php
            } else {
                $_SESSION['key'] = "VotersKey";
                ?>
                    <!-- Move to VOTER homepage -->
                    <script> location.assign("voters/index.php"); </script>
                <?php
            }
        } else {
            ?>
                <!-- For entering wrong credentials -->
                <script> location.assign("index.php?invalid_access=1"); </script>
            <?php
        }
    } else {
        ?>
            <!-- For users that are not registered yet -->
            <script> location.assign("index.php?sign-up=1&not_registered=1"); </script>
        <?php
    }
}
?>