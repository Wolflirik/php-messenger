<?php echo $header; ?>
<div class="form-register-wrap my-auto">
    <form class="form-register" action="<?php echo $domain; ?>/register" enctype="multipart/form-data" method="post">
        <header class="form-header p-4">
            <div class="d-flex align-items-end justify-content-center mb-1 h3 fw-normal"><span class="auth-text">Messenger</span></div>
            <div class="d-flex align-items-end justify-content-center fw-normal auth-text">
                <a href="<?php echo $domain; ?>/login" class="text-info">Авторизация</a>&nbsp;/&nbsp;
                <b class="auth-page-selected">Регистрация</b>
            </div>
        </header>
        <main class="form-main p-4">
            <div class="row">
                <div class="col-md-4 form-label-group">
                    <label for="name">Имя</label>
                    <input type="text" name="name" id="name" class="form-control<?php if($error_name){ ?> is-invalid<?php } ?>" value="<?php echo $name?$name:''; ?>" required autofocus>
                    <?php if($error_name) { ?>
                        <div class="invalid-feedback">
                            <?php echo $error_name; ?>
                        </div>
                    <?php } ?>
                </div>
                <div class="col-md-4 form-label-group">
                    <label for="surname">Фамилия</label>
                    <input type="text" name="surname" id="surname" class="form-control<?php if($error_surname){ ?> is-invalid<?php } ?>" value="<?php echo $surname?$surname:''; ?>" required>
                    <?php if($error_surname) { ?>
                        <div class="invalid-feedback">
                            <?php echo $error_surname; ?>
                        </div>
                    <?php } ?>
                </div>
                <div class="col-md-4 form-label-group">
                    <label for="patronymic">Отчество</label>
                    <input type="text" name="patronymic" id="patronymic" class="form-control<?php if($error_patronymic){ ?> is-invalid<?php } ?>" value="<?php echo $patronymic?$patronymic:''; ?>" required>
                    <?php if($error_patronymic) { ?>
                        <div class="invalid-feedback">
                            <?php echo $error_patronymic; ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 form-label-group">
                    <label for="nickname">Никнейм</label>
                    <input type="text" name="nickname" id="nickname" class="form-control<?php if($error_nickname){ ?> is-invalid<?php } ?>" value="<?php echo $nickname?$nickname:''; ?>" required>
                    <?php if($error_nickname) { ?>
                        <div class="invalid-feedback">
                            <?php echo $error_nickname; ?>
                        </div>
                    <?php } ?>
                </div>
                <div class="col-md-6 form-label-group">
                    <label for="email">Email</label>
                    <input type="text" name="email" id="email" class="form-control<?php if($error_email){ ?> is-invalid<?php } ?>" value="<?php echo $email?$email:''; ?>" required>
                    <?php if($error_email) { ?>
                        <div class="invalid-feedback">
                            <?php echo $error_email; ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="form-label-group">
                <label for="password">Пароль</label>
                <div class="input-group<?php if($error_password){ ?> is-invalid<?php } ?>">
                    <input type="password" name="password" id="password" class="form-control<?php if($error_password){ ?> is-invalid<?php } ?>" value="<?php echo $password?$password:''; ?>" required>
                    <div class="input-group-append">
                        <button class="btn btn-outline-<?php if($error_password){ ?>danger<?php } else { ?>secondary<?php } ?> js-toggle-password" type="button" data-toggle='<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="icon"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>'><svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="icon"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></button>
                    </div>
                </div>
                <?php if($error_password) { ?>
                    <div class="invalid-feedback">
                        <?php echo $error_password; ?>
                    </div>
                <?php } ?>
            </div>
            <button class="btn btn-md btn-success btn-group-in btn-block" type="submit"><svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="icon"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg> Зарегистрироваться</button>
        </main>
    </form>
</div>
<?php echo $footer; ?>