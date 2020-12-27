(function($){
    'use strict';

    /* hook every widget **/
    function attach_widget()
    {
        $.each(jnews_elementor.widgets, function(index, widget)
        {
            var action = 'panel/widgets/wp-widget-' + widget + '/controls/wp_widget/loaded';
            elementor.hooks.addAction( action , function( panel ) {
                do_accordion(panel);
                window.widget_script(panel.$el);
            });
        });
    }

    function instagram_feed_element()
    {
        elementor.hooks.addAction( 'panel/open_editor/widget/wp-widget-jnews_instagram' , function( panel )
        {
            instagram_token(panel);
        });

        elementor.hooks.addAction( 'panel/open_editor/widget/jnews_footer_instagram_elementor' , function( panel )
        {
            instagram_token(panel);
        });
    }

    function instagram_token(panel)
    {
        $(panel.$el).on('click', '.jnews_instagram_access_token', function(e)
        {
            e.preventDefault();

            var element    = $(this),
                container  = element.parents('#elementor-controls'),
                redirect   = element.attr('href'),
                app_id     = container.find('.type-text[data-field="clientid"] input');

            if ( app_id.length > 0 )
            {
                app_id = app_id.val();
            } else {
                app_id = container.find('input[data-setting="clientid"]').val();
            }

            var app_url = 'https://api.instagram.com/oauth/authorize/?client_id=' + app_id + '&redirect_uri=' + redirect + '&response_type=token';


            var win = window.open(app_url, '_blank');
            win.focus();
        });
    }

    function socialcounter_element()
    {
        elementor.hooks.addAction( 'panel/open_editor/widget/socialcounter' , function( panel )
        {
            facebook_token(panel);
        });
    }

    function facebook_token(panel)
    {
        $(panel.$el).on('click', '.jnews_token_access.facebook', function(e)
        {
            e.preventDefault();

            var $this   = $(this),
                control = $this.parents('#elementor-controls'),
                app_url = 'https://graph.facebook.com/oauth/access_token',
                access_token,
                parameter = {
                    'client_id'     : control.find('.elementor-control-fb_id input').val(),
                    'client_secret' : control.find('.elementor-control-fb_secret input').val(),
                    'grant_type'    : 'client_credentials'
                };

            $.ajax({
                url         : app_url,
                data        : parameter,
                dataType    : 'json',
                type        : 'POST',
                beforeSend  : function( jqXHR )
                {
                    $this.parent().find('.jnews-spinner').addClass('active');
                }
            }).done(function( data, textStatus, jqXHR )
            {
                access_token = data.access_token;

                control.find('.elementor-control-fb_key input').val(access_token);

            }).fail(function( jqXHR, textStatus, errorThrown )
            {
                window.alert( 'Info Message: ' + errorThrown );
            }).always(function( data, textStatus, jqXHR )
            {
                $this.parent().find('.jnews-spinner').removeClass('active');
            });
        });
    }

    function do_accordion(panel)
    {
        var events = $._data( $(panel.$el).find('.jeg_accordion_heading'), 'events' ).click;
        var hasEvents = (events != null);
        if( !hasEvents ){
            $(panel.$el).find('.jeg_accordion_heading').on('click' , function(e){
                e.preventDefault();
                var parent = $(this).parent('.jeg_accordion_wrapper');
                var body =  $(parent).children('.jeg_accordion_body');

                if($(parent).hasClass('open'))
                {
                    $(body).slideUp('fast');
                    $(parent).removeClass('open').addClass('close');
                } else {
                    $(body).slideDown('fast');
                    $(parent).removeClass('close').addClass('open');
                }
            });
        }
    }

    function ajaxLoad(query, callback) {
        var control = this;
        if (!query.length || query.length < 3) return callback();

        var request = wp.ajax.send(control.ajax, {
            data: {
                query: query,
                nonce: control.nonce
            }
        });

        request.done(function (response) {
            callback(response);
        });
    }

    function fetchOption(ajax, value, nonce){
        return wp.ajax.send(ajax, {
            data: {
                value: value,
                nonce: nonce
            }
        });
    }

    function singleSelect( element ) {
        var ajax = $(element).data('ajax'),
            nonce = $(element).data('nonce'),
            setting = {
                allowEmptyOption: true
            };

        if ('' !== ajax) {
            setting.load = ajaxLoad.bind({ajax: ajax, nonce: nonce});
            setting.create = true;
        }

        $(element).selectize(setting);
    }

    /**
     * Check if valid option passed
     *
     * @param options
     */
    function isValidOption (options){
        if(undefined !== options[0]) {
            if(undefined !== options[0]['value'] && undefined !== options[0]['text']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Setup select option for Selectize
     *
     * @param options
     * @returns {Array}
     */
    function setupOption(options) {
        if(isValidOption(options)) {
            return options;
        } else {
            var newOption = [];
            _.each(options, function(text, value){
                newOption.push({
                    'value': value,
                    'text': text
                });
            });
            return newOption;
        }
    };

    function renderMultiSelect(element, options) {
        options = setupOption(options);
        var multiple = $(element).data('multiple'),
            ajax = $(element).data('ajax'),
            nonce = $(element).data('nonce'),
            setting = {
                plugins: ['drag_drop', 'remove_button'],
                multiple: multiple,
                hideSelected: true,
                options: options,
                render: {
                    option: function (item) {
                        return '<div><span>' + item.text + '</span></div>';
                    }
                }
            };

        if ('' !== ajax) {
            setting.load = ajaxLoad.bind({ajax: ajax, nonce: nonce});
            setting.create = true;
            setting.onItemAdd = function()
            {
                if (ajax==='jeg_find_review')
                {
                    var value = this.items;

                    if (value.length > 1)
                    {
                        for ( var a = 0; a < value.length; a++ )
                        {
                            this.removeItem(value[a]);
                            this.refreshOptions();
                        }
                    }
                }
            }
        }

        $(element).selectize(setting);
    }

    function multiSelect( element ) {
        var value = $(element).val(),
            parent = $(element).parent(),
            options = $(parent).find('.data-option').text(),
            retriever = $(element).data('retriever'),
            nonce = $(element).data('nonce');

        options = JSON.parse(options);

        if('' !== value && options.length === 0) {
            var fetch = fetchOption(retriever, value, nonce);
            fetch.done(function(response){
                renderMultiSelect(element, response);
            });
        } else {
            renderMultiSelect(element, options);
        }
    }

    function selectField(element) {
        var tag = $(element).prop('tagName');

        if(tag === 'SELECT') {
            singleSelect( element );
        } else {
            multiSelect( element );
        }
    }

    window.open_control = function(control)
    {
        var wrapper = control.parent();

        if ( wrapper.hasClass('type-select') )
        {
            selectField(control);
        }

        wrapper.find('input.input-sortable').on('change', function()
        {
            $(this).trigger('input');
        });
    };

    function enable_droppable_sticky()
    {
        elementor.hooks.addFilter( 'elements/column/render/droppable-item-selector' , function( selector ) {
            return "> .jegStickyHolder > .theiaStickySidebar > .elementor-column-wrap > .elementor-widget-wrap > .elementor-element, > .jegStickyHolder > .theiaStickySidebar >.elementor-column-wrap > .elementor-widget-wrap > .elementor-empty-view > .elementor-first-add, " + selector;
        });
    }

    function get_column_width(width)
    {
        var column = 12;

        if(width < 34) {
            column = 4;
        } else if(width < 67) {
            column = 8;
        } else {
            column = 12;
        }

        return column;
    }

    function calculate_column_width()
    {
        var column = 12;

        elementor.channels.data.on( 'column:before:drop', function(event)
        {
            var width = $(event.delegateTarget).data('col');
            column = get_column_width(width);
        });

        $.ajaxPrefilter(function(options, originalOptions, jqXHR)
        {
            if(originalOptions.data.action === 'elementor_ajax')
            {
                var option  = JSON.parse(originalOptions.data.actions),
                    data    = Object.values(option)[0];

                if ( data.action === 'render_widget' )
                {
                    var element = $(elementor.$previewContents.get(0).activeElement).find("[data-id='" + data.data.data.id + "']");

                    if(element.length > 0)
                    {
                        var width = $(element).parents('.elementor-column').data('col');
                        column = get_column_width(width);
                    }

                    options.data += "&colwidth=" + column;
                }
            }

            column = 12;
        });

    }

    function open_control_handler()
    {
        elementor.hooks.addAction( 'panel/open_editor/widget' , function( panel )
        {
            var control = $(panel.$el).find('.elementor-control-input-wrapper > input');

            control.each(function()
            {
                window.open_control($(this));
            });
        });
    }

    function do_ready()
    {
        attach_widget();
        open_control_handler();
        socialcounter_element();
        instagram_feed_element();
        enable_droppable_sticky();
        calculate_column_width();
    }

    $(document).ready(do_ready);

})(jQuery);
