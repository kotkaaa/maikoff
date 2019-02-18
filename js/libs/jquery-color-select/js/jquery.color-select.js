/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function ($) {
    $.fn.colorSelect = function (method) {
        // Methods
        if (typeof method == 'string') {
            if (method == 'update') {
                this.each(function () {
                    var $select = $(this),
                        $dropdown = $(this).next('.color-select'),
                        open = $dropdown.hasClass('open');
                    if ($dropdown.length) {
                        $dropdown.remove();
                        create_color_select($select);
                        if (open) {
                            $select.next().trigger('click');
                        }
                    }
                });
            } else if (method == 'destroy') {
                this.each(function () {
                    var $select = $(this),
                        $dropdown = $(this).next('.color-select');
                    if ($dropdown.length) {
                        $dropdown.remove();
                        $select.css('display', '');
                    }
                });
                if ($('.color-select').length == 0) {
                    $(document).off('.color_select');
                }
            } else {
                console.log('Method "' + method + '" does not exist.')
            } return this;
        }
        // Hide native select
        this.hide();
        // Create custom markup
        this.each(function () {
            var $select = $(this);
            if (!$select.next().hasClass('color-select')) {
                create_color_select($select);
            }
        });
        function create_color_select($select) {
            $select.after($('<div></div>')
                .addClass('color-select')
                .addClass($select.attr('class') || '')
                .addClass($select.attr('disabled') ? 'disabled' : '')
                .attr('tabindex', $select.attr('disabled') ? null : '0')
                .html('<span class="current"></span><ul class="list"></ul>')
            );
            var $dropdown = $select.next(),
                $options = $select.find('option'),
                $selected = $select.find('option:selected'),
                color = $selected.data('color')||false,
                text  = $selected.data('display') || $selected.text();
            $dropdown.find('.current').html((color ? "<span class=\"color\" style=\"background-color:" + color + "\"></span>" : "") + ("<span class=\"text\">" + text + "</span>"));
            $options.each(function (i) {
                var $option = $(this),
                    display = $option.data('display'),
                    color   = $option.data('color')||false;
                $dropdown.find('ul').append($('<li></li>')
                    .attr('data-value', $option.val())
                    .attr('data-display', (display || null))
                    .attr('data-color', (color || null))
                    .addClass('option' +
                        ($option.is(':selected') ? ' selected' : '') +
                        ($option.is(':disabled') ? ' disabled' : ''))
                    .html((color ? "<span class=\"color\" style=\"background-color:" + color + "\"></span>" : "") + ("<span class=\"text\">" + $option.text() + "</span>"))
                );
            });
        }
        /* Event listeners */
        // Unbind existing events in case that the plugin has been initialized before
        $(document).off('.color_select');
        // Open/close
        $(document).on('click.color_select', '.color-select', function (event) {
            var $dropdown = $(this);
            $('.color-select').not($dropdown).removeClass('open');
            $dropdown.toggleClass('open');
            if ($dropdown.hasClass('open')) {
                $dropdown.find('.option');
                $dropdown.find('.focus').removeClass('focus');
                $dropdown.find('.selected').addClass('focus');
            } else {
                $dropdown.focus();
            }
        });
        // Close when clicking outside
        $(document).on('click.color_select', function (event) {
            if ($(event.target).closest('.color-select').length === 0) {
                $('.color-select').removeClass('open').find('.option');
            }
        });
        // Option click
        $(document).on('click.color_select', '.color-select .option:not(.disabled)', function (event) {
            var $option = $(this),
                $dropdown = $option.closest('.color-select');
            $dropdown.find('.selected').removeClass('selected');
            $option.addClass('selected');
            var text = $option.data('display') || $option.text(),
                color = $option.data('color') || false;
            $dropdown.find('.current').html((color ? "<span class=\"color\" style=\"background-color:" + color + "\"></span>" : "") + ("<span class=\"text\"></span>" + text + "</span>"));
            $dropdown.prev('select').val($option.data('value')).trigger('change');
        });
        // Keyboard events
        $(document).on('keydown.color_select', '.color-select', function (event) {
            var $dropdown = $(this),
                $focused_option = $($dropdown.find('.focus') || $dropdown.find('.list .option.selected'));
            // Space or Enter
            if (event.keyCode == 32 || event.keyCode == 13) {
                if ($dropdown.hasClass('open')) {
                    $focused_option.trigger('click');
                } else {
                    $dropdown.trigger('click');
                } return false;
                // Down
            } else if (event.keyCode == 40) {
                if (!$dropdown.hasClass('open')) {
                    $dropdown.trigger('click');
                } else {
                    var $next = $focused_option.nextAll('.option:not(.disabled)').first();
                    if ($next.length > 0) {
                        $dropdown.find('.focus').removeClass('focus');
                        $next.addClass('focus');
                    }
                } return false;
                // Up
            } else if (event.keyCode == 38) {
                if (!$dropdown.hasClass('open')) {
                    $dropdown.trigger('click');
                } else {
                    var $prev = $focused_option.prevAll('.option:not(.disabled)').first();
                    if ($prev.length > 0) {
                        $dropdown.find('.focus').removeClass('focus');
                        $prev.addClass('focus');
                    }
                } return false;
                // Esc
            } else if (event.keyCode == 27) {
                if ($dropdown.hasClass('open')) {
                    $dropdown.trigger('click');
                }
                // Tab
            } else if (event.keyCode == 9) {
                if ($dropdown.hasClass('open')) {
                    return false;
                }
            }
        });
        // Detect CSS pointer-events support, for IE <= 10. From Modernizr.
        var style = document.createElement('a').style;
        style.cssText = 'pointer-events:auto';
        if (style.pointerEvents !== 'auto') {
            $('html').addClass('no-csspointerevents');
        } return this;
    };
}(jQuery));