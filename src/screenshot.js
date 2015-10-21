// PhantomJS Screenshot Capture...
var fs = require('fs'),
    args = require('system').args,
    page = require('webpage').create();

page.viewportSize = { width: 1024, height: 768 };
page.clipRect = { top: 0, left: 0, width: 1024, height: 768 };

page.open(args[1], function() {
  	// Give the page 300ms to render any images...
	window.setTimeout(function() {
		page.render(args[2]);
		phantom.exit();
	}, 300);
});