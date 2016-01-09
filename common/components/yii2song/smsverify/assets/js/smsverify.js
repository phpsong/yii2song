(function ($) {
    $.fn.smsVerify = function (options) {
        var $that = $(this);
        var $id = $that.attr('id');
        var $btnTrigger = $('#' + $id + '-trigger');


        $that.on('keyup', function(e) {
            if ($(this).val().length==6) {

            }
        });

        $btnTrigger.on('click', function(e) {
            var mobile = $(options['mobileField']).val();
            if (/1\d{10}/g.test(mobile)) {
                $.ajax({
                    url : 'sms-verify?mobile=' + mobile + '&' + new Date().getTime(),
                    type : 'POST',
                    dataType : 'json',
                    success : function (data) {
                        if (data.success) {
                            countDown();
                        }
                        alert(data.message);
                    }
                });
            }
        });

        var lastSeconds = 3;
        var countDownInterval = null;
        var countDown = function() {
            countDownInterval = setInterval(function() {
                lastSeconds--;
                $btnTrigger.attr('disabled', true).text(lastSeconds + '秒后重新获取');
                if (lastSeconds<1) {
                    clearInterval(countDownInterval);
                    lastSeconds = 3;
                    $btnTrigger.attr('disabled', false).text($btnTrigger.attr('title'));
                }
            }, 1000);
        }
    };
})(window.jQuery);