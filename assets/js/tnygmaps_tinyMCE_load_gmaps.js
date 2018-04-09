(function () {
    tinymce.create('tinymce.plugins.TNYGMAPS', {
        init: function (ed, url) {
            // Register commands
            ed.addCommand('TNYGMAPS', function () {
                 ed.windowManager.open({
                     url: ajaxurl + '?action=tnygmaps_modal', // modal window
                     width: 520 + parseInt(ed.getLang('button.delta_width', 0)), // size of our window
                     height: 620 + parseInt(ed.getLang('button.delta_height', 0)),
                     inline: 1
                 }, {
                     plugin_url: url
                 });
            });
            // Register buttons
            ed.addButton('TNYGMAPS', {
                title: 'Insert Google Map',
                image: url + '/../img/app_icon.png',
                cmd: 'TNYGMAPS'
            });
        },
        getInfo: function () {
            return {
                longname: 'TNYGMAPS',
                author: 'Ben Rush',
                authorurl: 'http://www.orionrush.com',
                infourl: 'http://www.orionrush.com',
                version: '1.0',
                version: tinymce.majorVersion + '.' + tinymce.minorVersion
            };
        }
    });
    // Register plugin
    // first parameter is the button ID and must match ID elsewhere
    // second parameter must match the first parameter of the tinymce.create() function above
    tinymce.PluginManager.add('TNYGMAPS', tinymce.plugins.TNYGMAPS);
})();