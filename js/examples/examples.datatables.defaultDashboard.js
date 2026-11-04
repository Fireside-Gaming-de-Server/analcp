
(function($) {

	'use strict';

	var datatableInit = function() {

		$('#datatable-default2').dataTable({
			dom: '<"row"<"col-lg-6"l><"col-lg-6"f>><"table-responsive"t>p'
        });
        
    };
    

	$(function() {
        datatableInit();
	});

}).apply(this, [jQuery]);

(function($) {

	'use strict';

	var datatableInit = function() {

		$('#datatable-default1').dataTable({
			dom: '<"row"<"col-lg-6"l><"col-lg-6"f>><"table-responsive"t>p'
        });
        
    };

	$(function() {
        datatableInit();
	});

}).apply(this, [jQuery]);



function newFunction() {
	(function ($) {
		'use strict';

		var datatableInit = function () {

			$('#datatable-default3').dataTable({
				dom: '<"row"<"col-lg-6"l><"col-lg-6"f>><"table-responsive"t>p'
			});

		};

		$(function () {
			datatableInit();
		});

	}).apply(this, [jQuery]);
}

