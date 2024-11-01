var Widiz = {
    startUp: function(){
        this.initForms();
    },
    initForms: function(){
        jQuery('.widiz-form').on('submit',  function(event) {
            var formdata = new FormData(this);
            formdata.append('action', 'widiz_form_submit');

            var $form = jQuery(this);
            $form.find('.widiz-submit-wrapper input').hide();
            Widiz.getFormFields($form).attr('disabled', 'disabled');
            Widiz.appendLoader($form.find('.widiz-submit-wrapper'))

            jQuery.ajax({
                url: widizConfig.ajaxurl,
                type: 'POST',
                data: formdata,
                contentType:false,
                processData:false,
                dataType: 'json',
                success: function(data){
                    $form.find('.widiz-submit-wrapper input').show();
                    $form.find('.widiz_loader, .widiz-error').remove();
                    $form.find('p').removeClass('error');
                    Widiz.getFormFields($form).removeAttr('disabled');

                    if(!data.success){
                        if(data.data.errors){
                            jQuery.each(data.data.errors, function(index, error) {
                                var $field = $form.find('[data-field="'+error.field+'"]');
                                $field.closest('p').addClass('error').find('span').remove();
                                $field.closest('p').append('<span class="widiz-error">'+error.message+'</span>');
                            });
                            Widiz.appendWarning($form.find('.widiz-response'), data.data.message);
                        }
                        else{
                            Widiz.appendError($form.find('.widiz-response'), data.data.message);
                        }
                    }
                    else{
                        Widiz.getFormFields($form).val('');
                        Widiz.appendSuccess($form.find('.widiz-response'), data.data.message);
                    }
                },
                error: function(data){
                    $form.find('.widiz-submit-wrapper input').show();
                    Widiz.getFormFields($form).removeAttr('disabled');
                    $form.find('.widiz_loader').remove();
                    Widiz.appendError($form.find('.widiz-response'), 'Internal server error. Please try again.');
                }
            });

            return false;
        });
    },
    getFormFields: function($form){
        return $form.find('input[type="text"], input[type="email"], select, textarea');
    },
    appendLoader: function($el){
        $el.append('<div class="widiz_loader"><span class="widiz_loader_1"></span><span class="widiz_loader_2"></span><span class="widiz_loader_3"></span></div>');
    },
    appendSuccess: function($el, message){
        $el.find('.widiz-alert').remove();
        $el.append('<div class="widiz-alert widiz-alert-ok">'+message+'</div>');
    },
    appendWarning: function($el, message){
        $el.find('.widiz-alert').remove();
        $el.append('<div class="widiz-alert widiz-alert-errors">'+message+'</div>');
    },
    appendError: function($el, message){
        $el.find('.widiz-alert').remove();
        $el.append('<div class="widiz-alert widiz-alert-ng">'+message+'</div>');
    }
}

jQuery(document).ready(function($) {
    Widiz.startUp();
});
