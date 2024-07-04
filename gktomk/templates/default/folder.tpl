<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">{*PATH*}</li>
    </ol>
</nav>
<div><a class="btn btn-link" href="#" onclick="videorecords.openUnassignedFolder.back(event, '{*BACK*}')">Назад</a></div>
<table class="table">
    {%*ITEMS*}
    <tr>
        <td><a onclick="{?!*ITEMS:is_file!=false*}videorecords.openUnassignedFolder.openDir(event, '{*PATH*}{*ITEMS:path*}');{?}{?!*ITEMS:is_file=false*}videorecords.openUnassignedFolder.selectVideo(event, '{*ITEMS:file_name*}', '{*PATH*}');{?}" href="?path={*PATH*}/{*ITEMS:path*}">{*ITEMS:path*}</a></td>
    </tr>
    {*ITEMS*%}
</table>