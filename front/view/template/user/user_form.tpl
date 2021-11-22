<header class="popup-header d-flex justify-content-between">
    <h2 class="h5 text-white popup-title">Редактирование ЛК</h2>
    <button class="btn btn-lg text-white btn-custom" data-trigger-close title="Закрыть">
        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="icon"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
    </button>
</header>
<main class="popup-main">
<form action="<?php echo $domain; ?>/user/update" enctype="multipart/form-data" method="post" data-type="ajax">
    <div class="form-label-group px-3 mt-3">
        <label for="nickname">Никнейм</label>
        <input type="text" name="nickname" id="nickname" class="form-control<?php if($error_nickname){ ?> is-invalid<?php } ?>" value="<?php echo $nickname?$nickname:''; ?>" autocomplete="off" required>
        <?php if($error_nickname) { ?>
            <div class="invalid-feedback">
                <?php echo $error_nickname; ?>
            </div>
        <?php } ?>
    </div>
    <div class="form-label-group px-3">
        <label for="email">Email</label>
        <input type="text" name="email" id="email" class="form-control<?php if($error_email){ ?> is-invalid<?php } ?>" value="<?php echo $email?$email:''; ?>" autocomplete="off" required>
        <?php if($error_email) { ?>
            <div class="invalid-feedback">
                <?php echo $error_email; ?>
            </div>
        <?php } ?>
    </div>
    <div class="form-label-group px-3">
        <label for="password">Пароль</label>
        <div class="input-group<?php if($error_password){ ?> is-invalid<?php } ?>">
            <input type="password" name="password" id="password" class="form-control<?php if($error_password){ ?> is-invalid<?php } ?>" autocomplete="off">
            <div class="input-group-append">
                <button class="btn btn-outline-<?php if($error_password){ ?>danger<?php } else { ?>secondary<?php } ?> js-toggle-password" type="button" data-toggle='<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="icon"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>'><svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="icon"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></button>
            </div>
        </div>
        <?php if($error_password) { ?>
            <div class="invalid-feedback">
                <?php echo $error_password; ?>
            </div>
        <?php } ?>
        <small class="form-text text-muted">Заполните, если хотите обновить.</small>
    </div>
    <div class="mt-4 px-3">
        <button class="btn btn-md btn-success btn-group-in btn-block" type="submit"><svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="icon"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg> Сохранить</button>
    </div>
</main>
