<?php if (isset($this->allowFile) && $this->allowFile): ?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta property="og:site_name" content="<?= $this->e($data['header']['title']) ?>">
    <meta property="og:url" content="<?= $this->base_url() ?>">
    <meta property="og:image" content="<?= $this->e($data['header']['img']); ?>">
    <meta name="description" content="<?= $this->e($data['header']['desc']); ?>">
    <meta name="author" content="IS TEAM | UTACGROUP-ID">
    <meta name="access-token" content="<?= $this->e(base64_encode($this->RSAPublicKey())) ?>">

    <title><?= $this->e($data['header']['title']) ?></title>
    <link rel="icon" href="<?= $this->e($data['header']['img']); ?>" type="image/png">

    <!-- Custom fonts for this template-->
    <link href="<?= $this->base_url() ?>/assets/vendor/fontawesome-pro/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="<?= $this->base_url() ?>/assets/css/sb-admin-2.css" rel="stylesheet">
    <link href="<?= $this->base_url() ?>/assets/css/style.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

    <div class="container">

<?php endif; ?>