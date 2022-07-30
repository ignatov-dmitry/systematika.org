
<div class="bg-white">
    <div id="addgroupContent">

        <form id="formAddgroupIndividual" action="" method="post">

            <div class="modal-body" id="modalAddgroupContent">


                <p>Оставьте заявку. мы подберём преподавателя и свяжемся с вами в течение текущего или на следующий рабочий день.</p>

                <div class="form-group">
                    <label for="exampleFormControlSelect1"><b>Предмет</b></label>
                    <select class="form-control" name="Предмет">
                        <option>Математика</option>
                        <option>Физика</option>
                        <option>Биология</option>
                        <option>Химия</option>
                        <option>ТРИЗ</option>
                        <option>Шахматы</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="exampleFormControlSelect2"><b>Класс</b></label>
                    <select id="exampleFormControlSelect2" class="form-control" name="Класс">
                        <option>1</option>
                        <option>2</option>
                        <option>3</option>
                        <option>4</option>
                        <option>5</option>
                        <option>6</option>
                        <option>7</option>
                        <option>8</option>
                        <option>9</option>
                        <option>10</option>
                        <option>11</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="exampleFormControlTextarea1"><b>Пожелания по дням недели и времени</b></label>
                    <textarea name="Пожелания по дням недели и времени" class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label for="exampleFormControlTextarea1"><b>Ваши задачи и пожелания</b></label>
                    <textarea name="Ваши задачи и пожелания" class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                </div>

            </div>
            <div class="modal-footer">
                <input  type="submit" class="btn btn-primary" id="addgroupBtn" value="Записаться">
            </div>
        </form>


    </div>
</div>




<style>
    button.close{
        padding:0;
        background-color:transparent;
        border:0
    }
    a.close.disabled{pointer-events:none}
    button{border-radius:0}
    button:focus{outline:1px dotted;outline:none -webkit-focus-ring-color}
    button,input,optgroup,select,textarea{margin:0;font-family:inherit;font-size:inherit;line-height:inherit}button,input{overflow:visible}button,select{text-transform:none}[role=button]{cursor:pointer}select{word-wrap:normal}[type=button],[type=reset],[type=submit],button{-webkit-appearance:button}[type=button]:not(:disabled),[type=reset]:not(:disabled),[type=submit]:not(:disabled),button:not(:disabled){cursor:pointer}[type=button]::-moz-focus-inner,[type=reset]::-moz-focus-inner,[type=submit]::-moz-focus-inner,button::-moz-focus-inner{padding:0;border-style:none}input[type=checkbox],input[type=radio]{box-sizing:border-box;padding:0}textarea{overflow:auto;resize:vertical}fieldset{min-width:0;padding:0;margin:0;border:0}legend{display:block;width:100%;max-width:100%;padding:0;margin-bottom:.5rem;font-size:1.5rem;line-height:inherit;color:inherit;white-space:normal}progress{vertical-align:baseline}[type=number]::-webkit-inner-spin-button,[type=number]::-webkit-outer-spin-button{height:auto}[type=search]{outline-offset:-2px;-webkit-appearance:none}[type=search]::-webkit-search-decoration{-webkit-appearance:none}::-webkit-file-upload-button{font:inherit;-webkit-appearance:button}

    .modal-header .close {
        padding: 0.7rem 0.7rem;
        margin: -1rem -1rem -1rem auto;
    }

    .close {
        float: right;
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1;
        color: #000;
        text-shadow: 0 1px 0 #fff;
        opacity: .5;
    }

    .close:hover {
        color: #000;
        text-decoration: none;
    }
    .close:not(:disabled):not(.disabled):focus, .close:not(:disabled):not(.disabled):hover {
        opacity: .75;
    }
</style>