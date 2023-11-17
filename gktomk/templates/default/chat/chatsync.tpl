
<div class="my-3 p-3 bg-white rounded shadow-sm">
    <h6 class="border-bottom border-gray pb-2 mb-0">Синхронизация чатов</h6>
    <div id="result_div"></div>
    <form method="GET">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="date_from">ID группы</label>
                        <input type="text" class="form-control" name="group_id_mk" id="group_id_mk">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <button class="btn btn-primary" type="submit">Найти</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <table class="table table-sm table-responsive-sm">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">ID группы</th>
            <th scope="col">Менеджеры</th>
            <th scope="col"></th>
        </tr>
        </thead>
        <tbody>
        {%*SYNCS*}
        <tr>
            <form method="post" action="?group_id_mk={*SYNCS:group_id_mk*}">
                <input type="hidden" name="id" value="{*SYNCS:id*}">
                <td>{*SYNCS:id*}</td>
                <td><input type="text" name="group_id_mk" value="{*SYNCS:group_id_mk*}"></td>
                <td>
                        <div class="form-group">
                            <select multiple class="form-control" name="managers[]" id="managers">
                                {%*MANAGERS*}
                                <option {?* @in_array(*MANAGERS:id*, @json_decode(*SYNCS:manager_ids*)) *} selected {?} value="{*MANAGERS:id*}">{*MANAGERS:name*}</option>
                                {*MANAGERS*%}
                            </select>
                        </div>
                </td>
                <td><button type="submit">Сохранить</button></td>
             </form>
        </tr>
            {*SYNCS*%}
            </tbody>
        </table>
    {*PAGINATION*}
</div>