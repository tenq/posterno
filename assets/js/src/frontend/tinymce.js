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
			title: pnotinymce.title,
			type: 'menubutton',
			icon: 'icon pno-shortcodes-icon',
			menu: [

				/** Forms **/
				{
					text: pnotinymce.forms.title,
					menu: [

						{
							text: pnotinymce.forms.login,
							onclick: function () {
								editor.insertContent('[pno_login_form]');
							}
						},
						{
							text: pnotinymce.forms.registration,
							onclick: function () {
								editor.insertContent('[pno_registration_form]');
							}
						},
						{
							text: pnotinymce.forms.password,
							onclick: function () {
								editor.insertContent('[pno_password_recovery_form]');
							}
						},
						{
							text: pnotinymce.forms.submission,
							onclick: function () {
								editor.insertContent('[pno_listing_submission_form]');
							}
						},
						{
							text: pnotinymce.forms.editing,
							onclick: function () {
								editor.insertContent('[pno_listing_editing_form]');
							}
						},

					]
				}, // End forms

				// Links.
				{
					text: pnotinymce.links.title,
					menu: [

						// Login link.
						{
							text: pnotinymce.links.login.title,
							onclick: function () {
								editor.windowManager.open({
									title: pnotinymce.links.login.title,
									body: [{
											type: 'textbox',
											name: 'redirect',
											label: pnotinymce.links.login.redirect,
											value: ''
										},
										{
											type: 'textbox',
											name: 'label',
											label: pnotinymce.links.login.label,
											value: 'Login'
										},
									],
									onsubmit: function (e) {
										editor.insertContent('[pno_login_link redirect="' + e.data.redirect + '" label="' + e.data.label + '" ]');
									}
								});
							}
						},

						// Logout link.
						{
							text: pnotinymce.links.logout.title,
							onclick: function () {
								editor.windowManager.open({
									title: pnotinymce.links.logout.title,
									body: [{
											type: 'textbox',
											name: 'redirect',
											label: pnotinymce.links.logout.redirect,
											value: ''
										},
										{
											type: 'textbox',
											name: 'label',
											label: pnotinymce.links.logout.label,
											value: 'Logout'
										},
									],
									onsubmit: function (e) {
										editor.insertContent('[pno_logout_link redirect="' + e.data.redirect + '" label="' + e.data.label + '" ]');
									}
								});
							}
						},

					]
				},
				// End links.

				// Pages.
				{
					text: pnotinymce.pages.title,
					menu: [

						{
							text: pnotinymce.pages.dashboard,
							onclick: function () {
								editor.insertContent('[pno_dashboard]');
							}
						},

					]
				},
				// End pages.

			]
		});
	});
})();