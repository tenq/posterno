(function () {

	// Default Values
	var yes_no = [{
			text: 'Yes',
			value: 'yes'
		},
		{
			text: 'No',
			value: 'no'
		},
	];
	var no_yes = [{
			text: 'No',
			value: 'no'
		},
		{
			text: 'Yes',
			value: 'yes'
		},
	];
	var true_false = [{
			text: 'Yes',
			value: 'true'
		},
		{
			text: 'No',
			value: 'false'
		},
	];
	var false_true = [{
			text: 'No',
			value: 'false'
		},
		{
			text: 'Yes',
			value: 'true'
		},
	];

	tinymce.PluginManager.add('pno_shortcodes_mce_button', function (editor, url) {
		editor.addButton('pno_shortcodes_mce_button', {
			title: 'Posterno Shortcodes',
			type: 'menubutton',
			icon: 'icon pno-shortcodes-icon',
			menu: [

				/** Forms **/
				{
					text: 'Forms',
					menu: [

						{
							text: 'Login form',
							onclick: function () {
								editor.insertContent('[wpum_account]');
							}
						},

					]
				}, // End forms

			]
		});
	});
})();
