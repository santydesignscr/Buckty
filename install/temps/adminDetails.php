<div id="formDb" class="form_container">
    <h1 class="mini_title">Create admin account</h1>
    <form onsubmit="return Buckty.loadAdmin($(this));">
        <div class="field">
            <label for="username">Username</label>
            <input type="text" id="username" name="user" class="controlinput" placeholder="Username" required/>
        </div>
        <div class="field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="controlinput" placeholder="email" required/>
        </div>
        <div class="field">
            <label for="password">Password: min: 5 and max 15</label>
            <input type="password" id="passw" pattern=".{5,15}" name="passw" class="controlinput" placeholder="Password" required/>
        </div>
        <div class="field">
            <div class="loader"></div>
            <input type="hidden" name="hostname" value="<?= $_POST['hostname'];?>"/>
            <input type="hidden" name="username" value="<?= $_POST['username'];?>"/>
            <input type="hidden" name="password" value="<?= $_POST['password'];?>"/>
            <input type="hidden" name="database" value="<?= $_POST['database'];?>"/>
            <button type="submit" class="button blue"><i class="fa fa-check"></i><span>Create Admin</span></button>
        </div>
    </form>
</div>