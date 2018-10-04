module.exports = function (grunt) {
	require("load-grunt-tasks")(grunt);


	function rename(dest, src) {
		const countKey = "production." + dest;
		let count = grunt.config.get(countKey);
		if (!count) {
			count = Date.now();
		}
		grunt.config.set(countKey, ++count);

		return dest + count + "_" + src;
	}


	grunt.initConfig({
		pkg: grunt.file.readJSON("package.json"),
		banner: "/* <%= pkg.author.name %>, <%= pkg.author.email %> - <%= pkg.name %> -" +
		" <%= grunt.template.today('yyyy-mm-dd') %> */",

		bowerDir: grunt.file.readJSON(".bowerrc").directory,
		jsDir: "www/js",
		stylesDir: "www/styles",
		fontsDir: "<%= stylesDir %>/fonts",

		files: {
			js: [
				"<%= bowerDir %>/jquery/dist/jquery.slim.js",
				"<%= bowerDir %>/popper.js/dist/umd/popper.js",
				"<%= bowerDir %>/bootstrap/dist/js/bootstrap.js",
				"<%= bowerDir %>/nette-live-form-validation/live-form-validation.js",
				"<%= bowerDir %>/nette.ajax.js/nette.ajax.js",
				"<%= bowerDir %>/happy/dist/happy.js",
				"<%= bowerDir %>/bootstrap-datepicker/dist/js/bootstrap-datepicker.js",
				"<%= bowerDir %>/jquery-ui-sortable/jquery-ui-sortable.js",
				"<%= bowerDir %>/ublaboo-datagrid/assets/dist/datagrid.js",
				"<%= bowerDir %>/ublaboo-datagrid/assets/dist/datagrid-instant-url-refresh.js",
				"<%= bowerDir %>/ublaboo-datagrid/assets/dist/datagrid-spinners.js",
				"<%= jsDir %>/custom/main.js"
			],
			css: [
				"<%= bowerDir %>/bootstrap/dist/css/bootstrap.css",
				"<%= bowerDir %>/font-awesome/css/font-awesome.css",
				"<%= bowerDir %>/happy/dist/happy.css",
				"<%= bowerDir %>/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css",
				"<%= bowerDir %>/ublaboo-datagrid/assets/dist/datagrid.css",
				"<%= bowerDir %>/ublaboo-datagrid/assets/dist/datagrid-spinners.css",
				"<%= stylesDir %>/custom/main.css"
			],
			fonts: [
				"<%= bowerDir %>/font-awesome/fonts/*"
			]
		},

		clean: {
			js: [
				"<%= jsDir %>/development",
				"<%= jsDir %>/production"
			],
			css: [
				"<%= stylesDir %>/development",
				"<%= stylesDir %>/production"
			],
			fonts: [
				"<%= fontsDir %>"
			],
			jsDevelopment: [
				"<%= jsDir %>/development"
			],
			cssDevelopment: [
				"<%= stylesDir %>/development"
			]
		},

		copy: {
			js: {
				files: [
					{
						src: "<%= files.js %>",
						dest: "<%= jsDir %>/development/",
						expand: true,
						flatten: true,
						rename: rename
					}
				]
			},
			css: {
				files: [
					{
						src: "<%= files.css %>",
						dest: "<%= stylesDir %>/development/",
						expand: true,
						flatten: true,
						rename: rename
					}
				]
			},
			fonts: {
				files: [
					{src: "<%= files.fonts %>", dest: "<%= fontsDir %>", expand: true, flatten: true}
				]
			}
		},

		uglify: {
			options: {
				banner: "<%= banner %>\n",
				footer: "\n<%= banner %>\n",
				output: {
					comments: false
				}
			},
			default: {
				files: {
					"<%= jsDir %>/production/production_<%= grunt.template.today('lssMlH') %>.js": [
						"<%= jsDir %>/development/*.js"
					]
				}
			}
		},

		cssmin: {
			options: {
				keepSpecialComments: 0
			},
			default: {
				files: {
					"<%= stylesDir %>/production/production_<%= grunt.template.today('lssMlH') %>.css": [
						"<%= stylesDir %>/development/*.css"
					]
				}
			}
		}
	});


	grunt.registerTask("styles-development", ["clean:css", "clean:fonts", "copy:css", "copy:fonts"]);
	grunt.registerTask("styles-production", [
		"clean:css", "clean:fonts", "copy:css", "copy:fonts", "cssmin:default", "clean:cssDevelopment"
	]);

	grunt.registerTask("scripts-development", ["clean:js", "copy:js"]);
	grunt.registerTask("scripts-production", ["clean:js", "copy:js", "uglify:default", "clean:jsDevelopment"]);

	grunt.registerTask("development", ["styles-development", "scripts-development"]);
	grunt.registerTask("production", ["styles-production", "scripts-production"]);

	grunt.registerTask("default", ["development"]);
};
