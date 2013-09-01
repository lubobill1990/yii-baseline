define([], function () {
    var rts_server_url = "npeasy.com:3000";
    var this_site_url = "npeasy.com:3001";
    var socket = new eio.Socket('ws://' + rts_server_url + '/', {
        "upgrade":true
    })
    socket.on('open', function () {
        var rts_identity_url = "http://" + rts_server_url + "/identity?conn_id=" + socket.id + "&from_site=http://" + this_site_url
        $('body').append("<iframe src='" + rts_identity_url + "'></iframe>")
    })
    socket.on('message',function(data){
        var json_data=eval("("+data+")");
        socket.emit(json_data.type,json_data.data);
    })
    return socket;
})
