$(document).ready(function () {
  $(".detail-product-abs").slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    autoplay: true,
    dots: true,
    autoplaySpeed: 4000,
    arrows: false,
    draggable: true,
  });

  $(".slider-for").slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    arrows: false,
    fade: true,
    asNavFor: ".slider-nav",
  });

  $(".slider-nav").slick({
    slidesToShow: 3,
    slidesToScroll: 1,
    asNavFor: ".slider-for",
    centerMode: true,
    focusOnSelect: true,
  });
});

function displaySelectedColor_ngoai(optionId) {
  var selectedOption = document.getElementById(optionId);
  if (selectedOption.checked) {
    $("#selected_ngoai_color").text(" - " + selectedOption.value);
  }
}
function displaySelectedColor_noi(optionId) {
  var selectedOption = document.getElementById(optionId);
  if (selectedOption.checked) {
    $("#selected_noi_color").text(" - " + selectedOption.value);
  }
}
