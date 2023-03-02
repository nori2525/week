var $target = document.querySelector('.hid')
var $button = document.querySelector('.btn-form')
$button.addEventListener('click', function() {
  $target.classList.toggle('is-hidden')
})