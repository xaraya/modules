
var q_click = function() {
    var toLoad = $(this).attr('href');
    var targetname = $(this).attr('name');

    $('#'+targetname).ajaxComplete(function(info) {
    
        $(function()
            {
                $("li a.quantum_option").click(q_click);
            }
        );
    });

    $("#"+targetname).load(toLoad);
    return false;
}


$(function()
    {
        $("li a.quantum_option").click(q_click);
    }
);
