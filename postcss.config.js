module.exports = {
	plugins: [
		require("postcss-import"),
		require("postcss-easy-import")({
			prefix: "_",
		}),
		require('postcss-nested'),
		require("postcss-easy-import"),
		require("autoprefixer"),
		require("cssnano")({
			preset: "default",
		}),
	],
};
