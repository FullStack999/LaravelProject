try {
    require('./plugins/sb-admin');
    require('./plugins/sb-admin-charts');
} catch (e) {}


$('#logout').off('click').on('click',function(){
    sessionStorage.clear();
});


$('a[href="#navbar-more-show"], .navbar-more-overlay').on('click', function(event){
	event.preventDefault();
	$('body #app').toggleClass('navbar-more-show');
	if ($('body #app').hasClass('navbar-more-show')){
		$('a[href="#navbar-more-show"]').closest('li').addClass('active');
	}else{
		$('a[href="#navbar-more-show"]').closest('li').removeClass('active');
	}
	return false;
});

$(document).on('keydown', 'input.num_input', function(e){
    if (e.key.match(/[^0-9.]/) &&  e.key.length == 1 ) {
        return false
    }
});


$('#full_screen').off('click').on('click', function () {
    if (!document.fullscreenElement && !document.mozFullScreenElement && !document.webkitFullscreenElement) {
        if (document.documentElement.requestFullscreen) {
            document.documentElement.requestFullscreen();
        }
        else if (document.documentElement.mozRequestFullScreen) {
            document.documentElement.mozRequestFullScreen();
        }
        else if (document.documentElement.webkitRequestFullscreen) {
            document.documentElement.webkitRequestFullscreen();
        }
    }
    else {
        if (document.cancelFullScreen) {
            document.cancelFullScreen();
        }
        else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        }
        else if (document.webkitCancelFullScreen) {
            document.webkitCancelFullScreen();
        }
    }
});



let notify = $('.notify');

if (notify.length != 0){
    notification();
    let interval = setInterval(notification, 3000);
    function notification() {
        $.get('user5/notifications', function (response) {
            if (response.status == 'field'){
                clearInterval(interval)
            }
            else{
                $('.notify').html(response.view);
                $('.notify').closest('li').find('.notify_count').text(response.notify_count);
                $('.notify_item').off('click').on('click', function () {
                    $('#detail_orders_table').grid().reload({
                        notify_id: $(this).data('id'),
                    })
                })
            }
        }).fail(function () {
            clearInterval(interval)
        });
    }
}
$('#navbarResponsive').on('click', 'li',  function () {
    $(this).closest('#navbarResponsive').collapse('hide')
});

