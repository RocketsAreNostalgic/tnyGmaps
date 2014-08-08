(function() {
   tinymce.create('tinymce.plugins.TRMAPS', {
      init : function(ed, url) {
         // Register commands
         ed.addCommand('TRMAPS', function() {
            ed.windowManager.open({
               file : url + '/modal/tr_gmaps_modal.php', // modal window
               width : 520 + parseInt(ed.getLang('button.delta_width', 0)), // size of our window
               height : 600 + parseInt(ed.getLang('button.delta_height', 0)),
               inline : 1
            }, {
               plugin_url : url
            });
         });
         // Register buttons
         ed.addButton('TRMAPS', {
            title : 'Insert Google Map',
            image : url+'/icons/music_folk_map.png',
            cmd : 'TRMAPS'
         });
      },
      getInfo : function() {
         return {
            longname : 'TRMAPS',
            author : 'Ben Rush',
            authorurl : 'http://www.orionrush.com',
            infourl : 'http://www.orionrush.com',
            version : "1.0",
            version : tinymce.majorVersion + "." + tinymce.minorVersion
         };
      }
   });
   // Register plugin
   // first parameter is the button ID and must match ID elsewhere
   // second parameter must match the first parameter of the tinymce.create() function above
   tinymce.PluginManager.add('TRMAPS', tinymce.plugins.TRMAPS);
})();