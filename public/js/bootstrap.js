

require.config({
    baseUrl: '/js/mylibs',
});

$.ajaxSetup ({  cache: false  });  

require(["main"],function(MAIN){
    $(document).ready(function(){
        $main = new MAIN();
    });
});

