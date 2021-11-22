<header class="popup-header d-flex justify-content-between">
    <h2 class="h5 text-white popup-title">Поиск собеседников</h2>
    <button class="btn btn-lg text-white btn-custom" data-trigger-close title="Закрыть">
        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="icon"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
    </button>
</header>
<main class="popup-main">
    <div class="form-label-group px-3 mt-3">
        <div class="input-group">
            <input type="text" name="search" id="search" class="form-control js-search-inp" placeholder="@nickname или ФИО" autocomplete="off" autofocus>
            <div class="input-group-append">
                <button class="btn btn-outline-secondary js-search-btn" type="button"><svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="icon"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></button>
            </div>
        </div>
    </div>
    <div class="users pb-4 mb-3 border-bottom"></div>
    <form action="addRoom" class="form-add-room px-3" data-type="ws">
        <span class="text-small d-block mb-2">Список пользователей для создания беседы:</span>
        <div class="selected-users mb-3">
            <span class="text-small text-muted js-text-empty-room-users">В списке еще нет пользователей..</span>
        </div> <!--user_ids-->
        <button class="btn btn-md btn-success btn-group-in btn-block js-add-room" type="submit" disabled><svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="icon"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg> Добавить беседу</button>
    </div>
</main>
