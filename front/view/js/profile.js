let conn = null, timer = null, selectedRoomId = null, userId = null, usersInRoom;
let popup = {
    close: function () {
        $('.open').removeClass('open');
        $('.init').removeClass('init');
    },

    open: function () {
        if (!!!$(this).data('trigger-open') || $(this).attr('data-link') == '/') return;
        if ($('.popup.open.init').length) popup.close();
        let trigger = $(this),
            el = $('.' + trigger.data('trigger-open'));

        if (!!el.attr('data-link') || !!trigger.attr('data-link')) {
            let link = el.attr('data-link') || trigger.attr('data-link');
            req = $.get(link, function (data) {
                el.find('.popup-inner').html(data);
                el.addClass('init');
                initTooltip();
            });
        } else {
            el.addClass('init');
        }
        el.addClass('open');
    },

    without: function (e) {
        if ($(e.target).hasClass('popup-backdrop') || $(e.target).hasClass('popup')) {
            popup.close();
        }
    },
};

let alert = {
    show: function (title, text, type = 'success') {
        let typeImage = '',
            typeHead = 'toast-header';

        if (type == 'success') {
            typeImage = '<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="toast-icon icon"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>&nbsp;';
            typeHead = 'toast-header bg-success text-light';
        } else if (type == 'error') {
            typeImage = '<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="toast-icon icon"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>&nbsp;';
            typeHead = 'toast-header bg-danger text-light';
        } else if (type == 'warning') {
            typeImage = '<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="toast-icon icon"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>&nbsp;';
            typeHead = 'toast-header bg-warning text-light';
        }

        let postTime = new Date().toLocaleString("sv-SE"),
            template = `
            <div class="toast fade show">
                <div class="${typeHead}">
                    ${typeImage}
                    <strong class="mr-auto">${title}</strong>
                    <time class="timeago text-small" datetime="${postTime}">${postTime}</time>
                </div>
                <div class="toast-body">${text}</div>
            </div>
            `;

        let toast = $(template);
        $('.toasts').append(toast);

        setTimeout(function () {
            toast.remove();
        }, 5000);
        $('.timeago').timeago();
    }
};

let wsTargets = {
    getMessages: function(roomId = false) {
        if(Number.isInteger(roomId)) {
            roomItem = $('.js-room[data-room-id="'+roomId+'"]');
        } else{
            roomItem = $(this);
        }

        selectedRoomId = roomItem.attr('data-room-id');

        $('.rooms .js-room').removeClass('active');
        $('.rooms').find('.js-room[data-room-id="'+selectedRoomId+'"]').addClass('active');

        if(!!conn) {
            conn.send(JSON.stringify({
                route: 'get_messages',
                data: {
                    room_id: selectedRoomId
                }
            }));

            popup.close();
        } else {
            alert.show('Ошибка', 'Нет соединения с сервером.', 'error');
        }
    },

    sendMessage: function(self = false) {
        let inp = $(self),
            text = inp.val();
        if(!!conn) {
            inp.val('');
            conn.send(JSON.stringify({
                route: 'add_message',
                data: {
                    room_id: selectedRoomId,
                    text: text
                }
            }));
        } else {
            alert.show('Ошибка', 'Нет соединения с сервером.', 'error');
        }
    },

    addRoom: function(form) {
        let formEls = form.find('input[name="user_ids[]"]'), ids = [],
            submitBtn = form.find('.btn[type="submit"]'),
            icon = submitBtn.find('.icon');
        $.each(formEls, function(el, data) {
            ids.push(+$(data).val());
        });

        if(!!conn) {
            submitBtn.prepend('<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="icon icon-spinner"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line></svg>').attr('disabled', true);
            icon.remove();

            conn.send(JSON.stringify({
                route: 'add_room',
                data: {
                    user_ids: ids
                }
            }));
        } else {
            alert.show('Ошибка', 'Нет соединения с сервером.', 'error');
        }
    },

    searchUser: function() {
        let query = $('.js-search-inp').val();
        if(!!conn) {
            if(query.length && query.replace('@', '').length) {
                conn.send(JSON.stringify({
                    route: 'search_user',
                    data: {
                        query: query
                    }
                }));
            } else {
                $('.users').html('');
            }
        } else {
            alert.show('Ошибка', 'Нет соединения с сервером.', 'error');
        }
    },

    removeMissed: function(roomId) {
        if(!!conn) {
            $('.js-room[data-room-id="'+roomId+'"]').removeClass('missed');
            conn.send(JSON.stringify({
                route: 'remove_missed',
                data: {
                    room_id: roomId
                }
            }));
        } else {
            alert.show('Ошибка', 'Нет соединения с сервером.', 'error');
        }
    },

    loadMore: function() {
        let btn = $(this),
            page = btn.attr('data-page'),
            lastDate = btn.attr('data-last-date');

        if(!!conn) {
            btn.remove();
            conn.send(JSON.stringify({
                route: 'get_messages',
                data: {
                    room_id: selectedRoomId,
                    page: page,
                    last_date: lastDate
                }
            }));
        } else {
            alert.show('Ошибка', 'Нет соединения с сервером.', 'error');
        }
    }
};

