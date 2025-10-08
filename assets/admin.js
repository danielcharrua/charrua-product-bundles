(function($){
  // ----- Utilidades -----
  function updateHiddenFromList() {
    var ids = [];
    $('#charrua_pb_addons_list .charrua-pb-item').each(function(){
      ids.push($(this).data('id'));
    });
    $('#charrua_pb_addons_field').val(ids.join(','));
  }

  function addItemOnce(id, label) {
    if ($('#charrua_pb_addons_list .charrua-pb-item[data-id="'+id+'"]').length) return; // evitar duplicados
    var $li = $('<li class="charrua-pb-item" data-id="'+id+'" style="display:flex;align-items:center;gap:.5rem;margin:.35rem 0;padding:.35rem .5rem;border:1px solid #ddd;border-radius:.35rem;cursor:move;">'
      + '<span class="dashicons dashicons-move" aria-hidden="true"></span>'
      + '<span class="charrua-pb-item-label" style="flex:1"></span>'
      + '<button type="button" class="button-link charrua-pb-remove" aria-label="Remove" style="color:#b32d2e;">&times;</button>'
      + '</li>');
    $li.find('.charrua-pb-item-label').text(label + ' [#' + id + ']');
    $('#charrua_pb_addons_list').append($li);
    updateHiddenFromList();
  }

  function initSelectWoo($el, action){
    if (!$el.length) return;
    if ($el.data('select2')) $el.select2('destroy');
    $el.selectWoo({
      placeholder: $el.data('placeholder') || '',
      minimumInputLength: 1,
      allowClear: true,
      width: '100%',
      ajax: {
        url: CHARRUA_PB_ADMIN.ajaxUrl,
        dataType: 'json',
        delay: 200,
        data: function (params) {
          return {
            action: action,
            q: params.term || '',
            page: params.page || 1,
            per: CHARRUA_PB_ADMIN.per || 30,
            nonce: CHARRUA_PB_ADMIN.nonce
          };
        },
        processResults: function (data) {
          return {
            results: (data && data.results) ? data.results : [],
            pagination: { more: !!(data && data.more) }
          };
        },
        cache: true
      }
    });
  }

  // ----- Eventos y bindings -----
  function bindAddonsSelect() {
    var $sel = $('#charrua_pb_addons_select');
    if (!$sel.length) return;

    initSelectWoo($sel, 'charrua_pb_search_products');

    $sel.on('select2:select', function (e) {
      var item = e.params.data; // {id, text}
      var label = (item.text || '').replace(/\s*\[#\d+\]\s*$/,'');
      addItemOnce(item.id, label);
      $(this).val(null).trigger('change');
    });

    $(document).on('click', '.charrua-pb-remove', function(){
      $(this).closest('.charrua-pb-item').remove();
      updateHiddenFromList();
    });

    // --- Cambio: sortable con actualización del hidden en cada evento relevante ---
    if (typeof $.fn.sortable === 'function') {
      $('#charrua_pb_addons_list').sortable({
        handle: '.dashicons-move',
        axis: 'y',
        containment: 'parent',
        tolerance: 'pointer',
        helper: 'clone',
        start: function(){ $(this).addClass('sorting'); },
        stop: function(){ $(this).removeClass('sorting'); updateHiddenFromList(); },
        update: function(){ updateHiddenFromList(); },
        change: function(){ updateHiddenFromList(); }
      });

      $(document).on('postbox-toggled', function(){
        var $list = $('#charrua_pb_addons_list');
        if ($list.data('ui-sortable')) {
          $list.sortable('refresh');
        }
      });
    }
  }

  function bindConditionsSelects() {
    initSelectWoo($('#charrua_pb_cond_cats'), 'charrua_pb_search_product_cats');
    initSelectWoo($('#charrua_pb_cond_products'), 'charrua_pb_search_products');
  }

  // ----- Ready -----
  $(function(){
    bindConditionsSelects();
    bindAddonsSelect();
    updateHiddenFromList();
  });
})(jQuery);
