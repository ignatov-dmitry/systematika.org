<div class="my-3 p-3 bg-white rounded shadow-sm">
    <h6 class="pb-2 mb-2">
        Настройка WhatsApp
    </h6>
    {?*SAVE*}
    <div class="alert alert-success" role="alert">
        Настройки успешно сохранены.
    </div>
    {?}
    <form method="post">
        <div class="form-group">
            <label for="exampleInputEmail1">Текст сообщения</label>
            <textarea rows="5" type="text"  name="whatsapp[message]" class="form-control"  placeholder="Введите текст сообщения">{*whatsapp_message*}</textarea>
            <small class="form-text text-muted">Доступны следующие переменные: first_name, last_name, class_name, course_name, topic, datestart, timeleft, datesend, phone</small>
        </div>
        <div class="form-group">
            <label for="exampleInputPassword1">Время до начала занятия (минуты)</label>
            <input type="text" class="form-control" name="whatsapp[time]" placeholder="Время в минутах" value="{*whatsapp_time*}">
        </div>
        <div class="form-group">
            <label for="exampleInputPassword1">Включить тестовый режим на номер</label>
        <div class="input-group mb-3">

            <div class="input-group-prepend">
                <div class="input-group-text">
                    <input name="whatsapp[debug]" type="checkbox" aria-label="Checkbox for following text input" {?*whatsapp_debug*}checked{?}>
                </div>
            </div>
            <input name="whatsapp[phone]" type="text" class="form-control" placeholder="Номер (только цифры)" value="{*whatsapp_phone*}">
        </div>
        </div>
        
        <div class="form-group">
            <label for="typeapi">Тип интеграции</label>
            <select class="form-control" id="typeapi" name="whatsapp[typeapi]">
                <option value="chatapi"{?*whatsapp_typeapi="chatapi"*} selected{?}>Chat-Api</option>
                <option value="wazzup"{?*whatsapp_typeapi="wazzup"*} selected{?}>WAZZUP</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Сохранить</button>
    </form>
</div>