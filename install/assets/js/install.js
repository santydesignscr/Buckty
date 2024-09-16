var Buckty = {
    load: function(){
        $.get(site_url + '/temps/siteinfo.php').done(function(r){
           var main_container = $('.main_container');
            var container = $('#formDb');
            container.html(r);
            main_container.removeClass('extended');
        });
    },
    loadInfo: function(){
        $.get(site_url + '/temps/info.php').done(function(r){
            var main_container = $('.main_container');
            var container = $('#formDb');
            container.html(r);
            main_container.addClass('extended');
        });
    },
    loadDb: function(e){
        var data = e.serializeArray();
        var l = $('#site_url').val().substr(-1);
        if(l != '/') {
            Buckty.appendMessage(1,'Site url must end with slash "/"');
            return false;
        }
        $.post(site_url + 'temps/dbdetails.php',data).done(function(r){
            $('.message').empty();
            var container = $('#formDb');
            container.html(r);
        });
        return false;
    },
    loadDatabase: function(e){
        var data = e.serializeArray();
        $.post(site_url + '/action.php',data).done(function(r){
            r = JSON.parse(r);
            if(r.error_code === 1){
                Buckty.appendMessage(r.error_code, r.message);
            } else {
                Buckty.loadLast(data);
                $('.message').empty();
                Buckty.appendMessage(r.error_code, r.message);
            }
        });
        return false;
    },
    loadLast: function(data){
         $.post(site_url + '/temps/adminDetails.php',data).done(function(r){
            var container = $('#formDb');
            container.html(r);
        });
    },
    loadAdmin: function(e){
        var data = e.serializeArray();
        $.post(site_url + '/addAdmin.php',data).done(function(r){
            r = JSON.parse(r);
            if(r.error_code === 1){
                Buckty.appendMessage(r.error_code, r.message);
            } else {
                Buckty.loadDone(data);
                $('.message').empty();
                Buckty.appendMessage(r.error_code, r.message);
            }
        });
        return false;
    },
    loadDone: function(){
        $('.message').empty();
        $.get(site_url + '/temps/done.php').done(function(r){
            var container = $('#formDb');
            container.html(r);
        });
    },
    appendMessage: function(code,message){
        var container = $('.message');
        container.empty();
        if(code === 1){
            container.append('<span class="error">'+message+'</span>');
        } else if(code === 0){
            container.append('<span class="success">'+message+'</span>');
        }
    }
}