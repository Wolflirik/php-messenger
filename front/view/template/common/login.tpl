<?php echo $header; ?>
<div class="form-signin-wrap my-auto">
    <form class="form-signin" action="<?php echo $domain; ?>/login" enctype="multipart/form-data" method="post">
        <header class="form-header p-4">
            <div class="d-flex align-items-end justify-content-center mb-1 h3 fw-normal"><span class="auth-text">Messenger</span></div>
            <div class="d-flex align-items-end justify-content-center fw-normal auth-text">
                <b class="auth-page-selected">Авторизация</b>&nbsp;/&nbsp; 
                <a href="<?php echo $domain; ?>/register" class="text-info">Регистрация</a>
            </div>
        </header>
        <main class="form-main p-4">
            <div class="form-label-group">
                <label for="login">Никнейм\Email</label>
                <input type="text" name="login" id="login" class="form-control<?php if($error){ ?> is-invalid<?php } ?>" required autofocus>
            </div>
            <div class="form-label-group">
                <label for="password">Пароль</label>
                <input type="password" name="password" id="password" class="form-control<?php if($error){ ?> is-invalid<?php } ?>" required>
            </div>
            <button class="btn btn-md btn-info btn-group-in btn-block" type="submit"><svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="icon"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg> Войти</button>
        </main>
    </form>
</div>
<?php echo $footer; ?>