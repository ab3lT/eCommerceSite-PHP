<?php
require_once('header.php');
?>

<?php
$error_message = '';
$success_message = '';

$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row) {
    $banner_registration = $row['banner_registration'];
}
?>

<?php
if (isset($_POST['form1'])) {

    $valid = 1;

    if (empty($_POST['cust_name'])) {
        $valid = 0;
        $error_message .= LANG_VALUE_123 . "<br>";
    }

    if (empty($_POST['cust_email'])) {
        $valid = 0;
        $error_message .= LANG_VALUE_131 . "<br>";
    } else {
        if (filter_var($_POST['cust_email'], FILTER_VALIDATE_EMAIL) === false) {
            $valid = 0;
            $error_message .= LANG_VALUE_134 . "<br>";
        } else {
            $statement = $pdo->prepare("SELECT * FROM tbl_customer WHERE cust_email=?");
            $statement->execute(array($_POST['cust_email']));
            $total = $statement->rowCount();
            if ($total) {
                $valid = 0;
                $error_message .= LANG_VALUE_147 . "<br>";
            }
        }
    }

    if (empty($_POST['cust_phone'])) {
        $valid = 0;
        $error_message .= LANG_VALUE_124 . "<br>";
    }

    if (empty($_POST['cust_address'])) {
        $valid = 0;
        $error_message .= LANG_VALUE_125 . "<br>";
    }

    if (empty($_POST['cust_country'])) {
        $valid = 0;
        $error_message .= LANG_VALUE_126 . "<br>";
    }

    if (empty($_POST['cust_city'])) {
        $valid = 0;
        $error_message .= LANG_VALUE_127 . "<br>";
    }

    if (empty($_POST['cust_state'])) {
        $valid = 0;
        $error_message .= LANG_VALUE_128 . "<br>";
    }

    if (empty($_POST['cust_zip'])) {
        $valid = 0;
        $error_message .= LANG_VALUE_129 . "<br>";
    }

    if (empty($_POST['cust_password']) || empty($_POST['cust_re_password'])) {
        $valid = 0;
        $error_message .= LANG_VALUE_138 . "<br>";
    }

    if (!empty($_POST['cust_password']) && !empty($_POST['cust_re_password'])) {
        if ($_POST['cust_password'] != $_POST['cust_re_password']) {
            $valid = 0;
            $error_message .= LANG_VALUE_139 . "<br>";
        }
    }

    if ($valid == 1) {

        $token = md5(time());
        $cust_datetime = date('Y-m-d h:i:s');
        $cust_timestamp = time();

        try {
            // saving into the database
            $statement = $pdo->prepare("INSERT INTO tbl_customer (
                                        cust_name,
                                        cust_cname,
                                        cust_email,
                                        cust_phone,
                                        cust_country,
                                        cust_address,
                                        cust_city,
                                        cust_state,
                                        cust_zip,
                                        cust_b_name,
                                        cust_b_cname,
                                        cust_b_phone,
                                        cust_b_country,
                                        cust_b_address,
                                        cust_b_city,
                                        cust_b_state,
                                        cust_b_zip,
                                        cust_s_name,
                                        cust_s_cname,
                                        cust_s_phone,
                                        cust_s_country,
                                        cust_s_address,
                                        cust_s_city,
                                        cust_s_state,
                                        cust_s_zip,
                                        cust_password,
                                        cust_token,
                                        cust_datetime,
                                        cust_timestamp,
                                        cust_status
                                    ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $statement->execute(array(
                htmlspecialchars($_POST['cust_name']),
                htmlspecialchars($_POST['cust_cname']),
                htmlspecialchars($_POST['cust_email']),
                htmlspecialchars($_POST['cust_phone']),
                htmlspecialchars($_POST['cust_country']),
                htmlspecialchars($_POST['cust_address']),
                htmlspecialchars($_POST['cust_city']),
                htmlspecialchars($_POST['cust_state']),
                htmlspecialchars($_POST['cust_zip']),
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                md5($_POST['cust_password']),
                $token,
                $cust_datetime,
                $cust_timestamp,
                1
            ));

            // Send email for confirmation of the account
            $to = $_POST['cust_email'];

            $subject = LANG_VALUE_150;
            $verify_link = BASE_URL . 'verify.php?email=' . urlencode($to) . '&token=' . $token;
            $message = '
                ' . LANG_VALUE_151 . '<br><br>
                <a href="' . $verify_link . '">' . $verify_link . '</a>';

            $headers = "From: noreply@" . parse_url(BASE_URL, PHP_URL_HOST) . "\r\n" .
                "Reply-To: noreply@" . parse_url(BASE_URL, PHP_URL_HOST) . "\r\n" .
                "X-Mailer: PHP/" . phpversion() . "\r\n" .
                "MIME-Version: 1.0\r\n" .
                "Content-Type: text/html; charset=ISO-8859-1\r\n";

            // Sending Email
            // mail($to, $subject, $message, $headers);

            unset($_POST['cust_name']);
            unset($_POST['cust_cname']);
            unset($_POST['cust_email']);
            unset($_POST['cust_phone']);
            unset($_POST['cust_address']);
            unset($_POST['cust_city']);
            unset($_POST['cust_state']);
            unset($_POST['cust_zip']);

            $success_message = LANG_VALUE_152;
        } catch (PDOException $e) {
            $error_message .= $e->getMessage();
        }
    }
}
?>

<div class="page-banner" style="background-color:#444;background-image: url(assets/uploads/<?php echo $banner_registration; ?>);">
    <div class="inner">
        <h1><?php echo LANG_VALUE_16; ?></h1>
    </div>
</div>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="user-content">

                    <form action="" method="post">
                        <?php $csrf->echoInputField(); ?>
                        <div class="row">
                            <div class="col-md-2"></div>
                            <div class="col-md-8">

                                <?php
                                if (!empty($error_message)) {
                                    echo "<div class='error' style='padding: 10px;background:#f1f1f1;margin-bottom:20px;'>" . $error_message . "</div>";
                                }
                                if (!empty($success_message)) {
                                    echo "<div class='success' style='padding: 10px;background:#f1f1f1;margin-bottom:20px;'>" . $success_message . "</div>";
                                }
                                ?>

                                <div class="col-md-6 form-group">
                                    <label for=""><?php echo LANG_VALUE_102; ?> *</label>
                                    <input type="text" class="form-control" name="cust_name" value="<?php echo isset($_POST['cust_name']) ? htmlspecialchars($_POST['cust_name']) : ''; ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for=""><?php echo LANG_VALUE_103; ?></label>
                                    <input type="text" class="form-control" name="cust_cname" value="<?php echo isset($_POST['cust_cname']) ? htmlspecialchars($_POST['cust_cname']) : ''; ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for=""><?php echo LANG_VALUE_94; ?> *</label>
                                    <input type="email" class="form-control" name="cust_email" value="<?php echo isset($_POST['cust_email']) ? htmlspecialchars($_POST['cust_email']) : ''; ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for=""><?php echo LANG_VALUE_104; ?> *</label>
                                    <input type="text" class="form-control" name="cust_phone" value="<?php echo isset($_POST['cust_phone']) ? htmlspecialchars($_POST['cust_phone']) : ''; ?>">
                                </div>
                                <div class="col-md-12 form-group">
                                    <label for=""><?php echo LANG_VALUE_105; ?> *</label>
                                    <textarea name="cust_address" class="form-control" cols="30" rows="10" style="height:70px;"><?php echo isset($_POST['cust_address']) ? htmlspecialchars($_POST['cust_address']) : ''; ?></textarea>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for=""><?php echo LANG_VALUE_106; ?> *</label>
                                    <select name="cust_country" class="form-control select2">
                                        <option value="">Select country</option>
                                        <?php
                                        $statement = $pdo->prepare("SELECT * FROM tbl_country ORDER BY country_name ASC");
                                        $statement->execute();
                                        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($result as $row) {
                                            $selected = isset($_POST['cust_country']) && $_POST['cust_country'] == $row['country_id'] ? 'selected' : '';
                                            echo "<option value='" . htmlspecialchars($row['country_id']) . "' $selected>" . htmlspecialchars($row['country_name']) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-md-6 form-group">
                                    <label for=""><?php echo LANG_VALUE_107; ?> *</label>
                                    <input type="text" class="form-control" name="cust_city" value="<?php echo isset($_POST['cust_city']) ? htmlspecialchars($_POST['cust_city']) : ''; ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for=""><?php echo LANG_VALUE_108; ?> *</label>
                                    <input type="text" class="form-control" name="cust_state" value="<?php echo isset($_POST['cust_state']) ? htmlspecialchars($_POST['cust_state']) : ''; ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for=""><?php echo LANG_VALUE_109; ?> *</label>
                                    <input type="text" class="form-control" name="cust_zip" value="<?php echo isset($_POST['cust_zip']) ? htmlspecialchars($_POST['cust_zip']) : ''; ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for=""><?php echo LANG_VALUE_96; ?> *</label>
                                    <input type="password" class="form-control" name="cust_password">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for=""><?php echo LANG_VALUE_98; ?> *</label>
                                    <input type="password" class="form-control" name="cust_re_password">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for=""></label>
                                    <input type="submit" class="btn btn-danger" value="<?php echo LANG_VALUE_15; ?>" name="form1">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>