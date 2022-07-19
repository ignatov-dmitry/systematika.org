<div class="my-3 p-3 bg-white rounded shadow-sm">
    <h6 class="border-bottom border-gray pb-2 mb-0">Список абонементов в MoyKlass</h6>


    <table class="table table-sm">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">ID</th>
            <th scope="col">Название</th>
            <th scope="col">Стоимость</th>
            <th scope="col">Создан</th>
        </tr>
        </thead>
        <tbody>
        {%*SUBSCRIPTIONS*}
            <tr>
                <th scope="row">{*SUBSCRIPTIONS:^N*}</th>
                <td>{*SUBSCRIPTIONS:id*}</td>
                <td><strong class="d-block text-gray-dark">{*SUBSCRIPTIONS:name*}</strong></td>
                <td>{*SUBSCRIPTIONS:price*}</td>
                <td>{*SUBSCRIPTIONS:createdAt*}</td>
            </tr>
            {*SUBSCRIPTIONS*%}
            </tbody>
            </table>

    </div>