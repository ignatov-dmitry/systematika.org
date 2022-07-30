<style>
    .active {
        background: #ffc628;
    }
</style>
<div class="my-3 p-3 bg-white rounded shadow-sm">
    <h6 class="pb-2 mb-2">
        Синхронизация групп
        <div class="float-right">
            <a href="{*URL_SITE*}/settings/programs" class="btn btn-primary">Программы</a>
            <a href="{*URL_SITE*}/settings/classes" class="btn btn-primary">Классы</a>
        </div>
    </h6>

    <hr/>
    <div class="accordion" id="accordionGroups">
        Загрузка настроек программ и групп...
    </div>


</div>

<script>SETT.URL_GK = '{*URL_GK*}';</script>
        <script defer type="text/javascript" src="{*URL_SITE*}/templates/{*TPL_NAME*}/js/settings.groups.js?r={*@rand(1000000, 99999999)*}"></script>