let wsRoutes = {
    error: function (data) {
        alert.show('Ошибка', data.message, 'error');
    },

    errorAuth: function(data) {
        alert.show('Ошибка', data.message, 'error');
        setTimeout(function() {
          window.location.reload();
        }, 2000);
    },

    allRooms: function (data) {
        let roomsWrap = $('.rooms');
        if(data.length) {
            let html = '<ul class="list-group rounded-0">';
            $.each(data, function (el, room) {
                let missedClass = '';
                if(room.missed == '1') {
                    missedClass = ' missed';
                }
                html += `
                <li class="list-group-item list-group-item-action border-left-0 border-right-0 border-top-0 px-3 room-item js-room${missedClass}" data-room-id="${room.room_id}">
                    <div class="d-flex justify-content-between">
                        <b class="text-truncate">${room.name}</b> <time class="timeago text-small flex-shrink-0 ml-3" datetime="${room.date_updated}">${room.date_updated}</time>
                    </div>
                    <span class="text-truncate d-block mt-1 last-message-text">${room.last_message}</span>
                </li>
                `;
            });
            html += '</ul>';

            roomsWrap.html(html);
            $('.timeago').timeago();
        } else {
            roomsWrap.html('<span class="rooms-placeholder">Пока нет бесед, <span class="text-link" data-trigger-open="diff" data-link="/search">добавить новую</span>?</span>');
        }
    },

    addRoom: function(data) {
        let roomsWrap = $('.rooms'),
            html = '', empy = roomsWrap.find('.rooms-placeholder').length;
        if(empy) {
            roomsWrap.find('.rooms-placeholder').remove();
            html += '<ul class="list-group rounded-0">';
        }

        html += `
        <li class="list-group-item list-group-item-action border-left-0 border-right-0 border-top-0 px-3 room-item js-room" data-room-id="${data.room_id}">
            <div class="d-flex justify-content-between">
                <b class="text-truncate">${data.name}</b> <time class="timeago text-small flex-shrink-0 ml-3" datetime="${data.date_updated}">${data.date_updated}</time>
            </div>
            <span class="text-truncate d-block mt-1 last-message-text">${data.last_message}</span>
        </li>
        `;

        if(empy) {
            html += '</ul>';
            roomsWrap.html(html);
        } else {
            roomsWrap.find('.list-group').prepend(html);
        }

        popup.close();
        // console.log(data);
        // wsTargets.getMessages(+data.room_id);

        //data
        $('.timeago').timeago();
    },

    existedRoom: function(data) {
        popup.close();
        wsTargets.getMessages(+data.room_id);
    },

    searchUser: function(data) {
        let searchWrap = $('.users');
        if(data.length) {
            let html = '';
            $.each(data, function (el, user) {
                html += `
                <div class="d-flex justify-content-between align-items-center py-1 px-3" data-user-id="${user.user_id}">
                    <span class="text-truncate">
                        <span class="status-circle" data-status="${user.status}"></span>
                        ${user.full_name}
                    </span>
                    <button class="btn btn-link js-add-user-to-new-room" data-user-full-name="${user.full_name}"><svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="icon"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg></button>
                </div>
                `;
            });

            searchWrap.html(html);
        } else {
            searchWrap.html('<span class="search-placeholder">Нет результатов</span>');
        }
    },

    userStatusUpdate: function(data) {
        $('[data-user-id="'+data.user_id+'"]').find('.status-circle').attr('data-status', data.status);
    },

    getMessages: function(data) {
        if(data.current_page == 1) {
            userId = data.client_id;
            usersInRoom = data.users_in_room;
            let template = `
            <header class="chat-room-header p-2 text-white-50 bg-dark">
                <h1 class="h5 mb-0 text-truncate">В беседе состоят: ${data.room_data.name}</h1>
            </header>
            <main class="chat-room-messages h-100" data-room-id="${data.room_data.room_id}">

            </main>
            <div class="form-label-group p-2 mb-0 bg-dark">
                <div class="input-group">
                    <input type="text" name="message" id="message" class="form-control js-send-message-inp" placeholder="сообщение ..." autocomplete="off" autofocus>
                    <div class="input-group-append">
                        <button class="btn btn-secondary js-send-message-btn" type="button"><svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="icon"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg></button>
                    </div>
                </div>
            </div>
            `;

            $('.chat-room').html(template);
        }

        let html = '';
        if(data.messages.length) {
            $.each(data.messages, function(el, message) {
                let position = 'left';
                if(message.author_id == data.client_id) {
                    position = 'right';
                }

                html = `
                <div class="message message-${position} px-2 py-1">
                    <span class="message-author">${data.users_in_room[message.author_id].full_name}</span>
                    <p class="message-text p-2">${message.text}</p>
                    <time class="message-time timeago text-small" datetime="${message.date_added}">${message.date_added}</time>
                </div>
                ` + html;
            });

            if(data.next_page > 0) {
                html = '<button class="btn btn-md btn-primary js-load-more" data-page="'+ data.next_page +'" data-last-date="' + data.messages[data.messages.length-1].date_added + '">Загрузить еще ..</button>' + html;
            }

            let messageWrap = $('.chat-room-messages');

            if(data.current_page == 1) {
                messageWrap.html(html);
                messageWrap.scrollTop(messageWrap.prop('scrollHeight'));
            } else {
                messageWrap.prepend(html);
            }
            $('.timeago').timeago();
        } else {
            $('.chat-room-messages').html('<span class="message-placeholder">Пока нет сообщений</span>');
        }

        if($('.js-room[data-room-id="'+data.room_data.room_id+'"]').hasClass('missed')) {
            wsTargets.removeMissed(data.room_data.room_id);
        }
    },

    addMessage: function(data) {

        if(selectedRoomId == data.message_data.room_id) {
            let position = 'left';
            if(data.message_data.author_id == userId) {
                position = 'right';
            }

            let html = `
            <div class="message message-${position} px-2 py-1">
                <span class="message-author">${usersInRoom[data.message_data.author_id].full_name}</span>
                <p class="message-text p-2">${data.message_data.text}</p>
                <time class="message-time timeago text-small" datetime="${data.message_data.date_added}">${data.message_data.date_added}</time>
            </div>
            `;

            let messageWrap = $('.chat-room-messages');

            messageWrap.append(html);
            messageWrap.find('.message-placeholder').remove();
            messageWrap.scrollTop(messageWrap.prop('scrollHeight'));
        }

        let room = $('.js-room[data-room-id="'+data.message_data.room_id+'"]');
        room.find('.last-message-text').html(data.message_data.text);
        room.prependTo(".rooms .list-group");


        $('.popup-main .js-room[data-room-id="'+data.message_data.room_id+'"]').eq(1).remove();
        $('.chat-sidebar .js-room[data-room-id="'+data.message_data.room_id+'"]').eq(1).remove();

        if(data.missed == '1') {
            if(selectedRoomId == data.message_data.room_id) {
                wsTargets.removeMissed(data.message_data.room_id);
            } else {
                $('.js-room[data-room-id="'+data.message_data.room_id+'"]').addClass('missed');

                var audio = new Audio();
                audio.src = '/front/view/music/message.mp3';
                audio.autoplay = true;
            }
        }

        $('.timeago').timeago();
    },

    selectRoom: function(data) {
        wsTargets.getMessages(+data.room_id);
    }
};

