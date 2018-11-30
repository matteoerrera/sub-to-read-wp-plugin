function sendData() {
    $("#str_submit").attr("value", "Loading...");
    var data = {
        'action': 'str_form_submit',
        'name': $("section.sub-to-read-cta #name").val(),
        'email': $("section.sub-to-read-cta #email").val(),
        'post_id' : $("section.sub-to-read-cta #postid").val(),
    };
    jQuery.post(ajaxurl, data, function(response) {
        //console.log(response)
        $("section.sub-to-read-cta").fadeOut(300);
        document.cookie = "str_is_yet_registered=true";
        window.location.reload(false);
    });
    $("#str_submit").attr("value", "Invia");

    return false;
}

