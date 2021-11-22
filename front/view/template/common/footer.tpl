    <div class="popup diff">
        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="icon icon-spinner"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line></svg>
        <div class="container-fluid">
            <div class="row d-flex align-items-stretch">
                <div class="col-12 col-sm-8 col-md-5 col-lg-4 col-xl-3 px-0 popup-inner">
                </div>
                <div class="col-sm-4 col-md-7 col-lg-8 col-xl-9 d-none d-sm-block px-0 popup-backdrop">
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo $domain; ?>/front/view/libs/popper.min.js"></script>
<script src="<?php echo $domain; ?>/front/view/libs/bootstrap.min.js"></script>
<?php foreach($scripts as $script) { ?>
    <script src="<?php echo $script; ?>"></script>
<?php } ?>
<script src="<?php echo $domain; ?>/front/view/js/common.js" defer></script>
</body>
</html>
