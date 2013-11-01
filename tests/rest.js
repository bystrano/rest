module( "Base API" );

asyncTest( "l'api dit bonjour", function () {
    expect( 2 );

    jQuery.ajax({
        url: 'rest.api/',
        type: 'GET',
        timeout: 1000,
        success: function (data, textStatus, jqXHR) {
            deepEqual( jqXHR.status, 200, '200 OK');
            ok( data.hasOwnProperty('message'), 'message : ' + data.message);
            start();
        }
    });
});
