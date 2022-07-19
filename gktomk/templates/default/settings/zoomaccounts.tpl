<div class="my-3 p-3 bg-white rounded shadow-sm">
    <h6 class="pb-2 mb-2">
        Настройки аккаунтов ZOOM
        <div class="close float-right">
            <button type="button" class="btn btn-outline-success" onclick="zoomaccounts.add.openModal();">Добавить</button>
        </div>
    </h6>


    <table class="table table-sm table-responsive-sm" id="zoomaccounts_table">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Аккаунт ZOOM</th>
            <th scope="col">Комментарий</th>
            <th scope="col"></th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>


</div>

<div class="modal fade" id="modalZoomaccounts" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="syncModalLabel">Добавление аккаунта ZOOM</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" id="formZoomaccounts" action="">
                <div class="modal-body">
                    <input type="hidden" class="form-control" id="zoomaccountsFormID" name="zoomaccounts[id]">
                    <div class="form-group">
                        <label for="exampleFormControlInput1">Аккаунт (логин)</label>
                        <input type="text" class="form-control" name="zoomaccounts[login]" required="required">
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlInput1">API Key</label>
                        <input type="text" class="form-control" id="syncFormGKOFFER" name="zoomaccounts[api_key]" required="required">
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlInput1">API Secret</label>
                        <input type="text" class="form-control" id="syncFormGKOFFER" name="zoomaccounts[api_secret]" required="required">
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlInput1">Комментарий</label>
                        <input type="text" class="form-control" id="syncFormMKSUB" name="zoomaccounts[comment]">
                    </div>


                </div>
                <div class="modal-footer">
                    <input type="submit" class="btn btn-primary" id="syncBtn">
                </div>
            </form>
        </div>
    </div>
</div>

<div id="result_form"></div>
<script>SETT.URL_GK = '{*URL_GK*}';</script>
        <script defer type="text/javascript" src="{*URL_SITE*}/templates/{*TPL_NAME*}/js/settings.zoomaccounts.js?r={*@rand(1000000, 99999999)*}"></script>