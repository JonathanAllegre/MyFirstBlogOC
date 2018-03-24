
$('#submitContact').click(function() {

    var name = $("input#name").val();
    var email = $("input#email").val();
    var message = $("textarea#message").val();
    var adresse = $("input#adresse").val();
    var prefix = $("input#prefix").val();
    var firstName = name; // For Success/Failure Message

    jQuery.ajax({
        url: prefix + "/contact/sendMailContact",
        dataType: "json",
        type: "POST",
        data: {
            name: name,
            email: email,
            message: message,
            adresse: adresse
        },
        cache: false,
        success: function (data) {
            // Success message
            if (data.error == 0) {

                $('#success').html("<div class='alert alert-success'>");
                $('#success > .alert-success').html("<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;")
                    .append("</button>");
                $('#success > .alert-success')
                    .append("<strong>" + data.retour + "</strong>");
                $('#success > .alert-success')
                    .append('</div>');

                //clear all fields
                $('#contactForm').trigger("reset");
            } else {

                // Fail Verif PHP
                $('#success').html("<div class='alert alert-danger'>");
                $('#success > .alert-danger').html("<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;")
                    .append("</button>");
                $('#success > .alert-danger').append("<strong>" + data.errorTitle);
                $('#success > .alert-danger').append('</div>');

            }
        },
        error: function (data) {
            // Fail message
            $('#success').html("<div class='alert alert-danger'>");
            $('#success > .alert-danger').html("<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;")
                .append("</button>");
            $('#success > .alert-danger').append("<strong>Désolé, une erreur est survenue !" + data);
            $('#success > .alert-danger').append('</div>');
            //clear all fields
            $('#contactForm').trigger("reset");
        },
    })
});
