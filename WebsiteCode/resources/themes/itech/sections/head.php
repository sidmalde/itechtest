<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>iTech Test</title>
    <link href="<?php echo asset('css/libs/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?php echo asset('css/libs/sticky-footer.min.css') ?>" rel="stylesheet">
    <link href="<?php echo asset('css/home/iframe.css') ?>" rel="stylesheet">
    <link href="<?php echo asset('css/home/jquery-widget.css') ?>" rel="stylesheet">
    <?php foreach ($_headCss as $url): ?>
        <link rel="stylesheet" href="<?php echo $url ?>">
    <?php endforeach; ?>
    <script src="<?php echo asset('js/libs/jquery.min.js') ?>"></script>
    <script src="<?php echo asset('js/libs/bootstrap.min.js') ?>"></script>
    <script src="<?php echo asset('js/jquery-plugin/tweet.refresher.js') ?>"></script>

</head>

<body>

<?php include __DIR__ . '/navigation.php' ?>