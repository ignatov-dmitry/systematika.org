<div class="my-3 p-3 bg-white rounded shadow-sm">
    <h6 class="pb-2 mb-2">
        Настройки синхронизации
        <div class="close float-right">
            <button type="button" class="btn btn-outline-success" onclick="sync.add.openModal();">Добавить</button>
        </div>
    </h6>


    <table class="table table-sm" id="sync_table">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Программа</th>
            <th scope="col">Предложение ГК</th>
            <th scope="col">Абонемент МК</th>
            <th scope="col"></th>
        </tr>
        </thead>
        <tbody>
        {%*SYNCS*}
        <tr>
            <th scope="row">{*SYNCS:id*}</th>
            <td><strong class="d-block text-gray-dark">{*SYNCS:program*}</strong></td>
            <td><a href="{*URL_GK*}/pl/sales/offer/update?id={*SYNCS:gk_offer*}" target="_blank">{*SYNCS:gk_offer*}</a>
            </td>
            <td>{*SYNCS:mk_sub*}</td>
            <td>
                <a href="#" data-toggle="tooltip" data-placement="top" title="Редактировать" style="color: blue;"><i
                            class="far fa-edit"></i></a>
                <a href="#" data-toggle="tooltip" data-placement="top" title="Удалить" style="color: red;"><i
                            class="far fa-trash-alt"></i></a>
            </td>
        </tr>
        {*SYNCS*%}
        </tbody>
    </table>


</div>

<div class="modal fade" id="modalSync" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="syncModalLabel">Добавление синхронизации</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" id="formSync" action="">
                <div class="modal-body">
                    <input type="hidden" class="form-control" id="syncFormID" name="sync[id]">
                    <div class="form-group">
                        <label for="exampleFormControlInput1">Программа</label>
                        <input type="text" class="form-control" id="syncFormPROGRAM" name="sync[program]" required="required">
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlInput1">Предложение в ГК</label>
                        <input type="text" class="form-control" id="syncFormGKOFFER" name="sync[gk_offer]" required="required">
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="1" id="syncFormDEMO" name="sync[demo]">
                      <label class="form-check-label" for="syncFormDEMO">
                        Пробное занятие
                      </label>
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlInput1">Абонемент в МК</label>
                        <input type="text" class="form-control" id="syncFormMKSUB" name="sync[mk_sub]" required="required">
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
<script defer type="text/javascript" src="{*URL_SITE*}/templates/{*TPL_NAME*}/js/settings.sync.js?r={*@rand(1000000, 99999999)*}"></script>