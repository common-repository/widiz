var WidizHelpers = {
    createList: function(data, keyVal, keyName, emptyVal, emptyKey, defaultValue) {
        outHtml = "";
        if (emptyVal) {
            option = '<option value="' + emptyKey + '">' + emptyVal + '</option>';
            outHtml += option;
        }
        jQuery.each(data, function(index, val) {
            if (typeof keyVal !== "function") {
                value = keyVal ? val[keyVal] : index;
                label = keyName ? val[keyName] : val;
            } else {
                vals = keyVal(val, index);
                value = vals[0];
                label = vals[1];
            }


            var selectedAttr = '';
            if(defaultValue){
                if(value == defaultValue){
                    selectedAttr = ' selected';
                }
            }

            option = '<option value="' + value + '"'+selectedAttr+'>' + label + '</option>';
            outHtml += option;
        });
        return outHtml;
    }
}

var WidizForm = {
    startUp: function(){
        var self = this;

        jQuery('[name="widiz_forms_mode"]').on('change', function(event) {
            self.changedMode();
        })

        this.changedMode();
    },
    getSelectedMode: function(){
        return jQuery('[name="widiz_forms_mode"]:checked').val();
    },
    changedMode: function(){
        var mode = this.getSelectedMode();

        this.hideAllModesSections();

        if(this.modes[mode]){
            this.modes[mode].start();
        }
    },
    hideAllModesSections: function(){
        jQuery.each(this.modes, function(mode, modeMethods){
            modeMethods.allSections().hide();
        })
    },
    modes: {
        custom: {
            start: function(){
                var self = this;

                jQuery('#widiz_forms_custom_list_id').unbind('change').on('change', function(event) {
                    self.changedList();
                });

                this.listSection().show();
                this.changedList();
            },
            allSections: function(){
                return jQuery('#widiz_forms_custom_form_fields-repeatable, #widiz_forms_custom_list_id').closest('tr');
            },
            listSection: function(){
                return jQuery('#widiz_forms_custom_list_id').closest('tr');
            },
            fieldsSection: function(){
                return jQuery('#widiz_forms_custom_form_fields-repeatable').closest('tr');
            },
            changedList: function(){
                var self = this;
                var listId = jQuery('#widiz_forms_custom_list_id').val();

                if(!listId){
                    return this.fieldsSection().hide();
                }

                this.fieldsSection().show();

                jQuery.post(ajaxurl, {
                    action: 'widiz_fields',
                    list_id: listId
                }, function(response, textStatus, xhr) {
                    if(!response.success){
                        return;
                    }
                    var data = response.data;

                    self.fillListFields(data.list);
                });
            },
            fillListFields: function(fields){
                var fieldsLength = jQuery('#widiz_forms_custom_form_fields-repeatable tbody tr').length;
                for (var i = 0; i < fieldsLength; i++) {
                    var $select = jQuery('#widiz_forms_custom_form_fields_' + i + '_widiz_field_id');
                    $select.html(WidizHelpers.createList(fields, 'id', 'label', 'Select List field', ''));
                    var val = $select.data('value');
                    if(val){
                        $select.val(val);
                    }
                    $select.removeData('value');
                }
            }
        },
        cf7: {
            start: function(){
                var self = this;

                jQuery('#widiz_forms_cf7_form_id').unbind('change').on('change', function(event) {
                    self.changedFormId();
                });

                jQuery('#widiz_forms_cf7_list_id').unbind('change').on('change', function(event) {
                    self.changedList();
                });

                this.formSection().show();
                this.changedFormId();
            },
            allSections: function(){
                return jQuery('#widiz_forms_cf7_form_id, #widiz_forms_cf7_linked_fields-repeatable, #widiz_forms_cf7_list_id').closest('tr');
            },
            formSection: function(){
                return jQuery('#widiz_forms_cf7_form_id').closest('tr');
            },
            listSection: function(){
                return jQuery('#widiz_forms_cf7_list_id').closest('tr');
            },
            linkedFields: function(){
                return jQuery('#widiz_forms_cf7_linked_fields-repeatable').closest('tr');
            },
            changedFormId: function(){
                var self = this;
                var formId = jQuery('#widiz_forms_cf7_form_id').val();

                if(!formId){
                    this.listSection().hide();
                    this.linkedFields().hide();
                    return;
                }

                this.listSection().show();
                this.changedList();
            },
            changedList: function(){
                var self = this;
                var listId = jQuery('#widiz_forms_cf7_list_id').val();
                var formId = jQuery('#widiz_forms_cf7_form_id').val();

                if(!listId || !formId){
                    return this.linkedFields().hide();
                }

                this.linkedFields().show();

                jQuery.post(ajaxurl, {
                    action: 'widiz_fields',
                    form_id: formId,
                    list_id: listId
                }, function(response, textStatus, xhr) {
                    if(!response.success){
                        return;
                    }
                    var data = response.data;

                    self.fillFormFields(data.cf7);
                    self.fillListFields(data.list);
                });
            },
            fillFormFields: function(fields){
                var fieldsLength = jQuery('#widiz_forms_cf7_linked_fields-repeatable tbody tr').length;
                for (var i = 0; i < fieldsLength; i++) {
                    var $select = jQuery('#widiz_forms_cf7_linked_fields_'+i+'_cf7_field');
                    $select.html(WidizHelpers.createList(fields, 'id', 'label', 'Select CF7 form field', ''));
                    var val = $select.data('value');
                    if(val){
                        $select.val(val);
                    }
                    $select.removeData('value');
                }
            },
            fillListFields: function(fields){
                var fieldsLength = jQuery('#widiz_forms_cf7_linked_fields-repeatable tbody tr').length;
                for (var i = 0; i < fieldsLength; i++) {
                    var $select = jQuery('#widiz_forms_cf7_linked_fields_'+i+'_widiz_field_id');
                    $select.html(WidizHelpers.createList(fields, 'id', 'label', 'Select List field', ''));
                    var val = $select.data('value');
                    if(val){
                        $select.val(val);
                    }
                    $select.removeData('value');
                }
            }
        }
    }
}

jQuery(document).ready(function($) {
    WidizForm.startUp();
});
