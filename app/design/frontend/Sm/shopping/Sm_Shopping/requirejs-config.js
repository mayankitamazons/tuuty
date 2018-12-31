var config = {
    map: {
        '*': {
            jquerypopper: "Sm_Shopping/js/bootstrap/popper",
            jquerybootstrap: "Sm_Shopping/js/bootstrap/bootstrap.min",
            owlcarousel: "Sm_Shopping/js/owl.carousel",
            jqueryfancyboxpack: "Sm_Shopping/js/jquery.fancybox.pack",
            jqueryunveil: "Sm_Shopping/js/jquery.unveil",
            yttheme: "Sm_Shopping/js/yttheme"
        }
    },
    shim: {
        'jquerypopper': {
            'deps': ['jquery'],
            'exports': 'Popper'
        },
        'jquerybootstrap': {
            'deps': ['jquery', 'jquerypopper']
        }
    }
};