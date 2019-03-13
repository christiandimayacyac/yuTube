// alert("Hello from main.js");
$(document).ready(function() {
    
    //Evenlistener -> btnShowHide
    $(".btnShowHide").on("click", function() {
        let nav = $("#side-nav-container");
        let main = $("#main-section-container");

        if ( main.hasClass("left-padding") ) {
            nav.hide();
        }
        else {
            nav.show();
        }

        main.toggleClass("left-padding");
    });

    $("#inputFile").on("change", function() {

        console.log(this.files[0].size);
    });

    $("form").submit("click", ()=>{
        $(".modal").modal("show");
    });

});