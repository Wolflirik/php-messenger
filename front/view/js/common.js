let animation = {
    link: function (e) {
        let link = $(this);
        if (link.prop('hostname') == location.hostname && link.prop('target') != '_blank') {
            e.preventDefault();

            animation.location(link.attr('href'));
        }
    },

    location: function (link) {
        if (link == '/') return;

        if ($('.open').length) popup.close();

        setTimeout(function () {
            location = link;
        }, 50);
    },

    submit: function (e) {

        e.preventDefault();

        let form = $(this).closest('form'),
            submitBtn = form.find('.btn[type="submit"]'),
            icon = submitBtn.find('.icon');

        if (!!form.attr('data-type') && form.attr('data-type') == 'ajax') {
            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                dataType: 'html',
                data: form.serialize(),
                timeout: 0,
                beforeSend: function () {
                    submitBtn.prepend('<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="icon icon-spinner"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line></svg>').attr('disabled', true);
                    icon.remove();
                },
                success: function (data) {
                    let parentClass = form.parent().attr('class').split(' ', 1)[0];
                    let htmlData = $(data).filter('.' + parentClass).html();
                    if (htmlData) {
                        form.parent().html(htmlData);
                    } else {
                        form.parent().html($(data).find('.' + form.parent().attr('class').split(' ', 1)[0]).html());
                    }
                }
            });
        } else if(!!form.attr('data-type') && form.attr('data-type') == 'ws') {
            if(wsTargets.hasOwnProperty(form.attr('action'))) {
                wsTargets[form.attr('action')](form);
            }
        }else {
            if ($('open').length !== 0) popup.close();
            form.submit();
        }
    },

    togglePass: function () {
        let btn = $(this),
            inp = btn.closest('.form-label-group').find('[name=password]');

        if (inp.attr('type') == 'password') {
            inp.attr('type', 'text');
        } else {
            inp.attr('type', 'password');
        }

        let eye = btn.attr('data-toggle');
        btn.attr('data-toggle', btn.html());
        btn.html(eye);
    }
};

$(document).ready(function () {
    $(document).on('click', '.js-toggle-password', animation.togglePass);
    $(document).on('click', 'form .btn[type=submit]', animation.submit);
    $(document).on('click', 'a:not([href="#"])', animation.link);
});