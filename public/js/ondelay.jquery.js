(function($){
    $.fn.onDelay = function(params, func){
        let defaults = {
            action: 'click',
            interval: 200
        };

        let settings = $.extend(defaults, params);

        $(this).each(function(){
            let timer;

            $(this).on(settings.action, function(e){
                clearInterval(timer);
                let target = $(this);

                timer = setTimeout(function(){
                    func.call(target, e);
                }, settings.interval);
            });

        });

        return this;
    }
})(jQuery);
