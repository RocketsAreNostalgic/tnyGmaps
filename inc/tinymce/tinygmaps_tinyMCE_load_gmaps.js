(function() {
   tinymce.create('tinymce.plugins.TINYGMAPS', {
      init : function(ed, url) {
         // Register commands
         ed.addCommand('TINYGMAPS', function() {
            ed.windowManager.open({
               //file : url + '/modal/tinygmaps_modal.php', // modal window//
               file : ajaxurl + '?action=tinygmaps_modal', // modal window
               width : 520 + parseInt(ed.getLang('button.delta_width', 0)), // size of our window
               height : 620 + parseInt(ed.getLang('button.delta_height', 0)),
               inline : 1
            }, {
               plugin_url : url
            });
         });
         // Register buttons
         ed.addButton('TINYGMAPS', {
            title : 'Insert Google Map',
            image : url+'/icons/music_folk_map.png',
            cmd : 'TINYGMAPS'
         });
      },
      getInfo : function() {
         return {
            longname : 'TINYGMAPS',
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
   tinymce.PluginManager.add('TINYGMAPS', tinymce.plugins.TINYGMAPS);
})();