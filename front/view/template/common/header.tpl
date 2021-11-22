<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?php echo $title; ?></title>
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" href="<?php echo $domain; ?>/favicon.ico">
    <script src="<?php echo $domain; ?>/front/view/libs/jquery.js"></script>
    <?php foreach($links as $link) { ?>
        <link rel="<?php echo $link['rel']; ?>" href="<?php echo $link['href']; ?>" />
    <?php } ?>
    <?php foreach($styles as $style) { ?>
        <link rel="<?php echo $style['rel']; ?>" href="<?php echo $style['href']; ?>" media="<?php echo $style['media']; ?>"/>
    <?php } ?>
    <link rel="preload" href="<?php echo $domain; ?>/front/view/libs/bootstrap.min.css" as="style">
    <link rel="stylesheet" href="<?php echo $domain; ?>/front/view/libs/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $domain; ?>/front/view/css/main.css"/>
</head>
<body style="background-image:url(https://picsum.photos/1024/720);" class="page">
<?php if($logged){ ?>
    <nav class="navbar flex-md-nowrap p-0 shadow">
        <div class="btn-group">
            <?php if($is_messenger) { ?>
                <button class="btn btn-lg text-white btn-custom d-block d-md-none" data-trigger-open="sidebar" title="Список чатов">
                    <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="icon"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                </button>
                <button class="btn btn-lg text-white btn-custom" data-trigger-open="diff" data-link="<?php echo $domain; ?>/user/update" title="Настройки учетной записи">
                    <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="icon"><line x1="4" y1="21" x2="4" y2="14"></line><line x1="4" y1="10" x2="4" y2="3"></line><line x1="12" y1="21" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="3"></line><line x1="20" y1="21" x2="20" y2="16"></line><line x1="20" y1="12" x2="20" y2="3"></line><line x1="1" y1="14" x2="7" y2="14"></line><line x1="9" y1="8" x2="15" y2="8"></line><line x1="17" y1="16" x2="23" y2="16"></line></svg>
                </button>
                <button class="btn btn-lg text-white btn-custom" data-trigger-open="diff" data-link="<?php echo $domain; ?>/search" title="Добавить беседу">
                    <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="icon"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                </button>
            <?php } else { ?>
                <a href="<?php echo $domain; ?>/" class="btn btn-lg text-white btn-custom" title="Вернуться в мессенджер">
                    <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="icon"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                </a>
            <?php } ?>
        </div>
        <div>
            <span class="d-none d-md-inline-block mr-2 align-middle text-muted">Вы вошли как <?php echo $full_name; ?></span>
            <a href="<?php echo $domain; ?>/logout" class="btn btn-lg text-danger btn-custom" title="Выйти из системы">
                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="icon"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
            </a>
        </div>
    </nav>
<?php } ?>
<div class="container-fluid d-flex flex-column">
