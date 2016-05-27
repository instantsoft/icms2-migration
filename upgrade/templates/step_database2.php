<h1>База данных 2.x</h1>

<p>Укажите реквизиты для подключения к базе данных сайта на <b>InstantCMS 2.x</b></p>

<form id="step-form">

    <fieldset>

        <div class="field">
            <label>Сервер</label>
            <input type="text" class="input input-icon icon-db-server" name="db[host]" value="localhost" />
        </div>

        <div class="field">
            <label>Пользователь</label>
            <input type="text" class="input input-icon icon-user" name="db[user]" value="" />
        </div>

        <div class="field">
            <label>Пароль</label>
            <input type="password" class="input input-icon icon-password" name="db[pass]" value="" />
        </div>

        <div class="field">
            <label>База данных</label>
            <input type="text" class="input input-icon icon-db" name="db[base]" value="" />
        </div>

        <div class="field">
            <label>Префикс таблиц</label>
            <input type="text" class="input input-icon icon-db-prefix" name="db[prefix]" value="cms_" />
        </div>

    </fieldset>

</form>

<div class="buttons">
    <input type="button" name="next" id="btn-next" value="Далее" onclick="submitStep()" />
</div>

