(function() {
    tinymce.create('tinymce.plugins.Wpfc', {
        wpfcNotHTML : '',
        url: '',
        init : function(ed, url) {
            var self = this;
            self.setUrl(url);
            self.setWpfcNotHTML();

            ed.addButton('wpfc', {
                title : 'Block caching for this page',
                cmd : 'wpfc',
                image : self.url + "/icon.png"
            });

            ed.addCommand('wpfc', function() {
                ed.execCommand('mceInsertContent', 0, self.wpfcNotHTML);
            });

            self._handleWpfcNOT(ed, url);
        },
        setUrl: function(url){
            this.url = url.replace("../js","../images");
        },
        setWpfcNotHTML: function(){
            this.wpfcNotHTML = '<img src="' + this.url + "/tinymce-wpfcnot.jpg" + '" class="mce-wp-wpfcnot" style="margin-right:95%;" />';
        },
        _handleWpfcNOT : function(ed, url) {
            var self = this;
            ed.onPostRender.add(function() {
                if (ed.theme.onResolveName) {
                    ed.theme.onResolveName.add(function(th, o) {
                        if (o.node.nodeName == 'IMG') {
                            if ( ed.dom.hasClass(o.node, 'mce-wp-wpfcnot') ){
                                o.name = 'wpfcnot';
                            }
                        }
                    });
                }
            });
            ed.onBeforeSetContent.add(function(ed, o) {
                if ( o.content ) {
                    o.content = o.content.replace(/<\!--\s*\[wpfcNOT\]\s*-->/, self.wpfcNotHTML);
                }
            });
            ed.onPostProcess.add(function(ed, o) {
                if (o.get){
                    o.content = o.content.replace(/<img[^>]+>/g, function(im) {
                        if (im.indexOf('class="mce-wp-wpfcnot') !== -1) {
                            im = '<!--[wpfcNOT]-->';
                        }
                        return im;
                    });
                }
            });
        }
    });
    tinymce.PluginManager.add( 'wpfc', tinymce.plugins.Wpfc );
})();