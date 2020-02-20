jQuery(document).ready(function($){

  var $types = $('#mywp-debug .debug-types');
  var $renders = $('#mywp-debug .debug-renders');

  $types.find('.type-select').on('click', function() {

    var type = $(this).data('type');

    $types.find('.type-select').removeClass('active');

    $types.find('.type-' + type).addClass('active');

    $renders.find('.render').removeClass('active');

    $renders.find('.type-' + type).addClass('active');

  });

});