let initTooltip = function() {
    if(true !== ("ontouchstart" in window || window.DocumentTouch && document instanceof DocumentTouch)) {
        $('[title]:not(time)').tooltip({ container: '.navbar', trigger: 'hover' });
    }
};

let setUnits = function() {
    let vh = window.innerHeight * 0.01;
    $('body').css('--vh', vh+'px');
};

$(window).on('resize', function() {
    setUnits();
});

$(document).ready(function () {
    setUnits();
    initTooltip();
    $(document).on('click', '[data-trigger-open]', popup.open);
    $(document).on('click', '[data-trigger-close]', popup.close);
    $(document).on('click', '.popup', popup.without);
    $(document).on('click', '.js-room', wsTargets.getMessages);
    $(document).on('keydown', '.js-search-inp', function(e){
		if (e.keyCode == 27) $('.js-search-btn').trigger('click');
		if (e.keyCode == 8 || e.keyCode == 46){
			clearTimeout(timer);
	   	    timer = setTimeout(wsTargets.searchUser, 500);
		}
	});
    $(document).on('input', '.js-search-inp', function(e){
        clearTimeout(timer);
        timer = setTimeout(wsTargets.searchUser, 500);
	});
    $(document).on('click', '.js-search-btn', wsTargets.searchUser);
    $(document).on('click', '.js-add-user-to-new-room', function() {
        let btn = $(this);

        if(!!btn.closest('[data-user-id]').attr('data-user-id') && !!btn.closest('[data-user-id]').find('[data-status]').attr('data-status') && !!btn.attr('data-user-full-name') && !$('.js-remove-user-from-new-room[data-user-id="'+btn.closest('[data-user-id]').attr('data-user-id')+'"]').length) {

            $('.selected-users .js-text-empty-room-users').remove();
            $('.js-add-room').prop('disabled', false);
            $('.selected-users').append(`
            <div class="d-flex justify-content-between align-items-center">
                <span data-user-id="${btn.closest('[data-user-id]').attr('data-user-id')}">
                    <span class="status-circle" data-status="${btn.closest('[data-user-id]').find('[data-status]').attr('data-status')}"></span>
                    ${btn.attr('data-user-full-name')}
                    <input type="hidden" name="user_ids[]" value="${btn.closest('[data-user-id]').attr('data-user-id')}">
                </span>
                <button class="btn btn-link text-danger js-remove-user-from-new-room"><svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="icon"><line x1="5" y1="12" x2="19" y2="12"></line></svg></button>
            </div>
            `);
        }
    });
    $(document).on('click', '.js-remove-user-from-new-room', function() {
        let btn = $(this);

        if(!!btn.attr('data-user-id')) {
            btn.closest('.d-flex').remove();
            if(!$('.selected-users').html().trim().length) {
                $('.selected-users').append('<span class="text-small text-muted js-text-empty-room-users">В списке еще нет пользователей..</span>');
                $('.js-add-room').prop('disabled', true);
            }
        }
    });
    $(document).on('click', '.js-send-message-btn', function() {
        wsTargets.sendMessage($(this).closest('.input-group').find('.js-send-message-inp'))
    });
    $(document).on('keydown', '.js-send-message-inp', function(e) {
        if(e.keyCode == 13) {
            wsTargets.sendMessage(this);
        }
    });
    $(document).on('click', '.js-load-more', wsTargets.loadMore);


    conn = new ReconnectingWebSocket('wss://9922.ml/ws/?a_token=' + cookie.get('a_token'));
    conn.reconnectInterval = 15000;

    conn.onopen = function (e) {
        console.log("Connection established!");
        alert.show('Успешно', 'Соединение с сервером установлено.', 'success');
    };

    conn.onmessage = function (e) {
        message = JSON.parse(e.data);
        if (wsRoutes.hasOwnProperty(message.route)) {
            try {
                wsRoutes[message.route](message.data);
            } catch (e) {
                console.log(e);
            }
        }
        console.log(message);
    };

    conn.onerror = function(e) {
        $('.rooms').html('<span class="rooms-placeholder">Ожидание соединения ..</span>');
        $('.chat-room').html('');
        alert.show('Нет соединения', 'Нет соединения с сервером, попытка повторного подключения через 15 секунд.', 'warning');
        popup.close();
    };

    conn.onclose = function (e) {
        $('.rooms').html('<span class="rooms-placeholder">Ожидание соединения ..</span>');
        $('.chat-room').html('');
        popup.close();
    };
});
