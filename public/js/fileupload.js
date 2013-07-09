(function(global){
    Backbone.emulateHTTP = true;
    Backbone.emulateJSON = false;

    var $ = global.jQuery,
        Backbone = global.Backbone;

    var Memo = Backbone.Model.extend({
        url : '/Memo'
    });


    var MemoList = Backbone.Model.extend({
       model : Memo,



    });



    var AppView = Backbone.View.extend({
        el : '<tr><td></td></tr>',
        events : {
            'keypress .edit' : 'edit',
            'keypress .upload' : 'upload'
        },

        initialize : function(){
            this.listenTo(this.model, 'edit', this.edit);
            this.listenTo(this.model, 'upload', this.upload);
        },


        edit: function() {
            this.$el.html('<textarea></textarea><input type="button">');
            this.$el.addClass('activate');
            return this;
        },

        upload: function() {
            this.model.save({id : this.id
                            memo : this.memo}
            );
        }
    );







    $function(){
        new AppView();
    }

})(this)