module( "Base API" );

asyncTest( "l'api dit bonjour", function () {
    expect( 2 );

    jQuery.ajax({
        url: 'rest.api/',
        type: 'GET',
        success: function (data, textStatus, jqXHR) {
            deepEqual( jqXHR.status, 200, '200 OK');
            ok( data.hasOwnProperty('message'), 'message : ' + data.message);
            start();
        }
    });
});

asyncTest( "l'api rejete les ressources non valide", function () {
    expect( 2 );

    jQuery.ajax({
        url:'rest.api/ressource-invalide',
        type: 'GET',
        error: function (jqXHR, textStatus, errorThrown) {
            data = JSON.parse(jqXHR.responseText);

            deepEqual( jqXHR.status, 404, '404 Not Found');
            ok( data.hasOwnProperty('erreur'), 'erreur : ' + data.erreur);
            start();
        }
    });
});

asyncTest( "l'api rejete les verbes non valide", function () {
    expect( 2 );

    jQuery.ajax({
        url: 'rest.api/lien/auteur/1/article/1',
        type: 'FAIL',
        error: function (jqXHR, textStatus, errorThrown) {
            data = JSON.parse(jqXHR.responseText);

            deepEqual( jqXHR.status, 405, '405 Method Not Allowed');
            ok( data.hasOwnProperty('erreur'), 'erreur : ' + data.erreur);
            start();
        }
    });
});
