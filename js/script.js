document.addEventListener('DOMContentLoaded', function () {
    var videoFacades = document.querySelectorAll('.video-facade');
    videoFacades.forEach(function (facade) {
        facade.addEventListener('click', function () {
            var iframe = document.createElement('iframe');
            iframe.setAttribute('src', facade.dataset.src);
            iframe.setAttribute('allowfullscreen', 'true');
            iframe.setAttribute('frameborder', '0');
            iframe.setAttribute('width', '500');
            iframe.setAttribute('height', '281');
            iframe.setAttribute('allow', 'accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture; web-share');
            facade.parentNode.replaceChild(iframe, facade);
        });
    });
});
