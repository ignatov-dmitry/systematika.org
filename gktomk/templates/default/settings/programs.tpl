<div class="my-3 p-3 bg-white rounded shadow-sm">
    <h6 class="pb-2 mb-2">
        Список программ
        <div class="float-right">
            <a href="{*URL_SITE*}/settings/groups" class="btn">< К синхронизации</a>
            <button type="button" class="btn btn-outline-success" onclick="programs.add.openModal();">Добавить</button>
        </div>
    </h6>


    <table class="table table-sm table-responsive-sm" id="programs_table">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Название</th>
            <th scope="col">Аббревиатура</th>
            <th scope="col">По умолчанию</th>
            <th scope="col">Отображение</th>
            <th scope="col">Порядок</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>


</div>

<div class="modal fade" id="modalProgram" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="programModalLabel">Добавление программы</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" id="formProgram" action="">
                <div class="modal-body">
                    <input type="hidden" class="form-control" id="programFormID" name="program[id]">
                    <div class="form-group">
                        <label for="programFormName">Название</label>
                        <input type="text" class="form-control" id="programFormName" name="program[name]" required="required">
                    </div>
                    <div class="form-group">
                        <label for="programFormShortname">Аббревиатура</label>
                        <input type="text" class="form-control" id="programFormShortname" name="program[shortname]" required="required">
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="1" id="programFormDefault" name="program[default]">
                      <label class="form-check-label" for="programFormDefault">
                        По умолчанию
                      </label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="1" id="programFormShow" name="program[show]">
                      <label class="form-check-label" for="programFormShow">
                        Отображение
                      </label>
                    </div>
                    <div class="form-group">
                        <label for="programFormSort">Порядок</label>
                        <input type="text" class="form-control" id="programFormSort" name="program[sort]" required="required">
                    </div>


                </div>
                <div class="modal-footer">
                    <input type="submit" class="btn btn-primary" id="programBtn">
                </div>
            </form>
        </div>
    </div>
</div>

<div id="result_form"></div>
<script>SETT.URL_GK = '{*URL_GK*}';</script>
        <script defer type="text/javascript" src="{*URL_SITE*}/templates/{*TPL_NAME*}/js/settings.programs.js?r={*@rand(1000000, 99999999)*}"></script>