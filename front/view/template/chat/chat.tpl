<?php echo $header; ?>
<div class="row">
    <div class="col-md-5 col-lg-4 col-xl-3 d-none d-md-block px-0 chat-sidebar rooms">
        <span class="rooms-placeholder">Ожидание соединения ..</span>
    </div>
    <div class="col-12 col-md-7 col-lg-8 col-xl-9 px-0 chat-room d-flex flex-column">
    </div>
</div>
<div class="popup sidebar">
    <div class="container-fluid">
        <div class="row d-flex align-items-stretch">
            <div class="col-12 col-sm-8 col-md-5 col-lg-4 col-xl-3 px-0 popup-inner">
                <header class="popup-header d-flex justify-content-between">
                    <h2 class="h5 text-white popup-title">Список комнат</h2>
                    <button class="btn btn-lg text-white btn-custom" data-trigger-close title="Закрыть">
                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="icon"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </header>
                <main class="popup-main rooms">
                    <span class="rooms-placeholder">Ожидание соединения ..</span>
                </main>
            </div>
            <div class="col-sm-4 col-md-7 col-lg-8 col-xl-9 d-none d-sm-block px-0 popup-backdrop">
            </div>
        </div>
    </div>
</div>
<div class="position-fixed bottom-0 right-0 p-3 toasts" style="z-index: 999; right: 0; bottom: 0;"></div>
<?php echo $footer; ?>